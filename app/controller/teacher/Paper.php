<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\Paper as PaperModel;
use app\model\PaperQuestion;
use app\model\Question;
use app\model\QuestionType;
use app\model\Difficulty;
use app\model\Knowledge;
use app\model\Course;
use app\model\Chapter;
use app\model\QuestionOption;
use app\model\QuestionAnswer;
use app\model\ExamConfig;
use app\service\AiService;
use think\facade\View;
use think\facade\Session;
use think\facade\Db;
use think\Request;
use think\exception\ValidateException;

class Paper
{
    public function index()
    {
        $user = Session::get('user');
        
        $papers = PaperModel::where('creator_id', $user['id'])->order('create_time', 'desc')->select();
        
        View::assign([
            'user' => $user,
            'papers' => $papers,
        ]);
        
        return View::fetch('teacher/paper');
    }
    
    public function add(Request $request)
    {
        $user = Session::get('user');
        
        // POST 请求处理
        if ($request->isPost()) {
            try {
                // 防止长时间 AI 调用导致脚本超时
                set_time_limit(0);
                // 关闭错误显示，防止 PHP 警告混入 JSON 响应
                ini_set('display_errors', '0');
                
                $data = $request->post();
                
                // 验证必要字段
                if (empty($data['name'])) {
                    return json(['code' => 0, 'msg' => '试卷名称不能为空']);
                }
                
                if (empty($data['type']) || !in_array($data['type'], ['manual', 'auto'])) {
                    return json(['code' => 0, 'msg' => '组卷方式无效']);
                }
                
                // 自动组卷：先生成题目（不走事务，立即写入数据库，防止AI超时全部丢失）
                if ($data['type'] == 'auto') {
                    $aiService = app(AiService::class);
                    if (!$aiService->isConfigured()) {
                        return json(['code' => 0, 'msg' => 'AI 服务未配置，请检查 .env 文件']);
                    }
                    
                    $subject = $data['subject'] ?? '';
                    // 如果没传 subject，从课程名称获取
                    if (empty($subject) && !empty($data['course_id'])) {
                        $course = Course::find(intval($data['course_id']));
                        $subject = $course ? $course['name'] : '';
                    }
                    $typeIds = $this->getTypeIds($data);
                    
                    // 难度：表单传的是 difficulty_id（数字），需要转换为 code
                    $difficultyCode = 'easy';
                    if (!empty($data['difficulty_id']) && is_numeric($data['difficulty_id'])) {
                        $diff = Difficulty::find(intval($data['difficulty_id']));
                        $difficultyCode = $diff ? ($diff['code'] ?? 'easy') : 'easy';
                    }
                    
                    // 从 type_counts 计算题目总数（优先），兜底用 question_count
                    $typeCounts = $data['type_counts'] ?? [];
                    $questionCount = 0;
                    if (!empty($typeCounts)) {
                        foreach ($typeIds as $tid) {
                            $questionCount += isset($typeCounts[$tid]) ? intval($typeCounts[$tid]) : 0;
                        }
                    }
                    if ($questionCount <= 0) {
                        $questionCount = intval($data['question_count'] ?? 5);
                    }
                    
                    $questions = $this->generateAiQuestions($aiService, $subject, $typeIds, $difficultyCode, $questionCount, $data, $user);
                    
                    if (empty($questions)) {
                        return json(['code' => 0, 'msg' => 'AI 生成题目失败，请查看 runtime/ai_generate_log.txt 日志']);
                    }
                    
                    // 题目已生成并写入DB，现在创建试卷和关联
                    Db::startTrans();
                    try {
                        $paper = PaperModel::create([
                            'name' => $data['name'],
                            'description' => $data['description'] ?? '',
                            'course_id' => isset($data['course_id']) && $data['course_id'] !== '' ? intval($data['course_id']) : null,
                            'type' => $data['type'],
                            'total_score' => isset($data['total_score']) ? intval($data['total_score']) : 100,
                            'creator_id' => $user['id'],
                            'status' => 0,
                        ]);
                        
                        $paperId = $paper->id;
                        $sortIndex = 0;
                        foreach ($questions as $question) {
                            PaperQuestion::create([
                                'paper_id' => $paperId,
                                'question_id' => $question['id'],
                                'score' => $question['score'] ?? 5,
                                'sort' => ++$sortIndex,
                            ]);
                        }
                        
                        $this->updatePaperStats($paperId);
                        Db::commit();
                        
                        // 保存考试配置
                        $this->saveExamConfig($paperId, $data);
                        
                        $msg = '组卷成功，AI 生成了 ' . count($questions) . ' 道题目';
                        return json(['code' => 1, 'msg' => $msg, 'url' => '/teacher/papers']);
                        
                    } catch (\Throwable $e) {
                        Db::rollback();
                        try { $this->logError($e); } catch (\Throwable $logEx) {}
                        return json(['code' => 0, 'msg' => '服务器错误：' . $e->getMessage()]);
                    }
                }
                
                // 手动组卷
                Db::startTrans();
                
                // 创建试卷
                $paper = PaperModel::create([
                    'name' => $data['name'],
                    'description' => $data['description'] ?? '',
                    'course_id' => $data['course_id'] ?? null,
                    'type' => $data['type'],
                    'total_score' => $data['total_score'] ?? 100,
                    'creator_id' => $user['id'],
                    'status' => 0,
                ]);
                
                $paperId = $paper->id;
                
                $this->handleManualPaper($data, $paperId);
                
                file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " handleManualPaper done, now updatePaperStats\n", FILE_APPEND);
                
                // 更新试卷统计信息
                $this->updatePaperStats($paperId);
                
                file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " updatePaperStats done, now commit\n", FILE_APPEND);
                
                // 获取最终题目数量
                $paperQuestions = PaperQuestion::where('paper_id', $paperId)->select();
                $questionCount = count($paperQuestions);
                
                file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " questionCount=$questionCount, committing\n", FILE_APPEND);
                
                Db::commit();
                
                // 保存考试配置
                $this->saveExamConfig($paperId, $data);
                
                file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " committed, returning JSON\n", FILE_APPEND);
                
                if ($questionCount == 0) {
                    return json(['code' => 0, 'msg' => '组卷失败：未选择任何题目']);
                }
                
                return json(['code' => 1, 'msg' => '组卷成功', 'url' => '/teacher/papers']);
                
            } catch (\Throwable $e) {
                // 回滚事务（忽略回滚失败）
                try { Db::rollback(); } catch (\Throwable $rollbackEx) {}
                
                // 记录错误日志（忽略日志写入失败）
                try { $this->logError($e); } catch (\Throwable $logEx) {}
                
                // 始终返回 JSON，防止 ThinkPHP 输出 HTML 错误页
                return json(['code' => 0, 'msg' => '服务器错误：' . $e->getMessage()]);
            }
        }
        
        // GET 请求：显示表单
        $courses = Course::where('teacher_id', $user['id'])->select();
        $types = QuestionType::select();
        $difficulties = Difficulty::select();
        $knowledges = Knowledge::select();
        
        View::assign([
            'user' => $user,
            'courses' => $courses,
            'types' => $types,
            'difficulties' => $difficulties,
            'knowledges' => $knowledges,
        ]);
        
        return View::fetch('teacher/paper_add');
    }
    
    /**
     * 预览试卷
     */
    public function preview(Request $request)
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/login');
        }
        
        $paperId = $request->param('paper_id');
        if (!$paperId || !is_numeric($paperId)) {
            return '试卷ID无效';
        }
        
        $paper = PaperModel::find(intval($paperId));
        if (!$paper) {
            return '试卷不存在';
        }
        
        // 加载试卷题目
        $paperQuestions = PaperQuestion::where('paper_id', $paper['id'])->order('sort', 'asc')->select();
        $questions = [];
        foreach ($paperQuestions as $pq) {
            $question = Question::find($pq['question_id']);
            if ($question) {
                $question['score'] = $pq['score'];
                $question['type_name'] = QuestionType::where('id', $question['type_id'])->value('name');
                $question['difficulty_name'] = Difficulty::where('id', $question['difficulty_id'])->value('name');
                $question['options'] = QuestionOption::where('question_id', $question['id'])->order('id', 'asc')->select();
                // 获取答案：优先从 question_answer 表，兜底从选项 is_correct 推导
                $answers = QuestionAnswer::where('question_id', $question['id'])->order('blank_index', 'asc')->select();
                if (!empty($answers) && !empty($answers[0]['answer_content'])) {
                    // 填空题多空答案用分号拼接，如 "Shift；Esc"
                    $answerTexts = [];
                    foreach ($answers as $a) {
                        if (!empty($a['answer_content'])) {
                            $answerTexts[] = $a['answer_content'];
                        }
                    }
                    $question['answer_display'] = implode('；', $answerTexts);
                } else {
                    // 从选项推导正确答案
                    $correctOpts = [];
                    foreach ($question['options'] as $opt) {
                        if (!empty($opt['is_correct'])) {
                            $correctOpts[] = $opt['option_key'] . '. ' . $opt['option_content'];
                        }
                    }
                    $question['answer_display'] = !empty($correctOpts) ? implode('；', $correctOpts) : '';
                }
                $questions[] = $question;
            }
        }
        
        View::assign([
            'user' => $user,
            'paper' => $paper,
            'questions' => $questions,
        ]);
        
        return View::fetch('teacher/paper_preview');
    }
    
    /**
     * 处理手动组卷
     */
    private function handleManualPaper(array $data, int $paperId): void
    {
        // 解码题目数据
        $questionIds = json_decode($data['question_ids'] ?? '[]', true);
        $scores = json_decode($data['scores'] ?? '[]', true);
        
        // 验证JSON解码是否成功
        if ($questionIds === null || $scores === null) {
            throw new \Exception('题目数据格式错误，请刷新页面重试');
        }
        
        if (empty($questionIds)) {
            throw new \Exception('请至少选择一道题目');
        }
        
        // 验证题目数量与分数数量是否匹配
        if (count($questionIds) !== count($scores)) {
            throw new \Exception('题目分数配置错误');
        }
        
        // 批量创建试卷题目关联
        foreach ($questionIds as $i => $qId) {
            PaperQuestion::create([
                'paper_id' => $paperId,
                'question_id' => $qId,
                'score' => isset($scores[$i]) ? floatval($scores[$i]) : 1,
                'sort' => $i + 1,
            ]);
        }
    }
    
    /**
     * 处理AI自动组卷
     */
    private function handleAutoPaper(array $data, array $user, int $paperId): void
    {
        // 获取题目数量
        $questionCount = isset($data['question_count']) ? intval($data['question_count']) : 10;
        if ($questionCount <= 0 || $questionCount > 50) {
            throw new \Exception('题目数量必须在1-50之间');
        }
        
        // 初始化AI服务
        $aiService = new AiService();
        if (!$aiService->isConfigured()) {
            throw new \Exception('AI 服务未配置，请检查 .env 文件中的 AI_API_KEY 和 AI_BASE_URL');
        }
        
        // 获取题型配置
        $typeIds = $this->getTypeIds($data);
        if (empty($typeIds)) {
            throw new \Exception('请至少选择一种题型');
        }
        
        // 解析难度和科目
        $difficultyCode = $this->resolveDifficultyCode($data['difficulty_id'] ?? null);
        $subject = $this->resolveSubject($data);
        
        // 生成AI题目
        $questions = $this->generateAiQuestions($aiService, $subject, $typeIds, $difficultyCode, $questionCount, $data, $user);
        
        if (empty($questions)) {
            throw new \Exception('AI 生成题目失败，请检查 runtime/ai_generate_log.txt 日志');
        }
        
        // 创建试卷题目关联
        $sortIndex = 0;
        foreach ($questions as $question) {
            PaperQuestion::create([
                'paper_id' => $paperId,
                'question_id' => $question['id'],
                'score' => $question['score'] ?? 5,
                'sort' => ++$sortIndex,
            ]);
        }
        
        // 记录成功日志
        $this->logAiGeneration($subject, $questionCount, count($questions), true);
    }
    
    /**
     * 获取题型ID列表
     */
    private function getTypeIds(array $data): array
    {
        $typeIds = [];
        
        if (!empty($data['type_id'])) {
            $typeIds = is_array($data['type_id']) ? $data['type_id'] : [$data['type_id']];
        } elseif (!empty($data['type_ids']) && is_array($data['type_ids'])) {
            $typeIds = $data['type_ids'];
        }
        
        // 过滤掉非数字值（如 "all"）
        $typeIds = array_filter($typeIds, function($v) { return is_numeric($v); });
        $typeIds = array_values(array_map('intval', $typeIds));
        
        // 日志记录
        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            date('Y-m-d H:i:s') . " getTypeIds: raw=" . json_encode($data['type_ids'] ?? []) . ", type_id=" . json_encode($data['type_id'] ?? null) . ", filtered=" . json_encode($typeIds) . "\n",
            FILE_APPEND
        );
        
        // 如果没有指定题型，获取所有题型
        if (empty($typeIds)) {
            $allTypes = QuestionType::select()->toArray();
            $typeIds = array_column($allTypes, 'id');
        }
        
        return $typeIds;
    }
    
    /**
     * 生成AI题目
     */
    private function generateAiQuestions(AiService $aiService, string $subject, array $typeIds, string $difficultyCode, int $questionCount, array $data, array $user): array
    {
        $questions = [];
        $typeScores = $data['type_scores'] ?? [];
        $typeCounts = $data['type_counts'] ?? [];
        
        // 构建每种题型的出题数
        $typeRequests = [];  // ['typeCode' => ['typeId' => x, 'count' => n, 'score' => s]]
        foreach ($typeIds as $typeId) {
            $userTypeCount = isset($typeCounts[$typeId]) ? max(1, intval($typeCounts[$typeId])) : 1;
            $typeCode = $this->resolveTypeCode($typeId);
            $perScore = isset($typeScores[$typeId]) ? intval($typeScores[$typeId]) : 5;
            $typeRequests[$typeCode] = [
                'typeId' => (int)$typeId,
                'count' => $userTypeCount,
                'score' => $perScore,
            ];
        }
        
        if (empty($typeRequests)) {
            return $questions;
        }
        
        // 构建一次性生成所有题型的 prompt
        $systemPrompt = $this->buildMultiTypePrompt();
        $userPrompt = $this->buildMultiTypeUserPrompt($subject, $typeRequests, $difficultyCode);
        
        file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " generateAiQuestions: multi-type request, types=" . json_encode(array_keys($typeRequests)) . "\n", FILE_APPEND);
        
        // 尝试2次
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $chatResult = $aiService->chat($systemPrompt, $userPrompt);
            
            file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " generateAiQuestions: chat result success=" . ($chatResult['success'] ? 'true' : 'false') . ", error=" . ($chatResult['error'] ?? 'none') . "\n", FILE_APPEND);
            
            if (!$chatResult['success'] || empty($chatResult['content'])) {
                if ($attempt === 0) {
                    file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " generateAiQuestions: retrying...\n", FILE_APPEND);
                    sleep(1);
                    continue;
                }
                file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " generateAiQuestions: failed after retry\n", FILE_APPEND);
                return $questions;
            }
            
            $allQuestions = $this->parseMultiTypeResponse($chatResult['content']);
            file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " generateAiQuestions: parsed " . count($allQuestions) . " questions\n", FILE_APPEND);
            
            if (empty($allQuestions)) {
                if ($attempt === 0) { sleep(1); continue; }
                return $questions;
            }
            
            // 按题型分配并保存
            foreach ($typeRequests as $typeCode => $req) {
                $saved = 0;
                foreach ($allQuestions as $aiQ) {
                    if ($saved >= $req['count']) break;
                    
                    $qType = $aiQ['type'] ?? '';
                    if ($this->matchTypeName($qType, $typeCode)) {
                        if (!isset($aiQ['score']) || $aiQ['score'] <= 0) {
                            $aiQ['score'] = $req['score'];
                        }
                        $savedQuestion = $this->saveAiQuestion($aiQ, $data, $user, $req['typeId']);
                        if ($savedQuestion) {
                            $questions[] = $savedQuestion;
                            $saved++;
                            file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " generateAiQuestions: saved question id=" . $savedQuestion['id'] . " type=$typeCode\n", FILE_APPEND);
                        }
                    }
                }
            }
            
            break; // 成功
        }
        
        file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " generateAiQuestions: returning " . count($questions) . " questions\n", FILE_APPEND);
        return $questions;
    }
    
    /**
     * 构建多题型混合生成的 system prompt
     */
    private function buildMultiTypePrompt(): string
    {
        return <<<'PROMPT'
你是一个专业的信息技术课程出题老师。请根据用户要求生成多种题型的题目，以 JSON 格式输出。

支持的题型及格式要求：

1. 判断题(type:"判断题")：只有A正确、B错误两个选项
2. 填空题(type:"填空题")：题中含______表示填空，多空答案用分号;分隔，options为空数组[]
3. 简答题(type:"简答题")：学生用文字回答，options为空数组[]，answer为参考答案文本
4. 单选题(type:"单选题")：4个选项，answer为正确选项字母
5. 多选题(type:"多选题")：4个选项，answer为正确选项字母串如"AB"

通用输出格式：
{
  "questions": [
    {
      "title": "题目内容",
      "type": "题型名称",
      "difficulty": "难度",
      "options": [{"key": "A","content": "选项内容"}],
      "answer": "答案",
      "analysis": "解析",
      "score": 5
    }
  ]
}

注意：
- 必须严格按用户要求的每种题型数量生成
- 所有题目必须在同一个 JSON 的 questions 数组中
- 题目要紧扣知识点，不要偏离主题
PROMPT;
    }
    
    /**
     * 构建多题型用户 prompt
     */
    private function buildMultiTypeUserPrompt(string $subject, array $typeRequests, string $difficulty): string
    {
        $typeNames = [
            'judgment' => '判断题',
            'fill_blank' => '填空题',
            'short_answer' => '简答题',
            'single_choice' => '单选题',
            'multiple_choice' => '多选题',
        ];
        
        $lines = [];
        $lines[] = "请为「{$subject}」知识点生成以下题目，难度：{$difficulty}";
        $lines[] = '';
        
        $totalCount = 0;
        foreach ($typeRequests as $typeCode => $req) {
            $name = $typeNames[$typeCode] ?? $typeCode;
            $lines[] = "- {$name}：{$req['count']} 道";
            $totalCount += $req['count'];
        }
        
        $lines[] = '';
        $lines[] = "总共 {$totalCount} 道题，请一次性全部返回。";
        
        return implode("\n", $lines);
    }
    
    /**
     * 解析多题型混合的 AI 响应
     */
    private function parseMultiTypeResponse(string $content): array
    {
        // 尝试提取 JSON
        $jsonStr = $content;
        
        // 如果被 markdown 代码块包裹，去掉 ```
        if (preg_match('/```(?:json)?\s*\n?(.*?)\n?```/s', $content, $m)) {
            $jsonStr = $m[1];
        }
        
        // 尝试找到 JSON 对象
        if (preg_match('/\{[^{]*"questions"\s*:\s*\[/s', $content, $m, PREG_OFFSET_CAPTURE)) {
            $startPos = $m[0][1];
            $jsonStr = substr($content, $startPos);
            // 截取到最后一个 }
            $lastBrace = strrpos($jsonStr, '}');
            if ($lastBrace !== false) {
                $jsonStr = substr($jsonStr, 0, $lastBrace + 1);
            }
        }
        
        $data = json_decode($jsonStr, true);
        
        if (!$data || empty($data['questions'])) {
            file_put_contents(app()->getRuntimePath() . 'ai_generate_log.txt', date('Y-m-d H:i:s') . " parseMultiTypeResponse: JSON parse failed, raw=" . mb_substr($content, 0, 300) . "\n", FILE_APPEND);
            return [];
        }
        
        return $data['questions'];
    }
    
    /**
     * 匹配 AI 返回的题型名称到内部 code
     */
    private function matchTypeName(string $aiType, string $typeCode): bool
    {
        $map = [
            'judgment' => ['判断题'],
            'fill_blank' => ['填空题'],
            'short_answer' => ['简答题'],
            'single_choice' => ['单选题'],
            'multiple_choice' => ['多选题'],
        ];
        
        $names = $map[$typeCode] ?? [];
        foreach ($names as $name) {
            if (mb_strpos($aiType, $name) !== false) {
                return true;
            }
        }
        return false;
    }
    
    public function questions(Request $request)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            $typeId = $request->param('type_id');
            $difficultyId = $request->param('difficulty_id');
            $knowledgeId = $request->param('knowledge_id');
            $chapterId = $request->param('chapter_id');
            $keyword = $request->param('keyword');
            
            // 构建查询条件（使用参数化查询）
            $query = Question::where('creator_id', $user['id']);
            
            if ($typeId && is_numeric($typeId)) {
                $query->where('type_id', intval($typeId));
            }
            if ($difficultyId && is_numeric($difficultyId)) {
                $query->where('difficulty_id', intval($difficultyId));
            }
            if ($knowledgeId && is_numeric($knowledgeId)) {
                $query->where('knowledge_id', intval($knowledgeId));
            }
            if ($chapterId && is_numeric($chapterId)) {
                $query->where('chapter_id', intval($chapterId));
            }
            if ($keyword) {
                $query->where('title', 'like', '%' . addslashes($keyword) . '%');
            }
            
            $questions = $query->select();
            
            // 补充题型和难度名称
            foreach ($questions as &$question) {
                $question['type_name'] = QuestionType::where('id', $question['type_id'])->value('name');
                $question['difficulty_name'] = Difficulty::where('id', $question['difficulty_id'])->value('name');
            }
            
            return json(['code' => 1, 'data' => $questions]);
        } catch (\Throwable $e) {
            $this->logError($e);
            return json(['code' => 0, 'msg' => '获取题目列表失败']);
        }
    }
    
    public function getChapters(Request $request)
    {
        try {
            $courseId = $request->param('course_id');
            if (!$courseId || !is_numeric($courseId)) {
                return json(['code' => 0, 'msg' => '课程ID无效']);
            }
            
            $chapters = Chapter::where('course_id', intval($courseId))->select();
            return json(['code' => 1, 'data' => $chapters]);
        } catch (\Throwable $e) {
            $this->logError($e);
            return json(['code' => 0, 'msg' => '获取章节列表失败']);
        }
    }
    
    public function quickSave(Request $request)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            $data = $request->post();
            
            // 验证必要字段
            if (empty($data['title'])) {
                return json(['code' => 0, 'msg' => '请输入题干']);
            }
            if (empty($data['type_id']) || !is_numeric($data['type_id'])) {
                return json(['code' => 0, 'msg' => '请选择题型']);
            }
            
            // 验证题型是否存在
            $questionType = QuestionType::find($data['type_id']);
            if (!$questionType) {
                return json(['code' => 0, 'msg' => '题型不存在']);
            }
            
            Db::startTrans();
            
            // 创建题目
            $question = Question::create([
                'title' => $data['title'],
                'type_id' => intval($data['type_id']),
                'difficulty_id' => isset($data['difficulty_id']) && is_numeric($data['difficulty_id']) ? intval($data['difficulty_id']) : 1,
                'chapter_id' => isset($data['chapter_id']) && is_numeric($data['chapter_id']) ? intval($data['chapter_id']) : null,
                'knowledge_id' => isset($data['knowledge_id']) && is_numeric($data['knowledge_id']) ? intval($data['knowledge_id']) : null,
                'score' => isset($data['score']) ? floatval($data['score']) : 1,
                'analysis' => $data['analysis'] ?? '',
                'creator_id' => $user['id'],
            ]);
            
            $typeCode = $questionType['code'];
            
            // 根据题型处理选项和答案
            if (in_array($typeCode, ['single_choice', 'multiple_choice', 'judgment'])) {
                $this->saveQuestionOptions($question['id'], $typeCode, $data);
            } elseif ($typeCode == 'fill_blank') {
                $this->saveFillBlankAnswers($question['id'], $data);
            } else {
                $this->saveQuestionAnswer($question['id'], $data);
            }
            
            Db::commit();
            
            // 补充返回数据
            $question['type_name'] = $questionType['name'];
            $question['difficulty_name'] = Difficulty::where('id', $question['difficulty_id'])->value('name');
            
            return json(['code' => 1, 'msg' => '题目创建成功', 'data' => $question]);
            
        } catch (\Throwable $e) {
            Db::rollback();
            $this->logError($e);
            return json(['code' => 0, 'msg' => '保存失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 保存选择题选项
     */
    private function saveQuestionOptions(int $questionId, string $typeCode, array $data): void
    {
        $options = $data['options'] ?? [];
        if (empty($options)) {
            throw new \Exception('请填写选项内容');
        }
        
        $answers = isset($data['answer']) ? (array)$data['answer'] : [];
        
        foreach ($options as $index => $optionText) {
            if (empty($optionText)) {
                continue;
            }
            
            $letter = chr(65 + $index);
            $isCorrect = 0;
            
            if ($typeCode == 'single_choice') {
                // 单选题：只有一个正确答案
                $isCorrect = (isset($answers[0]) && $answers[0] == $letter) ? 1 : 0;
            } elseif ($typeCode == 'multiple_choice') {
                // 多选题：可以有多个正确答案
                $isCorrect = in_array($letter, $answers) ? 1 : 0;
            } elseif ($typeCode == 'judgment') {
                // 判断题：A表示正确，B表示错误
                $isCorrect = (isset($answers[0]) && $answers[0] == $letter) ? 1 : 0;
            }
            
            QuestionOption::create([
                'question_id' => $questionId,
                'option_content' => $optionText,
                'option_key' => $letter,
                'sort' => $index + 1,
                'is_correct' => $isCorrect,
            ]);
        }
    }
    
    /**
     * 保存填空题答案
     */
    private function saveFillBlankAnswers(int $questionId, array $data): void
    {
        $answers = explode(';', $data['answer'] ?? '');
        $hasValidAnswer = false;
        
        foreach ($answers as $idx => $ans) {
            $trimmed = trim($ans);
            if (!empty($trimmed)) {
                QuestionAnswer::create([
                    'question_id' => $questionId,
                    'answer_content' => $trimmed,
                    'blank_index' => $idx + 1,
                ]);
                $hasValidAnswer = true;
            }
        }
        
        if (!$hasValidAnswer) {
            throw new \Exception('请填写正确答案');
        }
    }
    
    /**
     * 保存普通题目的答案
     */
    private function saveQuestionAnswer(int $questionId, array $data): void
    {
        if (empty($data['answer'])) {
            throw new \Exception('请填写正确答案');
        }
        
        QuestionAnswer::create([
            'question_id' => $questionId,
            'answer_content' => $data['answer'],
            'blank_index' => 1,
        ]);
    }
    
    public function publish(Request $request)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            $paperId = $request->param('paper_id');
            if (!$paperId || !is_numeric($paperId)) {
                return json(['code' => 0, 'msg' => '试卷ID无效']);
            }
            
            $paper = PaperModel::find(intval($paperId));
            if (!$paper || $paper['creator_id'] != $user['id']) {
                return json(['code' => 0, 'msg' => '权限不足']);
            }
            
            // 切换发布状态
            $paper->status = $paper['status'] == 1 ? 0 : 1;
            $paper->save();
            
            return json(['code' => 1, 'msg' => $paper['status'] == 1 ? '已发布' : '已取消发布']);
        } catch (\Throwable $e) {
            $this->logError($e);
            return json(['code' => 0, 'msg' => '操作失败']);
        }
    }
    
    public function delete(Request $request)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            $paperId = $request->param('paper_id');
            if (!$paperId || !is_numeric($paperId)) {
                return json(['code' => 0, 'msg' => '试卷ID无效']);
            }
            
            $paper = PaperModel::find(intval($paperId));
            if (!$paper || $paper['creator_id'] != $user['id']) {
                return json(['code' => 0, 'msg' => '权限不足']);
            }
            
            Db::startTrans();
            
            // 删除关联的试卷题目
            PaperQuestion::where('paper_id', $paperId)->delete();
            // 删除试卷
            $paper->delete();
            
            Db::commit();
            
            return json(['code' => 1, 'msg' => '删除成功']);
        } catch (\Throwable $e) {
            Db::rollback();
            $this->logError($e);
            return json(['code' => 0, 'msg' => '删除失败']);
        }
    }
    
    /**
     * 更新试卷统计信息
     */
    private function updatePaperStats(int $paperId): void
    {
        $paperQuestions = PaperQuestion::where('paper_id', $paperId)->select();
        $paper = PaperModel::find($paperId);
        
        if ($paper) {
            $paper->question_count = count($paperQuestions);
            $paper->total_score = 0;
            foreach ($paperQuestions as $pq) {
                $paper->total_score += $pq['score'] ?? 0;
            }
            $paper->save();
        }
    }
    
    /**
     * 解析题型代码
     */
    private function resolveTypeCode($typeId): string
    {
        $typeMap = [
            'single_choice' => 'single_choice',
            'multiple_choice' => 'multiple_choice',
            'judgment' => 'judgment',
            'fill_blank' => 'fill_blank',
            'short_answer' => 'short_answer',
            'practice' => 'short_answer',
            'programming' => 'short_answer',
        ];
        
        if (!empty($typeId) && is_numeric($typeId)) {
            $typeObj = QuestionType::find(intval($typeId));
            if ($typeObj && isset($typeObj['code'])) {
                return $typeMap[$typeObj['code']] ?? 'single_choice';
            }
        }
        
        return 'single_choice';
    }
    
    /**
     * 解析难度代码
     */
    private function resolveDifficultyCode($difficultyId): string
    {
        $difficultyMap = [
            'easy' => 'easy',
            'medium' => 'medium',
            'hard' => 'hard',
        ];
        
        if (!empty($difficultyId) && is_numeric($difficultyId)) {
            $diffObj = Difficulty::find(intval($difficultyId));
            if ($diffObj && isset($diffObj['code'])) {
                return $difficultyMap[$diffObj['code']] ?? 'medium';
            }
        }
        
        return 'medium';
    }
    
    /**
     * 解析科目
     */
    private function resolveSubject(array $data): string
    {
        if (!empty($data['knowledge_id']) && is_numeric($data['knowledge_id'])) {
            $knowledge = Knowledge::find(intval($data['knowledge_id']));
            if ($knowledge && !empty($knowledge['name'])) {
                return $knowledge['name'];
            }
        }
        
        if (!empty($data['course_id']) && is_numeric($data['course_id'])) {
            $course = Course::find(intval($data['course_id']));
            if ($course && !empty($course['name'])) {
                return $course['name'];
            }
        }
        
        return '通用知识';
    }
    
    /**
     * 保存AI生成的题目
     */
    private function saveAiQuestion(array $aiQ, array $data, array $user, ?int $defaultTypeId = null): ?Question
    {
        try {
            // 获取题型ID
            $typeId = $defaultTypeId ?? ($data['type_id'] ?? null);
            if (empty($typeId)) {
                $firstType = QuestionType::order('id', 'asc')->find();
                $typeId = $firstType ? $firstType['id'] : null;
            }
            
            // 获取难度ID
            $difficultyId = $data['difficulty_id'] ?? null;
            if (empty($difficultyId)) {
                $firstDiff = Difficulty::where('code', 'medium')->find();
                $difficultyId = $firstDiff ? $firstDiff['id'] : null;
            }
            
            // 获取课程ID - 问题4修复：AI生成的题目关联所属课程
            $courseId = null;
            if (!empty($data['course_id']) && is_numeric($data['course_id'])) {
                $courseId = intval($data['course_id']);
            }
            
            // 获取知识点/章节
            $knowledgeId = null;
            $chapterId = null;
            if (!empty($data['knowledge_id']) && is_numeric($data['knowledge_id'])) {
                $knowledgeId = intval($data['knowledge_id']);
            }
            if (!empty($data['chapter_id']) && is_numeric($data['chapter_id'])) {
                $chapterId = intval($data['chapter_id']);
            }
            
            // 验证必填字段
            if (empty($aiQ['title'])) {
                $this->logError(new \Exception('AI生成的题目标题为空'));
                return null;
            }
            
            // 创建题目 - 问题4修复：添加course_id
            $question = Question::create([
                'title' => $aiQ['title'],
                'type_id' => $typeId,
                'difficulty_id' => $difficultyId,
                'course_id' => $courseId,
                'knowledge_id' => $knowledgeId,
                'chapter_id' => $chapterId,
                'score' => $aiQ['score'] ?? 5,
                'analysis' => $aiQ['analysis'] ?? '',
                'creator_id' => $user['id'],
            ]);
            
            // 处理选项和答案
            $options = $aiQ['options'] ?? [];
            $answer = $aiQ['answer'] ?? '';
            
            if (!empty($options)) {
                $this->saveAiQuestionOptions($question['id'], $options, $answer);
            } else {
                $this->saveAiQuestionAnswers($question['id'], $answer);
            }
            
            return $question;
            
        } catch (\Throwable $e) {
            $this->logError($e);
            return null;
        }
    }
    
    /**
     * 保存AI题目的选项
     */
    private function saveAiQuestionOptions(int $questionId, array $options, $answer): void
    {
        $answerLetters = [];
        if (is_string($answer)) {
            $answerLetters = str_split(strtoupper(trim($answer)));
        } elseif (is_array($answer)) {
            $answerLetters = array_map('strtoupper', $answer);
        }
        
        $correctContents = [];
        
        foreach ($options as $idx => $opt) {
            $key = $opt['key'] ?? chr(65 + $idx);
            $content = $opt['content'] ?? $opt;
            
            if (empty($content)) {
                continue;
            }
            
            $isCorrect = in_array(strtoupper($key), $answerLetters) ? 1 : 0;
            
            QuestionOption::create([
                'question_id' => $questionId,
                'option_content' => $content,
                'option_key' => strtoupper($key),
                'sort' => $idx + 1,
                'is_correct' => $isCorrect,
            ]);
            
            if ($isCorrect) {
                $correctContents[] = strtoupper($key) . '. ' . $content;
            }
        }
        
        // 同时保存答案到 question_answer 表（预览和历史查看需要）
        $answerDisplay = !empty($correctContents) ? implode('；', $correctContents) : (is_string($answer) ? $answer : json_encode($answer));
        QuestionAnswer::create([
            'question_id' => $questionId,
            'answer_content' => $answerDisplay,
        ]);
    }
    
    /**
     * 保存AI题目的答案
     */
    private function saveAiQuestionAnswers(int $questionId, $answer): void
    {
        $answerText = is_array($answer) ? implode(';', $answer) : (string)$answer;
        $answerParts = explode(';', $answerText);
        
        $hasValidAnswer = false;
        foreach ($answerParts as $aIdx => $ans) {
            $trimmed = trim($ans);
            if ($trimmed !== '') {
                QuestionAnswer::create([
                    'question_id' => $questionId,
                    'answer_content' => $trimmed,
                    'blank_index' => $aIdx + 1,
                ]);
                $hasValidAnswer = true;
            }
        }
        
        if (!$hasValidAnswer) {
            // 如果没有答案，创建一个默认答案
            QuestionAnswer::create([
                'question_id' => $questionId,
                'answer_content' => $answerText,
                'blank_index' => 1,
            ]);
        }
    }
    
    /**
     * 保存考试配置
     */
    private function saveExamConfig(int $paperId, array $data): void
    {
        $duration = isset($data['duration']) ? intval($data['duration']) : 0;
        if ($duration <= 0) {
            $duration = 60; // 默认60分钟
        }
        
        // 检查是否已有配置，有则更新，无则创建
        $config = ExamConfig::where('paper_id', $paperId)->find();
        if ($config) {
            $config->save(['duration' => $duration]);
        } else {
            ExamConfig::create([
                'paper_id' => $paperId,
                'duration' => $duration,
            ]);
        }
    }
    
    /**
     * 记录错误日志
     */
    private function logError(\Throwable $e): void
    {
        $logContent = sprintf(
            "[%s] Error: %s\nFile: %s:%d\nStack Trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        
        try {
            file_put_contents(
                app()->getRuntimePath() . 'paper_error.log',
                $logContent,
                FILE_APPEND
            );
        } catch (\Throwable $ex) {
            // 静默失败，不影响主流程
        }
    }
    
    /**
     * 记录AI生成日志
     */
    private function logAiGeneration(string $subject, int $needCount, int $actualCount, bool $success): void
    {
        $logContent = sprintf(
            "[%s] AI Generation: %s\nSubject: %s, Need: %d, Actual: %d\n\n",
            date('Y-m-d H:i:s'),
            $success ? 'SUCCESS' : 'FAILED',
            $subject,
            $needCount,
            $actualCount
        );
        
        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            $logContent,
            FILE_APPEND
        );
    }
    
    /**
     * 记录AI生成错误
     */
    private function logAiGenerationError($typeId, string $typeCode, int $attempt, string $error): void
    {
        $logContent = sprintf(
            "[%s] AI Generation Attempt %d Failed\nType: %s (ID: %s), Error: %s\n\n",
            date('Y-m-d H:i:s'),
            $attempt,
            $typeCode,
            $typeId,
            $error
        );
        
        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            $logContent,
            FILE_APPEND
        );
    }
}
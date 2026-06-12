<?php
declare(strict_types=1);

namespace app\controller\student;

use app\service\AiService;
use think\facade\Session;
use think\Request;

class Ai
{
    /**
     * AI 随机出题接口
     * POST /student/ai/generate
     *
     * 参数: subject(知识点), type(题型), difficulty(难度), count(数量)
     */
    public function generate(Request $request)
    {
        $user = Session::get('user');

        $subject    = $request->post('subject', '');
        $type       = $request->post('type', 'single_choice');
        $difficulty = $request->post('difficulty', 'medium');
        $count      = (int) $request->post('count', 1);

        if (empty($subject)) {
            return json(['code' => 0, 'msg' => '请指定知识点/章节']);
        }

        $maxCount = config('ai.generate_question_max') ?? 5;
        if ($count > $maxCount) {
            $count = $maxCount;
        }

        $service = new AiService();

        if (!$service->isConfigured()) {
            return json(['code' => 0, 'msg' => 'AI 服务未配置，请联系管理员']);
        }

        $result = $service->generateQuestions($subject, $type, $difficulty, $count);

        if (!$result['success']) {
            return json(['code' => 0, 'msg' => $result['error']]);
        }

        return json(['code' => 1, 'msg' => '生成成功', 'data' => $result['data']]);
    }

    /**
     * AI 批改接口（主观题）
     * POST /student/ai/grade
     *
     * 参数: question, reference_answer, user_answer, total_score
     */
    public function grade(Request $request)
    {
        $user = Session::get('user');

        $question        = $request->post('question', '');
        $referenceAnswer = $request->post('reference_answer', '');
        $userAnswer      = $request->post('user_answer', '');
        $totalScore      = (int) $request->post('total_score', 10);

        if (empty($question)) {
            return json(['code' => 0, 'msg' => '缺少题目内容']);
        }

        $service = new AiService();

        if (!$service->isConfigured()) {
            return json(['code' => 0, 'msg' => 'AI 服务未配置，请联系管理员']);
        }

        $result = $service->gradeAnswer($question, $referenceAnswer, $userAnswer, $totalScore);

        if (!$result['success']) {
            return json(['code' => 0, 'msg' => $result['error']]);
        }

        return json(['code' => 1, 'msg' => '批改完成', 'data' => $result['data']]);
    }

    /**
     * AI 学习报告接口
     * POST /student/ai/report
     *
     * 参数: 无（后端自动收集学生数据）
     */
    public function report(Request $request)
    {
        $user = Session::get('user');

        // 收集学生学习数据
        $stats = [
            'user_name'      => $user['real_name'] ?? '',
            'practice_count' => \app\model\PracticeRecord::where('user_id', $user['id'])->where('type', 'practice')->count(),
            'exam_count'     => \app\model\PracticeRecord::where('user_id', $user['id'])->where('type', 'exam')->count(),
            'error_count'    => \app\model\ErrorQuestion::where('user_id', $user['id'])->count(),
            'favorite_count' => \app\model\FavoriteQuestion::where('user_id', $user['id'])->count(),
        ];

        // 计算练习正确率
        $practiceRecords = \app\model\PracticeRecord::where('user_id', $user['id'])
            ->where('type', 'practice')
            ->where('status', 1)
            ->select();
        $totalScore = 0;
        $totalFull  = 0;
        foreach ($practiceRecords as $rec) {
            $totalScore += $rec['score'] ?? 0;
            $totalFull  += $rec['total_score'] ?? 0;
        }
        $stats['practice_correct_rate'] = $totalFull > 0 ? round($totalScore / $totalFull * 100) . '%' : '暂无数据';

        // 计算考试平均分
        $examRecords = \app\model\PracticeRecord::where('user_id', $user['id'])
            ->where('type', 'exam')
            ->where('status', 1)
            ->select();
        $examTotal = 0;
        $examCount = 0;
        foreach ($examRecords as $rec) {
            $examTotal += $rec['score'] ?? 0;
            $examCount++;
        }
        $stats['exam_avg_score'] = $examCount > 0 ? round($examTotal / $examCount) : '暂无数据';

        $service = new AiService();

        if (!$service->isConfigured()) {
            return json(['code' => 0, 'msg' => 'AI 服务未配置，请联系管理员']);
        }

        $result = $service->generateReport($stats);

        if (!$result['success']) {
            return json(['code' => 0, 'msg' => $result['error']]);
        }

        return json(['code' => 1, 'msg' => '报告生成成功', 'data' => $result['data']]);
    }
}
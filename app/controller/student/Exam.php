<?php
declare(strict_types=1);

namespace app\controller\student;

use app\model\Paper;
use app\model\PaperQuestion;
use app\model\Question;
use app\model\QuestionOption;
use app\model\QuestionAnswer;
use app\model\QuestionType;
use app\model\ExamConfig;
use app\model\PracticeRecord;
use app\model\PracticeDetail;
use app\model\ErrorQuestion;
use app\service\AiService;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Exam
{
    public function index()
    {
        $user = Session::get('user');
        
        $papers = Paper::where('status', 1)->select();
        
        foreach ($papers as &$paper) {
            $completedRecord = PracticeRecord::where('user_id', $user['id'])
                ->where('paper_id', $paper['id'])
                ->where('type', 'exam')
                ->where('status', 1)
                ->find();
            $paper['is_completed'] = $completedRecord ? true : false;
            $paper['record_id'] = $completedRecord ? $completedRecord['id'] : null;
        }
        
        View::assign([
            'user' => $user,
            'papers' => $papers,
        ]);
        
        return View::fetch('student/exam');
    }
    
    public function start(Request $request)
    {
        $user = Session::get('user');
        $paperId = $request->param('paper_id');
        
        $paper = Paper::find($paperId);
        if (!$paper || $paper->status != 1) {
            return redirect('/student/exam');
        }
        
        PracticeRecord::where('user_id', $user['id'])
            ->where('paper_id', $paperId)
            ->where('type', 'exam')
            ->where('status', 0)
            ->delete();
        
        $config = ExamConfig::where('paper_id', $paperId)->find();
        
        $paperQuestions = PaperQuestion::where('paper_id', $paperId)->order('sort')->select();
        
        $questions = [];
        foreach ($paperQuestions as $pq) {
            $question = Question::find($pq['question_id']);
            if (!$question) continue;
            
            $item = $question->toArray();
            $item['paper_score'] = $pq['score'] ?: $question['score'];
            $item['options'] = QuestionOption::where('question_id', $question['id'])->order('sort')->select();
            $item['answers'] = QuestionAnswer::where('question_id', $question['id'])->order('blank_index')->select();
            $item['type_name'] = QuestionType::where('id', $question['type_id'])->value('name');
            $questions[] = $item;
        }
        
        $record = PracticeRecord::create([
            'user_id' => $user['id'],
            'paper_id' => $paperId,
            'type' => 'exam',
            'title' => $paper['name'],
            'total_score' => $paper['total_score'],
            'start_time' => date('Y-m-d H:i:s'),
            'status' => 0,
        ]);
        
        Session::set('exam_record_id', $record['id']);
        
        View::assign([
            'user' => $user,
            'paper' => $paper,
            'config' => $config,
            'questions' => $questions,
            'duration' => $config ? $config['duration'] * 60 : 3600,
        ]);
        
        return View::fetch('student/exam_start');
    }
    
    public function submit(Request $request)
    {
        $user = Session::get('user');
        $recordId = Session::get('exam_record_id');
        $answers = $request->post('answers', []);
        
        if (!$recordId) {
            return json(['code' => 0, 'msg' => '考试记录不存在，请重新开始考试']);
        }
        
        $record = PracticeRecord::find($recordId);
        if (!$record) {
            return json(['code' => 0, 'msg' => '考试记录不存在']);
        }
        if ($record['status'] == 1) {
            return json(['code' => 0, 'msg' => '考试已结束，不能重复提交']);
        }
        
        $paperQuestions = PaperQuestion::where('paper_id', $record['paper_id'])->select();
        
        $totalScore = 0;
        $correctCount = 0;
        
        foreach ($paperQuestions as $pq) {
            $question = Question::find($pq['question_id']);
            if (!$question) continue;
            
            $userAnswer = $answers[$pq['question_id']] ?? '';
            $score = $pq['score'] ?: $question['score'];
            $isCorrect = false;
            $typeCode = QuestionType::where('id', $question['type_id'])->value('code');

            $aiScore = null;
            $aiComment = null;
            $gradeStatus = null;

            if ($typeCode == 'short_answer') {
                // 简答题：AI 自动评分
                $correctAnswer = QuestionAnswer::where('question_id', $question['id'])->value('answer_content');
                $userAnswerStr = is_array($userAnswer) ? implode('', $userAnswer) : (string)$userAnswer;

                $aiService = new AiService();
                if ($aiService->isConfigured()) {
                    try {
                        $aiResult = $aiService->gradeAnswer($question['title'], $correctAnswer, $userAnswerStr, $score);
                        if ($aiResult['success'] && isset($aiResult['data']['score'])) {
                            $aiScore = floatval($aiResult['data']['score']);
                            $aiComment = $aiResult['data']['comment'] ?? '';
                            $gradeStatus = 'graded';
                            $totalScore += $aiScore;
                            $isCorrect = $aiScore >= ($score * 0.6);
                            if ($isCorrect) $correctCount++;
                        } else {
                            // AI 评分失败，回退到精确匹配
                            $isCorrect = (trim($userAnswerStr) === trim($correctAnswer));
                            if ($isCorrect) {
                                $totalScore += $score;
                                $correctCount++;
                            }
                        }
                    } catch (\Throwable $e) {
                        // AI 调用异常，回退到精确匹配
                        $isCorrect = (trim($userAnswerStr) === trim($correctAnswer));
                        if ($isCorrect) {
                            $totalScore += $score;
                            $correctCount++;
                        }
                    }
                } else {
                    // 未配置 AI，回退到精确匹配
                    $isCorrect = (trim($userAnswerStr) === trim($correctAnswer));
                    if ($isCorrect) {
                        $totalScore += $score;
                        $correctCount++;
                    }
                }
            } elseif ($typeCode == 'fill_blank') {
                $correctAnswers = QuestionAnswer::where('question_id', $question['id'])->column('answer_content');
                $userAnswers = array_filter(explode(';', (string)$userAnswer));
                $isCorrect = count(array_diff($correctAnswers, $userAnswers)) === 0 && count($userAnswers) === count($correctAnswers);
            } else {
                $correctOptions = QuestionOption::where('question_id', $question['id'])->where('is_correct', 1)->column('option_key');
                sort($correctOptions);
                $userOptions = str_split(strtoupper($userAnswer));
                sort($userOptions);
                $isCorrect = $correctOptions === $userOptions;
            }
            
            if ($isCorrect) {
                $totalScore += $score;
                $correctCount++;
            } else {
                $errorQuestion = ErrorQuestion::where('user_id', $user['id'])
                    ->where('question_id', $question['id'])
                    ->find();
                if ($errorQuestion) {
                    $errorQuestion->wrong_count = ($errorQuestion->wrong_count ?? 0) + 1;
                    $errorQuestion->last_wrong_time = date('Y-m-d H:i:s');
                    $errorQuestion->save();
                } else {
                    ErrorQuestion::create([
                        'user_id' => $user['id'],
                        'question_id' => $question['id'],
                        'wrong_count' => 1,
                        'last_wrong_time' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            
            // 设置 grade_status
            if ($typeCode == 'short_answer') {
                if (!isset($gradeStatus)) {
                    $gradeStatus = $isCorrect ? 'auto' : 'wrong';
                }
            } else {
                $gradeStatus = $isCorrect ? 'auto' : 'wrong';
            }
            
            PracticeDetail::create([
                'record_id' => $recordId,
                'question_id' => $question['id'],
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect ? 1 : 0,
                'score' => $isCorrect ? $score : 0,
                'question_type' => $typeCode,
                'ai_score' => $aiScore ?? 0,
                'ai_comment' => $aiComment ?? '',
                'grade_status' => $gradeStatus ?? ($isCorrect ? 'auto' : 'wrong'),
                'answer_time' => date('Y-m-d H:i:s'),
            ]);
        }
        
        $record->score = $totalScore;
        $record->is_passed = $totalScore >= ($record['total_score'] * 0.6) ? 1 : 0;
        $record->status = 1;
        $record->submit_time = date('Y-m-d H:i:s');
        $record->save();
        
        Session::delete('exam_record_id');
        
        return json([
            'code' => 1,
            'msg' => '提交成功',
            'data' => [
                'record_id' => $recordId,
                'score' => $totalScore,
                'total_score' => $record['total_score'],
                'correct_count' => $correctCount,
                'total_count' => count($paperQuestions),
            ]
        ]);
    }

    public function detail(Request $request)
    {
        $user = Session::get('user');
        $recordId = $request->param('record_id');

        $record = PracticeRecord::where('id', $recordId)
            ->where('user_id', $user['id'])
            ->where('status', 1)
            ->find();

        if (!$record) {
            return redirect('/student/exam');
        }

        $paper = Paper::find($record['paper_id']);

        $details = PracticeDetail::where('record_id', $recordId)->select();
        $questions = [];

        foreach ($details as $detail) {
            $question = Question::find($detail['question_id']);
            if (!$question) continue;

            $item = $question->toArray();
            $item['user_answer'] = $detail['user_answer'];
            $item['is_correct'] = $detail['is_correct'];
            $item['score'] = $detail['score'];
            $item['type_name'] = QuestionType::where('id', $question['type_id'])->value('name');
            $item['type_code'] = QuestionType::where('id', $question['type_id'])->value('code');
            $item['options'] = QuestionOption::where('question_id', $question['id'])->order('sort')->select();
            $item['correct_options'] = QuestionOption::where('question_id', $question['id'])
                ->where('is_correct', 1)->column('option_key');
            $item['answers'] = QuestionAnswer::where('question_id', $question['id'])->order('blank_index')->select();

            $questions[] = $item;
        }

        View::assign([
            'user' => $user,
            'record' => $record,
            'paper' => $paper,
            'questions' => $questions,
        ]);

        return View::fetch('student/exam_detail');
    }
}
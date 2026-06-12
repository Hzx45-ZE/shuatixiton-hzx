<?php
declare(strict_types=1);

namespace app\controller\student;

use app\model\Question;
use app\model\QuestionOption;
use app\model\QuestionAnswer;
use app\model\PracticeRecord;
use app\model\PracticeDetail;
use app\model\QuestionType;
use app\model\Difficulty;
use app\model\Knowledge;
use app\model\Chapter;
use app\model\Course;
use app\service\AiService;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Practice
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        $chapterId = $request->param('chapter_id');

        if ($chapterId) {
            $query = Question::where('chapter_id', $chapterId);
            $questions = $query->orderRaw('RAND()')->limit(10)->select()->toArray();

            foreach ($questions as &$question) {
                $question['type_name'] = QuestionType::where('id', $question['type_id'])->value('name');
                $question['type_code'] = QuestionType::where('id', $question['type_id'])->value('code');
                $question['difficulty_name'] = Difficulty::where('id', $question['difficulty_id'])->value('name');
                $question['knowledge_name'] = Knowledge::where('id', $question['knowledge_id'])->value('name');

                $options = QuestionOption::where('question_id', $question['id'])->order('sort')->select();
                $question['options'] = $options ? $options->toArray() : [];
            }

            $courses = Course::select();

            View::assign([
                'user' => $user,
                'questions' => $questions,
                'courses' => $courses,
                'chapterId' => $chapterId,
                'chapter_groups' => [],
            ]);
        } else {
            $courses = Course::select();
            $chapter_groups = [];

            foreach ($courses as $course) {
                $chapters = Chapter::where('course_id', $course['id'])->select();
                $course_chapters = [];
                foreach ($chapters as $chapter) {
                    $count = Question::where('chapter_id', $chapter['id'])->count();

                    $hasPracticed = PracticeRecord::where('user_id', $user['id'])
                        ->where('type', 'practice')
                        ->where('status', 1)
                        ->where('title', 'like', '%章节练习-' . $chapter['id'] . '-%')
                        ->find();

                    $course_chapters[] = [
                        'id' => $chapter['id'],
                        'name' => $chapter['name'],
                        'question_count' => $count,
                        'is_completed' => $hasPracticed ? true : false,
                    ];
                }
                if (!empty($course_chapters)) {
                    $chapter_groups[] = [
                        'course_name' => $course['name'],
                        'chapters' => $course_chapters,
                    ];
                }
            }

            View::assign([
                'user' => $user,
                'questions' => [],
                'courses' => $courses,
                'chapterId' => null,
                'chapter_groups' => $chapter_groups,
            ]);
        }

        return View::fetch('student/practice');
    }

    public function submit(Request $request)
    {
        $user = Session::get('user');
        $data = $request->post();

        $answers = $data['answers'] ?? [];
        $questionIds = $data['question_ids'] ?? [];

        if (empty($answers)) {
            return json(['code' => 0, 'msg' => '请至少回答一道题']);
        }

        $questions = Question::whereIn('id', $questionIds)->select()->toArray();
        $questionMap = [];
        foreach ($questions as $q) {
            $questionMap[$q['id']] = $q;
        }

        $totalScore = 0;
        $userScore = 0;
        $details = [];

        foreach ($questionIds as $questionId) {
            $q = $questionMap[$questionId] ?? null;
            if (!$q) continue;
            $totalScore += $q['score'] ?? 1;
        }

        $chapterId = $data['chapter_id'] ?? 0;
        $record = PracticeRecord::create([
            'user_id' => $user['id'],
            'type' => 'practice',
            'title' => '章节练习-' . $chapterId . '-' . date('YmdHis'),
            'score' => 0,
            'total_score' => $totalScore,
            'time_spent' => 0,
            'status' => 1,
            'start_time' => date('Y-m-d H:i:s'),
            'submit_time' => date('Y-m-d H:i:s'),
        ]);

        foreach ($answers as $questionId => $userAnswer) {
            $q = $questionMap[$questionId] ?? null;
            if (!$q) continue;

            $questionTypeCode = QuestionType::where('id', $q['type_id'])->value('code');
            $score = $q['score'] ?? 1;
            $isCorrect = false;
            $correctAnswerArr = [];

            if (in_array($questionTypeCode, ['single_choice', 'multiple_choice', 'judgment'])) {
                $correctAnswerArr = QuestionOption::where('question_id', $questionId)
                    ->where('is_correct', 1)
                    ->column('option_key');

                $studentAnswerArr = is_array($userAnswer) ? $userAnswer : [$userAnswer];
                sort($studentAnswerArr);
                sort($correctAnswerArr);

                if ($studentAnswerArr == $correctAnswerArr) {
                    $isCorrect = true;
                }
            }
            elseif ($questionTypeCode == 'fill_blank') {
                $correctAnswerArr = QuestionAnswer::where('question_id', $questionId)
                    ->order('blank_index')
                    ->column('answer_content');

                $studentAnswerStr = is_array($userAnswer) ? implode(';', $userAnswer) : $userAnswer;
                $studentAnswerArr = explode(';', $studentAnswerStr);

                $allCorrect = true;
                foreach ($correctAnswerArr as $idx => $correct) {
                    $student = isset($studentAnswerArr[$idx]) ? trim($studentAnswerArr[$idx]) : '';
                    if ($student !== trim($correct)) {
                        $allCorrect = false;
                        break;
                    }
                }
                if ($allCorrect && count($correctAnswerArr) > 0) {
                    $isCorrect = true;
                }
            }
            else {
                // 简答题：AI 自动评分
                $correctAnswer = QuestionAnswer::where('question_id', $questionId)->value('answer_content');
                $studentAnswerStr = is_array($userAnswer) ? implode('', $userAnswer) : $userAnswer;

                $aiService = new AiService();
                if ($aiService->isConfigured()) {
                    try {
                        $aiResult = $aiService->gradeAnswer($q['title'], $correctAnswer, $studentAnswerStr, $score);
                        if ($aiResult['success'] && isset($aiResult['data']['score'])) {
                            $aiScore = floatval($aiResult['data']['score']);
                            $aiComment = $aiResult['data']['comment'] ?? '';
                            $gradeStatus = 'graded';
                            $userScore += $aiScore;
                            $isCorrect = $aiScore >= ($score * 0.6);
                        } else {
                            // AI 评分失败，回退到精确匹配
                            $isCorrect = (trim($studentAnswerStr) === trim($correctAnswer));
                            if ($isCorrect) $userScore += $score;
                            $aiScore = 0;
                            $aiComment = '';
                            $gradeStatus = 'wrong';
                        }
                    } catch (\Throwable $e) {
                        // AI 调用异常，回退到精确匹配
                        $isCorrect = (trim($studentAnswerStr) === trim($correctAnswer));
                        if ($isCorrect) $userScore += $score;
                        $aiScore = 0;
                        $aiComment = '';
                        $gradeStatus = 'wrong';
                    }
                } else {
                    // 未配置 AI，回退到精确匹配
                    $isCorrect = (trim($studentAnswerStr) === trim($correctAnswer));
                    if ($isCorrect) $userScore += $score;
                    $aiScore = 0;
                    $aiComment = '';
                    $gradeStatus = 'wrong';
                }
            }

            // 简答题的分数已在 AI 评分时计算，其他题型答对才加分
            if ($isCorrect && $questionTypeCode != 'short_answer') {
                $userScore += $score;
            }

            PracticeDetail::create([
                'record_id' => $record['id'],
                'question_id' => $questionId,
                'user_answer' => is_array($userAnswer) ? implode('|', $userAnswer) : $userAnswer,
                'is_correct' => $isCorrect ? 1 : 0,
                'score' => $isCorrect ? $score : 0,
                'question_type' => $questionTypeCode,
                'ai_score' => $aiScore ?? 0,
                'ai_comment' => $aiComment ?? '',
                'grade_status' => $gradeStatus ?? 'wrong',
                'answer_time' => date('Y-m-d H:i:s'),
            ]);

            $details[$questionId] = [
                'is_correct' => $isCorrect ? 1 : 0,
                'score' => $isCorrect ? $score : 0,
                'correct_answer' => $correctAnswerArr,
                'user_answer' => is_array($userAnswer) ? $userAnswer : [$userAnswer],
                'debug' => [
                    'question_type' => $questionTypeCode,
                    'db_correct_count' => count($correctAnswerArr),
                    'student_answer' => is_array($userAnswer) ? $userAnswer : [$userAnswer],
                ],
            ];
        }

        $record->score = $userScore;
        $record->save();

        return json([
            'code' => 1,
            'msg' => '提交成功',
            'data' => [
                'score' => $userScore,
                'total_score' => $totalScore,
                'rate' => $totalScore > 0 ? round($userScore / $totalScore * 100) : 0,
                'details' => $details,
            ],
        ]);
    }
}

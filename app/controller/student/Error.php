<?php
declare(strict_types=1);

namespace app\controller\student;

use app\model\ErrorQuestion;
use app\model\Question;
use app\model\QuestionOption;
use app\model\QuestionAnswer;
use app\model\QuestionType;
use app\model\Difficulty;
use app\model\Knowledge;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Error
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        
        $knowledgeId = $request->param('knowledge_id');
        $typeId = $request->param('type_id');
        
        $query = ErrorQuestion::where('user_id', $user['id']);
        
        $errorQuestions = $query->select();
        
        $questions = [];
        foreach ($errorQuestions as $eq) {
            $question = Question::find($eq['question_id']);
            if (!$question) continue;
            
            if ($typeId && $question['type_id'] != $typeId) continue;
            if ($knowledgeId && $question['knowledge_id'] != $knowledgeId) continue;
            
            $item = $question->toArray();
            $item['wrong_count'] = $eq['wrong_count'];
            $item['options'] = QuestionOption::where('question_id', $question['id'])->order('sort')->select()->toArray();
            $item['type_name'] = QuestionType::where('id', $question['type_id'])->value('name');
            $item['type_code'] = QuestionType::where('id', $question['type_id'])->value('code');
            $item['difficulty_name'] = Difficulty::where('id', $question['difficulty_id'])->value('name');
            
            $knowledge = Knowledge::find($question['knowledge_id']);
            $item['knowledge_name'] = $knowledge ? $knowledge['name'] : '';
            
            $questions[] = $item;
        }
        
        $types = QuestionType::select();
        $knowledges = Knowledge::select();
        
        View::assign([
            'user' => $user,
            'questions' => $questions,
            'types' => $types,
            'knowledges' => $knowledges,
            'selectedType' => $typeId,
            'selectedKnowledge' => $knowledgeId,
        ]);
        
        return View::fetch('student/error');
    }
    
    public function remove(Request $request)
    {
        $user = Session::get('user');
        $questionId = $request->post('question_id');
        
        $result = ErrorQuestion::where('user_id', $user['id'])->where('question_id', $questionId)->delete();
        
        if ($result) {
            return json(['code' => 1, 'msg' => '已移除错题']);
        }
        
        return json(['code' => 0, 'msg' => '移除失败']);
    }

    public function grade(Request $request)
    {
        $user = Session::get('user');
        $questionId = $request->post('question_id');
        $userAnswer = $request->post('answer', '');

        $question = Question::find($questionId);
        if (!$question) {
            return json(['code' => 0, 'msg' => '题目不存在']);
        }

        $typeCode = QuestionType::where('id', $question['type_id'])->value('code');
        $options = QuestionOption::where('question_id', $questionId)->order('sort')->select()->toArray();
        $answers = QuestionAnswer::where('question_id', $questionId)->order('blank_index')->select()->toArray();

        $isCorrect = false;
        $correctAnswer = '';

        if (in_array($typeCode, ['single_choice', 'judgment'])) {
            foreach ($options as $opt) {
                if ($opt['is_correct'] == 1) {
                    $correctAnswer = $opt['option_key'];
                    break;
                }
            }
            $isCorrect = (strtoupper(trim((string)$userAnswer)) === strtoupper($correctAnswer));
        } elseif ($typeCode == 'multiple_choice') {
            $correctKeys = [];
            foreach ($options as $opt) {
                if ($opt['is_correct'] == 1) {
                    $correctKeys[] = $opt['option_key'];
                }
            }
            sort($correctKeys);
            $correctAnswer = implode('', $correctKeys);

            $userKeys = is_array($userAnswer) ? $userAnswer : str_split(strtoupper(trim((string)$userAnswer)));
            if (is_array($userKeys)) {
                $userKeys = array_map('strtoupper', $userKeys);
                sort($userKeys);
                $isCorrect = (implode('', $userKeys) === $correctAnswer);
            }
        } elseif ($typeCode == 'fill_blank') {
            $userParts = array_map('trim', explode(';', (string)$userAnswer));
            $correctParts = [];
            foreach ($answers as $ans) {
                $correctParts[] = $ans['answer_content'];
            }
            $correctAnswer = implode('; ', $correctParts);

            if (count($userParts) === count($correctParts)) {
                $allMatch = true;
                foreach ($correctParts as $i => $cp) {
                    if (!isset($userParts[$i]) || trim($userParts[$i]) !== trim($cp)) {
                        $allMatch = false;
                        break;
                    }
                }
                $isCorrect = $allMatch;
            }
        } else {
            if (!empty($answers)) {
                $correctAnswer = $answers[0]['answer_content'];
                $isCorrect = (trim((string)$userAnswer) === trim($correctAnswer));
            }
        }

        $userAnswerDisplay = is_array($userAnswer) ? implode('', $userAnswer) : (string)$userAnswer;

        $data = [
            'is_correct' => $isCorrect,
            'user_answer' => $userAnswerDisplay,
            'correct_answer' => $correctAnswer,
            'analysis' => $question['analysis'] ?? '',
            'options' => $options,
            'type_code' => $typeCode,
        ];

        return json(['code' => 1, 'msg' => $isCorrect ? '回答正确' : '回答错误', 'data' => $data]);
    }
}

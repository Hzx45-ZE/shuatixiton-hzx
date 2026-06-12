<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\Question as QuestionModel;
use app\model\QuestionOption;
use app\model\QuestionAnswer;
use app\model\QuestionType;
use app\model\Chapter;
use app\model\Course;
use app\model\Knowledge;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Question
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        $chapterId = $request->param('chapter_id');

        if (!$chapterId) {
            return View::fetch('teacher/question');
        }

        $chapter = Chapter::find($chapterId);
        if (!$chapter) {
            return View::fetch('teacher/question');
        }

        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return View::fetch('teacher/question');
        }

        $questions = QuestionModel::where('chapter_id', $chapterId)->order('create_time', 'desc')->paginate(20);

        View::assign([
            'user' => $user,
            'chapter' => $chapter,
            'chapter_id' => $chapterId,
            'chapter_name' => $chapter['name'],
            'course' => $course,
            'course_name' => $course['name'],
            'questions' => $questions,
            'types' => QuestionType::select(),
            'difficulties' => \app\model\Difficulty::select(),
            'knowledges' => Knowledge::where('chapter_id', $chapterId)->select(),
        ]);

        return View::fetch('teacher/question');
    }

    public function save(Request $request)
    {
        try {
            $user = Session::get('user');
            
            if (!$user) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            $data = $request->post();
            
            if (empty($data['title'])) {
                return json(['code' => 0, 'msg' => '题干不能为空']);
            }

            if (empty($data['chapter_id'])) {
                return json(['code' => 0, 'msg' => '章节ID不能为空']);
            }
            
            if (empty($data['type_id'])) {
                return json(['code' => 0, 'msg' => '请选择题型']);
            }
            
            if (empty($data['difficulty_id'])) {
                return json(['code' => 0, 'msg' => '请选择难度']);
            }

            $chapter = Chapter::find($data['chapter_id']);
            if (!$chapter) {
                return json(['code' => 0, 'msg' => '章节不存在']);
            }

            $course = Course::find($chapter['course_id']);
            if (!$course || $course['teacher_id'] != $user['id']) {
                return json(['code' => 0, 'msg' => '无权操作']);
            }

            $isUpdate = !empty($data['id']);

            if ($isUpdate) {
                $question = QuestionModel::find($data['id']);
                if (!$question) {
                    return json(['code' => 0, 'msg' => '题目不存在']);
                }

                $question->save([
                'title' => $data['title'],
                'type_id' => $data['type_id'],
                'difficulty_id' => $data['difficulty_id'],
                'knowledge_id' => isset($data['knowledge_id']) ? $data['knowledge_id'] : null,
                'score' => isset($data['score']) ? $data['score'] : 1,
                'analysis' => isset($data['analysis']) ? $data['analysis'] : '',
            ]);

                QuestionOption::where('question_id', $question['id'])->delete();
                QuestionAnswer::where('question_id', $question['id'])->delete();
            } else {
                $question = QuestionModel::create([
                    'chapter_id' => $data['chapter_id'],
                    'title' => $data['title'],
                    'type_id' => $data['type_id'],
                    'difficulty_id' => $data['difficulty_id'],
                    'knowledge_id' => isset($data['knowledge_id']) ? $data['knowledge_id'] : null,
                    'score' => isset($data['score']) ? $data['score'] : 1,
                    'analysis' => isset($data['analysis']) ? $data['analysis'] : '',
                    'creator_id' => $user['id'],
                ]);
            }

            $typeCode = QuestionType::where('id', $data['type_id'])->value('code');

            if (in_array($typeCode, ['single_choice', 'multiple_choice', 'judgment'])) {
                $options = $data['options'] ?? [];
                
                // 处理正确答案 - 兼容多种格式
                $answers = [];
                if (isset($data['answer'])) {
                    if (is_array($data['answer'])) {
                        $answers = array_values($data['answer']);
                    } else {
                        $answers = [$data['answer']];
                    }
                }
                
                foreach ($options as $index => $optionText) {
                    if (!empty($optionText)) {
                        $letter = chr(65 + $index);
                        // 判断该选项是否是正确答案
                        $isCorrect = in_array($letter, $answers) ? 1 : 0;
                        
                        QuestionOption::create([
                            'question_id' => $question['id'],
                            'option_content' => $optionText,
                            'option_key' => $letter,
                            'sort' => $index + 1,
                            'is_correct' => $isCorrect,
                        ]);
                    }
                }
                
                // 验证是否至少有一个正确答案
                $correctCount = QuestionOption::where('question_id', $question['id'])
                    ->where('is_correct', 1)
                    ->count();
                    
                if ($correctCount == 0 && in_array($typeCode, ['single_choice', 'multiple_choice', 'judgment'])) {
                    return json(['code' => 0, 'msg' => '请选择正确答案！']);
                }
            } elseif ($typeCode == 'fill_blank') {
                $answerStr = $data['answer'] ?? '';
                $answers = explode(';', $answerStr);
                foreach ($answers as $index => $answer) {
                    if (!empty(trim($answer))) {
                        QuestionAnswer::create([
                            'question_id' => $question['id'],
                            'answer_content' => trim($answer),
                            'blank_index' => $index,
                        ]);
                    }
                }
            } else {
                if (!empty($data['answer'])) {
                    QuestionAnswer::create([
                        'question_id' => $question['id'],
                        'answer_content' => $data['answer'],
                    ]);
                }
            }

            return json(['code' => 1, 'msg' => '题目保存成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '保存失败: ' . $e->getMessage()]);
        }
    }

    public function detail(Request $request)
    {
        $user = Session::get('user');
        $id = $request->get('id');

        $question = QuestionModel::with(['options', 'answers', 'type'])->find($id);
        if (!$question) {
            return Response::error('题目不存在');
        }

        $chapter = Chapter::find($question['chapter_id']);
        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }

        $data = [
            'id' => $question['id'],
            'title' => $question['title'],
            'type_id' => $question['type_id'],
            'type_code' => $question['type']['code'] ?? '',
            'difficulty_id' => $question['difficulty_id'],
            'knowledge_id' => $question['knowledge_id'],
            'score' => $question['score'],
            'analysis' => $question['analysis'],
            'options' => $question['options'] ? $question['options']->toArray() : [],
            'answers' => $question['answers'] ? $question['answers']->toArray() : [],
        ];

        return Response::success('获取成功', $data);
    }

    public function delete(Request $request)
    {
        $user = Session::get('user');
        $id = $request->post('id');

        $question = QuestionModel::find($id);
        if (!$question) {
            return Response::error('题目不存在');
        }

        $chapter = Chapter::find($question['chapter_id']);
        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }

        QuestionOption::where('question_id', $id)->delete();
        QuestionAnswer::where('question_id', $id)->delete();
        $question->delete();

        return Response::success('删除成功');
    }

    public function importTemplate()
    {
        $types = QuestionType::select();
        $difficulties = \app\model\Difficulty::select();

        View::assign([
            'types' => $types,
            'difficulties' => $difficulties,
        ]);

        return View::fetch('teacher/question_import_template');
    }

    public function import(Request $request)
    {
        $user = Session::get('user');
        $file = $request->file('file');

        if (!$file) {
            return Response::error('请选择文件');
        }

        $info = $file->validate(['ext' => 'xls,xlsx'])->move('./uploads/import');
        if (!$info) {
            return Response::error($file->getError());
        }

        $filePath = './uploads/import/' . $info->getSaveName();
        
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $success = 0;
            $failed = 0;

            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $chapterId = $sheet->getCell('A' . $row)->getValue();
                    $title = $sheet->getCell('B' . $row)->getValue();
                    $typeId = $sheet->getCell('C' . $row)->getValue();
                    $difficultyId = $sheet->getCell('D' . $row)->getValue();
                    $score = $sheet->getCell('E' . $row)->getValue() ?: 1;
                    $optionA = $sheet->getCell('F' . $row)->getValue();
                    $optionB = $sheet->getCell('G' . $row)->getValue();
                    $optionC = $sheet->getCell('H' . $row)->getValue();
                    $optionD = $sheet->getCell('I' . $row)->getValue();
                    $answer = $sheet->getCell('J' . $row)->getValue();
                    $analysis = $sheet->getCell('K' . $row)->getValue() ?? '';

                    if (empty($title) || empty($chapterId)) {
                        continue;
                    }

                    $chapter = Chapter::find($chapterId);
                    if (!$chapter) {
                        $failed++;
                        continue;
                    }

                    $course = Course::find($chapter['course_id']);
                    if (!$course || $course['teacher_id'] != $user['id']) {
                        $failed++;
                        continue;
                    }

                    $question = QuestionModel::create([
                        'chapter_id' => $chapterId,
                        'title' => $title,
                        'type_id' => $typeId,
                        'difficulty_id' => $difficultyId,
                        'score' => $score,
                        'analysis' => $analysis,
                        'creator_id' => $user['id'],
                    ]);

                    $options = [$optionA, $optionB, $optionC, $optionD];
                    foreach ($options as $index => $optionText) {
                        if (!empty($optionText)) {
                            $letter = chr(65 + $index);
                            QuestionOption::create([
                                'question_id' => $question['id'],
                                'option_content' => $optionText,
                                'option_key' => $letter,
                                'sort' => $index + 1,
                                'is_correct' => strtoupper($answer) === $letter ? 1 : 0,
                            ]);
                        }
                    }

                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }

            unlink($filePath);

            return Response::success("导入完成，成功{$success}条，失败{$failed}条");
        } catch (\Exception $e) {
            return Response::error('导入失败：' . $e->getMessage());
        }
    }
}

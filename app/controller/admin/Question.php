<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\Question as QuestionModel;
use app\model\QuestionOption;
use app\model\QuestionAnswer;
use app\model\QuestionType;
use app\model\Difficulty;
use app\model\Knowledge;
use app\model\Chapter;
use app\model\Course;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Question
{
    public function index(Request $request)
    {
        $user = Session::get('user');

        $courses = Course::with('chapters.knowledges')->select();

        $keyword = $request->param('keyword', '');
        $query = QuestionModel::with(['chapter.course']);

        if ($keyword) {
            $query = $query->where('title', 'like', "%$keyword%");
        }

        $questions = $query->order('create_time', 'desc')->paginate(20);

        foreach ($questions as &$question) {
            $question['type_name'] = QuestionType::where('id', $question['type_id'])->value('name');
            $question['difficulty_name'] = Difficulty::where('id', $question['difficulty_id'])->value('name');
        }

        $types = QuestionType::select();
        $difficulties = Difficulty::select();

        View::assign([
            'user' => $user,
            'questions' => $questions,
            'courses' => $courses,
            'types' => $types,
            'difficulties' => $difficulties,
            'keyword' => $keyword,
        ]);

        return View::fetch('admin/question');
    }

    public function get(Request $request)
    {
        $id = $request->param('id');
        $question = QuestionModel::find($id);

        if (!$question) {
            return Response::error('题目不存在');
        }

        $question['options'] = QuestionOption::where('question_id', $id)->order('sort')->select();
        $question['answers'] = QuestionAnswer::where('question_id', $id)->order('blank_index')->select();
        $question['type_code'] = QuestionType::where('id', $question['type_id'])->value('code');

        return Response::success($question);
    }

    public function save(Request $request)
    {
        $data = $request->post();

        if (empty($data['title'])) {
            return Response::error('题干不能为空');
        }

        $isUpdate = !empty($data['id']);

        if ($isUpdate) {
            $question = QuestionModel::find($data['id']);
            if (!$question) {
                return Response::error('题目不存在');
            }

            $question->save([
                'title' => $data['title'],
                'chapter_id' => $data['chapter_id'] ?: null,
                'type_id' => $data['type_id'],
                'difficulty_id' => $data['difficulty_id'],
                'knowledge_id' => $data['knowledge_id'] ?: null,
                'score' => $data['score'] ?? 1,
                'analysis' => $data['analysis'] ?? '',
                'status' => 1,
            ]);

            QuestionOption::where('question_id', $question['id'])->delete();
            QuestionAnswer::where('question_id', $question['id'])->delete();
        } else {
            $question = QuestionModel::create([
                'title' => $data['title'],
                'chapter_id' => $data['chapter_id'] ?: null,
                'type_id' => $data['type_id'],
                'difficulty_id' => $data['difficulty_id'],
                'knowledge_id' => $data['knowledge_id'] ?: null,
                'score' => $data['score'] ?? 1,
                'analysis' => $data['analysis'] ?? '',
                'status' => 1,
            ]);
        }

        $typeCode = QuestionType::where('id', $data['type_id'])->value('code');

        if (in_array($typeCode, ['single_choice', 'multiple_choice', 'judgment'])) {
            $options = $data['options'] ?? [];
            foreach ($options as $index => $optionText) {
                if (!empty($optionText)) {
                    $letter = chr(65 + $index);
                    QuestionOption::create([
                        'question_id' => $question['id'],
                        'option_content' => $optionText,
                        'option_key' => $letter,
                        'sort' => $index + 1,
                        'is_correct' => in_array($letter, (array)($data['answer'] ?? [])) ? 1 : 0,
                    ]);
                }
            }
        } elseif ($typeCode == 'fill_blank') {
            $answers = explode(';', $data['answer'] ?? '');
            foreach ($answers as $index => $answer) {
                if (!empty(trim($answer))) {
                    QuestionAnswer::create([
                        'question_id' => $question['id'],
                        'answer_content' => trim($answer),
                        'blank_index' => $index + 1,
                    ]);
                }
            }
        } else {
            if (!empty($data['answer'])) {
                QuestionAnswer::create([
                    'question_id' => $question['id'],
                    'answer_content' => $data['answer'],
                    'blank_index' => 1,
                ]);
            }
        }

        return Response::success(null, $isUpdate ? '题目修改成功' : '题目添加成功');
    }

    public function delete(Request $request)
    {
        $id = $request->post('id');

        $question = QuestionModel::find($id);
        if (!$question) {
            return Response::error('题目不存在');
        }

        QuestionOption::where('question_id', $id)->delete();
        QuestionAnswer::where('question_id', $id)->delete();
        $question->delete();

        return Response::success(null, '题目删除成功');
    }

    public function getKnowledges(Request $request)
    {
        $chapterId = $request->param('chapter_id');
        $knowledges = Knowledge::where('chapter_id', $chapterId)->select();
        return Response::success($knowledges);
    }

    public function getChapters(Request $request)
    {
        $courseId = $request->param('course_id');
        $chapters = Chapter::where('course_id', $courseId)->select();
        return Response::success($chapters);
    }

    public function importTemplate()
    {
        $types = QuestionType::select();
        $typeNames = [];
        foreach ($types as $t) {
            $typeNames[] = $t['name'];
        }
        $typeNamesStr = implode('/', $typeNames);

        $difficulties = Difficulty::select();
        $diffNames = [];
        foreach ($difficulties as $d) {
            $diffNames[] = $d['name'];
        }
        $diffNamesStr = implode('/', $diffNames);

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="题目导入模板.xls"');
        echo '<html><head><meta charset="UTF-8"></head><body>';
        echo '<table border="1">';
        echo '<tr>';
        echo '<th>题干</th><th>题型</th><th>难度</th><th>选项A</th><th>选项B</th><th>选项C</th><th>选项D</th><th>正确答案</th><th>解析</th><th>分值</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>示例：1+1=?</td>';
        echo '<td>' . $typeNamesStr . '</td>';
        echo '<td>' . $diffNamesStr . '</td>';
        echo '<td>1</td><td>2</td><td>3</td><td>4</td>';
        echo '<td>B</td>';
        echo '<td>1+1=2</td><td>1</td>';
        echo '</tr>';
        echo '</table>';
        echo '</body></html>';
        exit;
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            return Response::error('请上传文件');
        }

        require_once app()->getRootPath() . 'vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
        require_once app()->getRootPath() . 'vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

        try {
            $objPHPExcel = \PHPExcel_IOFactory::load($file->getPathname());
            $sheet = $objPHPExcel->getActiveSheet();
            $rows = $sheet->toArray();

            $successCount = 0;
            $errorCount = 0;

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row[0])) continue;

                $title = trim($row[0]);
                $typeName = trim($row[1] ?? '');
                $diffName = trim($row[2] ?? '');
                $optionA = trim($row[3] ?? '');
                $optionB = trim($row[4] ?? '');
                $optionC = trim($row[5] ?? '');
                $optionD = trim($row[6] ?? '');
                $answer = trim($row[7] ?? '');
                $analysis = trim($row[8] ?? '');
                $score = intval($row[9] ?? 1);

                $type = QuestionType::where('name', $typeName)->find();
                $diff = Difficulty::where('name', $diffName)->find();

                if (!$type || !$diff) {
                    $errorCount++;
                    continue;
                }

                $question = QuestionModel::create([
                    'title' => $title,
                    'type_id' => $type['id'],
                    'difficulty_id' => $diff['id'],
                    'score' => $score > 0 ? $score : 1,
                    'analysis' => $analysis,
                    'status' => 1,
                ]);

                if (in_array($type['code'], ['single_choice', 'multiple_choice', 'judgment'])) {
                    $options = ['A' => $optionA, 'B' => $optionB, 'C' => $optionC, 'D' => $optionD];
                    $correctAnswers = explode(',', $answer);
                    $sort = 1;
                    foreach ($options as $letter => $text) {
                        if (!empty($text)) {
                            QuestionOption::create([
                                'question_id' => $question['id'],
                                'option_content' => $text,
                                'option_key' => $letter,
                                'sort' => $sort,
                                'is_correct' => in_array($letter, $correctAnswers) ? 1 : 0,
                            ]);
                            $sort++;
                        }
                    }
                } elseif ($type['code'] == 'fill_blank') {
                    $answers = explode(';', $answer);
                    foreach ($answers as $idx => $ans) {
                        if (!empty(trim($ans))) {
                            QuestionAnswer::create([
                                'question_id' => $question['id'],
                                'answer_content' => trim($ans),
                                'blank_index' => $idx + 1,
                            ]);
                        }
                    }
                } else {
                    if (!empty($answer)) {
                        QuestionAnswer::create([
                            'question_id' => $question['id'],
                            'answer_content' => $answer,
                            'blank_index' => 1,
                        ]);
                    }
                }

                $successCount++;
            }

            return Response::success(null, "导入完成：成功 {$successCount} 条，失败 {$errorCount} 条");
        } catch (\Exception $e) {
            return Response::error('导入失败：' . $e->getMessage());
        }
    }
}
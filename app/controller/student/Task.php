<?php
declare(strict_types=1);

namespace app\controller\student;

use app\model\StudyTask;
use app\model\Paper;
use app\model\PaperQuestion;
use app\model\Question;
use app\model\QuestionOption;
use app\model\QuestionAnswer;
use app\model\QuestionType;
use app\model\ExamConfig;
use app\model\PracticeRecord;
use app\model\PracticeDetail;
use app\model\ClassStudent;
use app\model\ClassInfo;
use app\service\AiService;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Task
{
    public function index()
    {
        $user = Session::get('user');

        // 获取学生所在班级
        $classStudent = ClassStudent::where('student_id', $user['id'])->find();
        $classId = $classStudent ? $classStudent['class_id'] : 0;

        // 获取该班级的任务
        $query = StudyTask::where('status', 1);
        if ($classId > 0) {
            $query->where('class_id', $classId);
        }
        $tasks = $query->order('create_time', 'desc')->select();

        $taskList = [];
        foreach ($tasks as $task) {
            $paper = Paper::find($task['related_id']);
            $taskItem = $task->toArray();
            $taskItem['paper_name'] = $paper ? $paper['name'] : '试卷已删除';
            $taskItem['paper_total_score'] = $paper ? $paper['total_score'] : 0;

            // 检查是否已完成
            $completed = PracticeRecord::where('user_id', $user['id'])
                ->where('paper_id', $task['related_id'])
                ->where('type', 'exam')
                ->where('status', 1)
                ->find();
            $taskItem['is_completed'] = $completed ? true : false;
            $taskItem['record_id'] = $completed ? $completed['id'] : null;

            // 格式化时间
            $taskItem['start_time'] = $task['start_time'] ?? '';
            $taskItem['end_time'] = $task['deadline'] ?? '';

            $taskList[] = $taskItem;
        }

        View::assign([
            'user' => $user,
            'tasks' => $taskList,
        ]);

        return View::fetch('student/task');
    }

    public function start(Request $request)
    {
        $user = Session::get('user');
        $taskId = $request->param('task_id');

        $task = StudyTask::find($taskId);
        if (!$task || $task['status'] != 1) {
            return redirect('/student/tasks')->with('error', '任务不存在或已结束');
        }

        $paperId = $task['related_id'];
        $paper = Paper::find($paperId);
        if (!$paper) {
            return redirect('/student/tasks')->with('error', '试卷不存在');
        }

        // 检查是否在有效时间内
        $now = date('Y-m-d H:i:s');
        if (!empty($task['start_time']) && $now < $task['start_time']) {
            return redirect('/student/tasks')->with('error', '任务尚未开始');
        }
        if (!empty($task['deadline']) && $now > $task['deadline']) {
            return redirect('/student/tasks')->with('error', '任务已截止');
        }

        // 删除未完成的考试记录
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
            'task' => $task,
        ]);

        return View::fetch('student/exam_start');
    }
}
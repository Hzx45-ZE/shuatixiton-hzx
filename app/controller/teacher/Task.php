<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\StudyTask;
use app\model\Paper;
use app\model\ClassInfo;
use app\model\ClassStudent;
use app\model\Course;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Task
{
    public function index()
    {
        $user = Session::get('user');

        $tasks = StudyTask::with('paper')->where('teacher_id', $user['id'])->order('create_time', 'desc')->select();

        foreach ($tasks as &$task) {
            $class = ClassInfo::find($task['class_id']);
            $task['class_name'] = $class ? $class['name'] : '-';

            $totalStudents = ClassStudent::where('class_id', $task['class_id'])->count();
            $task['total_students'] = $totalStudents;
        }

        $classes = ClassInfo::where('teacher_id', $user['id'])->select();
        $papers = Paper::where('creator_id', $user['id'])->select();

        View::assign([
            'user' => $user,
            'tasks' => $tasks,
            'taskCount' => count($tasks),
            'classes' => $classes,
            'papers' => $papers,
        ]);

        return View::fetch('teacher/task');
    }

    public function create(Request $request)
    {
        $user = Session::get('user');

        if ($request->isPost()) {
            $data = $request->post();

            if (empty($data['title'])) {
                return Response::error('任务标题不能为空');
            }

            if (empty($data['class_id'])) {
                return Response::error('请选择班级');
            }

            if (empty($data['paper_id'])) {
                return Response::error('请选择试卷');
            }

            if (empty($data['start_time']) || empty($data['end_time'])) {
                return Response::error('请设置开始和结束时间');
            }

            StudyTask::create([
                'title' => $data['title'],
                'teacher_id' => $user['id'],
                'class_id' => $data['class_id'],
                'paper_id' => $data['paper_id'],
                'type' => $data['type'] ?? 'practice',
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'description' => $data['description'] ?? '',
                'status' => 1,
            ]);

            return Response::success(null, '任务发布成功');
        }

        $classes = ClassInfo::where('teacher_id', $user['id'])->select();
        $papers = Paper::with('questions')->where('creator_id', $user['id'])->select();

        View::assign([
            'user' => $user,
            'classes' => $classes,
            'papers' => $papers,
        ]);

        return View::fetch('teacher/task_create');
    }

    public function delete(Request $request)
    {
        $user = Session::get('user');
        $id = $request->post('id');

        $task = StudyTask::find($id);
        if (!$task || $task['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }

        $task->delete();

        return Response::success(null, '任务删除成功');
    }
}
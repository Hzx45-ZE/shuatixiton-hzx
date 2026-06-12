<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\ClassInfo;
use app\model\ClassStudent;
use app\model\User;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class ClassManager
{
    public function index()
    {
        $user = Session::get('user');

        $classes = ClassInfo::with('teacher')->order('create_time', 'desc')->select();

        foreach ($classes as &$class) {
            $class['student_count'] = ClassStudent::where('class_id', $class['id'])->count();
        }

        $teachers = User::where('role', 2)->select();

        View::assign([
            'user' => $user,
            'classes' => $classes,
            'teachers' => $teachers,
        ]);

        return View::fetch('admin/class');
    }

    public function save(Request $request)
    {
        $data = $request->post();

        if (empty($data['name'])) {
            return Response::error('班级名称不能为空');
        }

        if (!empty($data['id'])) {
            $class = ClassInfo::find($data['id']);
            if (!$class) {
                return Response::error('班级不存在');
            }

            $class->save([
                'name' => $data['name'],
                'grade' => $data['grade'] ?? '',
                'major' => $data['major'] ?? '',
                'description' => $data['description'] ?? '',
                'teacher_id' => $data['teacher_id'] ?: null,
            ]);

            return Response::success(null, '班级修改成功');
        }

        ClassInfo::create([
            'name' => $data['name'],
            'grade' => $data['grade'] ?? '',
            'major' => $data['major'] ?? '',
            'description' => $data['description'] ?? '',
            'teacher_id' => $data['teacher_id'] ?: null,
        ]);

        return Response::success(null, '班级添加成功');
    }

    public function get(Request $request)
    {
        $id = $request->param('id');
        $class = ClassInfo::find($id);

        if (!$class) {
            return Response::error('班级不存在');
        }

        return Response::success($class);
    }

    public function delete(Request $request)
    {
        $id = $request->post('id');
        $class = ClassInfo::find($id);

        if (!$class) {
            return Response::error('班级不存在');
        }

        ClassStudent::where('class_id', $id)->delete();
        $class->delete();

        return Response::success(null, '班级删除成功');
    }

    public function students(Request $request)
    {
        $user = Session::get('user');
        $classId = $request->param('id');

        $class = ClassInfo::find($classId);
        if (!$class) {
            return Response::error('班级不存在');
        }

        $classStudents = ClassStudent::where('class_id', $classId)->with('student')->select();
        $studentIds = $classStudents->column('student_id');

        $allStudents = User::where('role', 1)->select();

        View::assign([
            'user' => $user,
            'class' => $class,
            'classStudents' => $classStudents,
            'allStudents' => $allStudents,
            'studentIds' => $studentIds,
        ]);

        return View::fetch('admin/class_students');
    }

    public function addStudent(Request $request)
    {
        $classId = $request->post('class_id');
        $studentId = $request->post('student_id');

        $class = ClassInfo::find($classId);
        if (!$class) {
            return Response::error('班级不存在');
        }

        $exists = ClassStudent::where('class_id', $classId)->where('student_id', $studentId)->find();
        if ($exists) {
            return Response::error('该学生已在班级中');
        }

        ClassStudent::create([
            'class_id' => $classId,
            'student_id' => $studentId,
        ]);

        return Response::success(null, '学生添加成功');
    }

    public function removeStudent(Request $request)
    {
        $classId = $request->post('class_id');
        $studentId = $request->post('student_id');

        ClassStudent::where('class_id', $classId)->where('student_id', $studentId)->delete();

        return Response::success(null, '学生移除成功');
    }
}
<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\ClassInfo;
use app\model\ClassStudent;
use app\model\User;
use think\facade\View;
use think\facade\Session;
use think\Request;

class ClassManager
{
    public function index()
    {
        $user = Session::get('user');

        $classes = ClassInfo::where('teacher_id', $user['id'])->select();
        $classCount = count($classes);

        foreach ($classes as &$class) {
            $class['student_count'] = ClassStudent::where('class_id', $class['id'])->count();
        }

        $allClasses = ClassInfo::with('teacher')->select();

        View::assign([
            'user' => $user,
            'classes' => $classes,
            'classCount' => $classCount,
            'allClasses' => $allClasses,
        ]);

        return View::fetch('teacher/class');
    }

    public function join(Request $request)
    {
        $user = Session::get('user');
        $classId = $request->post('class_id');

        $class = ClassInfo::find($classId);
        if (!$class) {
            return json(['code' => 0, 'msg' => '班级不存在']);
        }

        if ($class['teacher_id'] == $user['id']) {
            return json(['code' => 0, 'msg' => '您已在该班级中']);
        }

        $class->teacher_id = $user['id'];
        $class->save();

        return json(['code' => 1, 'msg' => '加入班级成功']);
    }
}
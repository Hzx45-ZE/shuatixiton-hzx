<?php
declare(strict_types=1);

namespace app\controller\student;

use app\model\ClassInfo;
use app\model\ClassStudent;
use think\facade\View;
use think\facade\Session;
use think\Request;

class ClassManager
{
    public function index()
    {
        $user = Session::get('user');

        $classStudent = ClassStudent::where('student_id', $user['id'])->find();
        $myClass = null;
        if ($classStudent) {
            $myClass = ClassInfo::find($classStudent['class_id']);
        }

        $allClasses = ClassInfo::with('teacher')->select();

        View::assign([
            'user' => $user,
            'myClass' => $myClass,
            'allClasses' => $allClasses,
        ]);

        return View::fetch('student/class');
    }

    public function join(Request $request)
    {
        $user = Session::get('user');
        $classId = $request->post('class_id');

        $class = ClassInfo::find($classId);
        if (!$class) {
            return json(['code' => 0, 'msg' => '班级不存在']);
        }

        ClassStudent::where('student_id', $user['id'])->delete();

        ClassStudent::create([
            'class_id' => $classId,
            'student_id' => $user['id'],
        ]);

        return json(['code' => 1, 'msg' => '加入班级成功']);
    }
}
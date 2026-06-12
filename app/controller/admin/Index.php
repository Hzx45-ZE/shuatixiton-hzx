<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\User;
use app\model\Course;
use app\model\Question;
use app\model\ClassInfo;
use think\facade\View;
use think\facade\Session;

class Index
{
    public function index()
    {
        $user = Session::get('user');
        
        $studentCount = User::where('role', 1)->count();
        $teacherCount = User::where('role', 2)->count();
        $courseCount = Course::count();
        $questionCount = Question::count();
        $classCount = ClassInfo::count();
        
        View::assign([
            'user' => $user,
            'studentCount' => $studentCount,
            'teacherCount' => $teacherCount,
            'courseCount' => $courseCount,
            'questionCount' => $questionCount,
            'classCount' => $classCount,
        ]);
        
        return View::fetch('admin/index');
    }
}

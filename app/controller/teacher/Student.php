<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\User;
use app\model\ClassInfo;
use app\model\ClassStudent;
use app\model\PracticeRecord;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Student
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        
        $classId = $request->param('class_id');
        
        $classes = ClassInfo::where('teacher_id', $user['id'])->select();
        
        $students = [];
        if ($classId) {
            $studentIds = ClassStudent::where('class_id', $classId)->where('status', 1)->column('student_id');
            $students = User::whereIn('id', $studentIds)->where('role', 1)->select();
        }
        
        View::assign([
            'user' => $user,
            'classes' => $classes,
            'selectedClass' => $classId,
            'students' => $students,
        ]);
        
        return View::fetch('teacher/student');
    }
    
    public function detail(Request $request)
    {
        $user = Session::get('user');
        $studentId = $request->param('student_id');
        
        $student = User::find($studentId);
        if (!$student || $student->role != 1) {
            return redirect('/teacher/students');
        }
        
        $records = PracticeRecord::where('user_id', $studentId)
            ->where('status', 1)
            ->order('create_time', 'desc')
            ->select();
        
        View::assign([
            'user' => $user,
            'student' => $student,
            'records' => $records,
        ]);
        
        return View::fetch('teacher/student_detail');
    }
}
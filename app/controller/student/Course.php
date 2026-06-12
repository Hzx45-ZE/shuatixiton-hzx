<?php
declare(strict_types=1);

namespace app\controller\student;

use app\model\Course as CourseModel;
use app\model\Chapter;
use app\model\Knowledge;
use app\model\Question;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Course
{
    public function index()
    {
        $user = Session::get('user');
        $courses = CourseModel::where('status', 1)->select();
        
        View::assign([
            'user' => $user,
            'courses' => $courses,
        ]);
        
        return View::fetch('student/course');
    }
    
    public function detail(Request $request)
    {
        $user = Session::get('user');
        $courseId = $request->param('id');
        
        $course = CourseModel::find($courseId);
        if (!$course || $course['status'] != 1) {
            return View::fetch('error', ['msg' => '课程不存在或未启用']);
        }
        
        $chapters = Chapter::where('course_id', $courseId)->order('sort')->select();
        
        foreach ($chapters as &$chapter) {
            $chapter['knowledge_list'] = Knowledge::where('chapter_id', $chapter['id'])->order('sort')->select();
            $chapter['question_count'] = Question::where('chapter_id', $chapter['id'])->where('status', 1)->count();
        }
        
        View::assign([
            'user' => $user,
            'course' => $course,
            'chapters' => $chapters,
        ]);
        
        return View::fetch('student/course_detail');
    }
}

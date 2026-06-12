<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\Course as CourseModel;
use app\model\Chapter;
use app\model\Knowledge;
use app\model\Question;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Course
{
    public function index()
    {
        $user = Session::get('user');
        $courses = CourseModel::where('teacher_id', $user['id'])->select();
        $courseCount = count($courses);
        
        View::assign([
            'user' => $user,
            'courses' => $courses,
            'courseCount' => $courseCount,
        ]);
        
        return View::fetch('teacher/course');
    }
    
    public function detail(Request $request)
    {
        $user = Session::get('user');
        $courseId = $request->param('id');
        
        $course = CourseModel::find($courseId);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return View::fetch('error', ['msg' => '课程不存在或无权访问']);
        }
        
        $chapters = Chapter::where('course_id', $courseId)->order('sort')->select();
        
        foreach ($chapters as &$chapter) {
            $chapter['knowledge_count'] = Knowledge::where('chapter_id', $chapter['id'])->count();
            $chapter['question_count'] = Question::where('chapter_id', $chapter['id'])->count();
        }
        
        View::assign([
            'user' => $user,
            'course' => $course,
            'chapters' => $chapters,
        ]);
        
        return View::fetch('teacher/course_detail');
    }
}

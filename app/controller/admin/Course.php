<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\Course as CourseModel;
use app\model\User;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Course
{
    public function index()
    {
        $user = Session::get('user');
        $courses = CourseModel::with('teacher')->select();
        $teachers = User::where('role', 2)->select();
        
        View::assign([
            'user' => $user,
            'courses' => $courses,
            'teachers' => $teachers,
        ]);
        
        return View::fetch('admin/course');
    }
    
    public function add(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            
            if (empty($data['name'])) {
                return Response::error('课程名称不能为空');
            }
            
            $course = CourseModel::create([
                'name' => $data['name'],
                'code' => $data['code'] ?? '',
                'description' => $data['description'] ?? '',
                'teacher_id' => $data['teacher_id'] ?? null,
                'sort' => $data['sort'] ?? 0,
                'status' => $data['status'] ?? 1,
            ]);
            
            if ($course) {
                return Response::success(null, '课程添加成功');
            }
            
            return Response::error('课程添加失败');
        }
        
        $teachers = User::where('role', 2)->select();
        
        View::assign([
            'teachers' => $teachers,
        ]);
        
        return View::fetch('admin/course_add');
    }
    
    public function edit(Request $request)
    {
        $id = $request->param('id');
        $course = CourseModel::find($id);
        
        if (!$course) {
            return Response::error('课程不存在');
        }
        
        if ($request->isPost()) {
            $data = $request->post();
            
            if (empty($data['name'])) {
                return Response::error('课程名称不能为空');
            }
            
            $result = $course->save([
                'name' => $data['name'],
                'code' => $data['code'] ?? '',
                'description' => $data['description'] ?? '',
                'teacher_id' => $data['teacher_id'] ?? null,
                'sort' => $data['sort'] ?? 0,
                'status' => $data['status'] ?? 1,
            ]);
            
            if ($result) {
                return Response::success(null, '课程修改成功');
            }
            
            return Response::error('课程修改失败');
        }
        
        $teachers = User::where('role', 2)->select();
        
        View::assign([
            'course' => $course,
            'teachers' => $teachers,
        ]);
        
        return View::fetch('admin/course_edit');
    }
    
    public function delete(Request $request)
    {
        $id = $request->post('id');
        $course = CourseModel::find($id);
        
        if (!$course) {
            return Response::error('课程不存在');
        }
        
        $result = $course->delete();
        
        if ($result) {
            return Response::success(null, '课程删除成功');
        }
        
        return Response::error('课程删除失败');
    }
}

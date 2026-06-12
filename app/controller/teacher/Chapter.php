<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\Chapter as ChapterModel;
use app\model\Course;
use app\common\Response;
use think\facade\Session;
use think\Request;

class Chapter
{
    public function save(Request $request)
    {
        $user = Session::get('user');
        $data = $request->post();
        
        if (empty($data['name'])) {
            return Response::error('章节名称不能为空');
        }
        
        if (empty($data['course_id'])) {
            return Response::error('课程ID不能为空');
        }
        
        $course = Course::find($data['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }
        
        if (!empty($data['id'])) {
            $chapter = ChapterModel::find($data['id']);
            if (!$chapter) {
                return Response::error('章节不存在');
            }
            
            $result = $chapter->save([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'sort' => $data['sort'] ?? 0,
            ]);
            
            if ($result) {
                return Response::success(null, '章节修改成功');
            }
            
            return Response::error('章节修改失败');
        }
        
        $chapter = ChapterModel::create([
            'course_id' => $data['course_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'sort' => $data['sort'] ?? 0,
        ]);
        
        if ($chapter) {
            return Response::success(null, '章节添加成功');
        }
        
        return Response::error('章节添加失败');
    }
    
    public function get(Request $request)
    {
        $user = Session::get('user');
        $id = $request->param('id');
        
        $chapter = ChapterModel::find($id);
        if (!$chapter) {
            return Response::error('章节不存在');
        }
        
        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }
        
        return Response::success($chapter);
    }
    
    public function delete(Request $request)
    {
        $user = Session::get('user');
        $id = $request->post('id');
        
        $chapter = ChapterModel::find($id);
        if (!$chapter) {
            return Response::error('章节不存在');
        }
        
        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }
        
        $result = $chapter->delete();
        
        if ($result) {
            return Response::success(null, '章节删除成功');
        }
        
        return Response::error('章节删除失败');
    }
}

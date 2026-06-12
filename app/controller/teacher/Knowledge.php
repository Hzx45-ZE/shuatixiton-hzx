<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\Knowledge as KnowledgeModel;
use app\model\Chapter;
use app\model\Course;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Knowledge
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        $chapterId = $request->param('chapter_id');

        $chapter = Chapter::find($chapterId);
        if (!$chapter) {
            return View::fetch('error', ['msg' => '章节不存在']);
        }

        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return View::fetch('error', ['msg' => '无权访问']);
        }

        $knowledges = KnowledgeModel::where('chapter_id', $chapterId)->order('sort')->select();

        View::assign([
            'user' => $user,
            'chapter' => $chapter,
            'course' => $course,
            'knowledges' => $knowledges,
        ]);

        return View::fetch('teacher/knowledge');
    }

    public function save(Request $request)
    {
        $user = Session::get('user');
        $data = $request->post();

        if (empty($data['name'])) {
            return Response::error('知识点名称不能为空');
        }

        if (empty($data['chapter_id'])) {
            return Response::error('章节ID不能为空');
        }

        $chapter = Chapter::find($data['chapter_id']);
        if (!$chapter) {
            return Response::error('章节不存在');
        }

        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }

        if (!empty($data['id'])) {
            $knowledge = KnowledgeModel::find($data['id']);
            if (!$knowledge) {
                return Response::error('知识点不存在');
            }

            $knowledge->save([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'sort' => $data['sort'] ?? 0,
            ]);

            return Response::success(null, '知识点修改成功');
        }

        KnowledgeModel::create([
            'chapter_id' => $data['chapter_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'sort' => $data['sort'] ?? 0,
        ]);

        return Response::success(null, '知识点添加成功');
    }

    public function get(Request $request)
    {
        $user = Session::get('user');
        $id = $request->param('id');

        $knowledge = KnowledgeModel::find($id);
        if (!$knowledge) {
            return Response::error('知识点不存在');
        }

        $chapter = Chapter::find($knowledge['chapter_id']);
        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }

        return Response::success($knowledge);
    }

    public function delete(Request $request)
    {
        $user = Session::get('user');
        $id = $request->post('id');

        $knowledge = KnowledgeModel::find($id);
        if (!$knowledge) {
            return Response::error('知识点不存在');
        }

        $chapter = Chapter::find($knowledge['chapter_id']);
        $course = Course::find($chapter['course_id']);
        if (!$course || $course['teacher_id'] != $user['id']) {
            return Response::error('无权操作');
        }

        $knowledge->delete();

        return Response::success(null, '知识点删除成功');
    }
}
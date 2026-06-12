<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\StudyMaterial;
use app\model\ClassInfo;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Material
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        
        $classId = $request->param('class_id');
        
        $classes = ClassInfo::where('teacher_id', $user['id'])->select();
        
        $query = StudyMaterial::where('creator_id', $user['id'])->where('status', 1);
        
        if ($classId) {
            $query->where('class_id', $classId);
        }
        
        $materials = $query->order('create_time', 'desc')->select();
        
        View::assign([
            'user' => $user,
            'classes' => $classes,
            'selectedClass' => $classId,
            'materials' => $materials,
        ]);
        
        return View::fetch('teacher/material');
    }
    
    public function add(Request $request)
    {
        $user = Session::get('user');
        
        if ($request->isPost()) {
            $data = $request->post();
            
            $material = StudyMaterial::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'type' => $data['type'],
                'class_id' => $data['class_id'] ?? null,
                'course_id' => $data['course_id'] ?? null,
                'chapter_id' => $data['chapter_id'] ?? null,
                'link_url' => $data['link_url'] ?? null,
                'creator_id' => $user['id'],
                'status' => 1,
            ]);
            
            return json(['code' => 1, 'msg' => '发布成功', 'url' => '/teacher/materials']);
        }
        
        $classes = ClassInfo::where('teacher_id', $user['id'])->select();
        
        View::assign([
            'user' => $user,
            'classes' => $classes,
        ]);
        
        return View::fetch('teacher/material_add');
    }
    
    public function delete(Request $request)
    {
        $user = Session::get('user');
        $materialId = $request->param('material_id');
        
        $material = StudyMaterial::find($materialId);
        if (!$material || $material['creator_id'] != $user['id']) {
            return json(['code' => 0, 'msg' => '权限不足']);
        }
        
        $material->status = 0;
        $material->save();
        
        return json(['code' => 1, 'msg' => '删除成功']);
    }
}
<?php
declare(strict_types=1);

namespace app\controller\student;

use app\model\StudyMaterial;
use app\model\UserClass;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Material
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        
        $userClass = UserClass::where('user_id', $user['id'])->find();
        $classId = $userClass ? $userClass['class_id'] : null;
        
        $query = StudyMaterial::where('status', 1);
        
        $query->where(function($q) use ($classId) {
            $q->where('class_id', '=', null)->whereOr('class_id', '=', $classId);
        });
        
        $materials = $query->order('create_time', 'desc')->select();
        
        View::assign([
            'user' => $user,
            'materials' => $materials,
        ]);
        
        return View::fetch('student/material');
    }
}
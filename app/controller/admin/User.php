<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\User as UserModel;
use app\common\Response;
use think\facade\View;
use think\facade\Session;
use think\Request;

class User
{
    public function index()
    {
        $user = Session::get('user');
        
        $students = UserModel::where('role', 1)->select();
        $teachers = UserModel::where('role', 2)->select();
        
        View::assign([
            'user' => $user,
            'students' => $students,
            'teachers' => $teachers,
        ]);
        
        return View::fetch('admin/user');
    }
    
    public function resetPassword(Request $request)
    {
        $userId = $request->post('user_id');
        $newPassword = $request->post('new_password');
        
        if (empty($userId) || empty($newPassword)) {
            return Response::error('参数错误');
        }
        
        if (strlen($newPassword) < 6) {
            return Response::error('密码长度不能少于6位');
        }
        
        $user = UserModel::find($userId);
        if (!$user) {
            return Response::error('用户不存在');
        }
        
        $user->password = $newPassword;
        $result = $user->save();
        
        if ($result) {
            return Response::success(null, '密码重置成功');
        }
        
        return Response::error('密码重置失败');
    }
}

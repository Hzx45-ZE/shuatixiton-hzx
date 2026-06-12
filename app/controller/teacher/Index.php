<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\User;
use app\model\Course;
use app\model\ClassInfo;
use app\model\ClassStudent;
use app\model\StudyTask;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Index
{
    public function index()
    {
        $user = Session::get('user');
        
        $classes = ClassInfo::where('teacher_id', $user['id'])->select();
        $classCount = count($classes);
        $courseCount = Course::where('teacher_id', $user['id'])->count();
        $taskCount = StudyTask::where('teacher_id', $user['id'])->count();
        
        $studentCount = 0;
        foreach ($classes as $class) {
            $studentCount += ClassStudent::where('class_id', $class['id'])->where('status', 1)->count();
        }
        
        View::assign([
            'user' => $user,
            'classes' => $classes,
            'classCount' => $classCount,
            'courseCount' => $courseCount,
            'studentCount' => $studentCount,
            'taskCount' => $taskCount,
        ]);
        
        return View::fetch('teacher/index');
    }

    public function profile()
    {
        $user = Session::get('user');
        $userInfo = User::find($user['id']);
        
        View::assign([
            'user' => $userInfo,
        ]);
        
        return View::fetch('teacher/profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Session::get('user');
        $data = $request->post();
        
        $updateData = [];
        if (isset($data['nickname'])) {
            $updateData['nickname'] = trim($data['nickname']);
        }
        if (isset($data['phone'])) {
            $updateData['phone'] = trim($data['phone']);
        }
        if (isset($data['email'])) {
            $updateData['email'] = trim($data['email']);
        }
        if (isset($data['gender'])) {
            $updateData['gender'] = (int)$data['gender'];
        }
        
        if (empty($updateData)) {
            return json(['code' => 0, 'msg' => '没有需要修改的信息']);
        }
        
        User::where('id', $user['id'])->update($updateData);
        $newUser = User::find($user['id'])->toArray();
        Session::set('user', $newUser);
        
        return json(['code' => 1, 'msg' => '个人信息修改成功']);
    }

    public function updatePassword(Request $request)
    {
        $user = Session::get('user');
        $oldPassword = $request->post('old_password');
        $newPassword = $request->post('new_password');
        $confirmPassword = $request->post('confirm_password');
        
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            return json(['code' => 0, 'msg' => '请填写完整信息']);
        }
        
        if ($newPassword !== $confirmPassword) {
            return json(['code' => 0, 'msg' => '两次输入的新密码不一致']);
        }
        
        if (strlen($newPassword) < 6) {
            return json(['code' => 0, 'msg' => '新密码长度不能少于6位']);
        }
        
        $userModel = User::find($user['id']);
        if (!$userModel->verifyPassword($oldPassword)) {
            return json(['code' => 0, 'msg' => '原密码错误']);
        }
        
        $userModel->password = $newPassword;
        $userModel->save();
        
        return json(['code' => 1, 'msg' => '密码修改成功，请重新登录']);
    }
}

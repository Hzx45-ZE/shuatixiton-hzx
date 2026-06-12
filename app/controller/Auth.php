<?php
declare(strict_types=1);

namespace app\controller;

use app\model\User;
use think\facade\Session;
use think\facade\View;
use think\Request;
use think\response\Json;

class Auth
{
    public function login()
    {
        if (Session::has('user')) {
            return redirect('/home');
        }
        return View::fetch('auth/login');
    }

    public function adminLogin()
    {
        if (Session::has('user')) {
            $user = Session::get('user');
            if ($user['role'] === 3) {
                return redirect('/admin');
            }
        }
        return View::fetch('auth/admin_login');
    }

    public function register()
    {
        if (Session::has('user')) {
            return redirect('/home');
        }
        return View::fetch('auth/register');
    }

    public function captcha()
    {
        $width = 120;
        $height = 40;
        $code = $this->generateCode(4);
        
        Session::set('captcha', strtolower($code));
        
        $image = imagecreatetruecolor($width, $height);
        
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);
        
        for ($i = 0; $i < 50; $i++) {
            $color = imagecolorallocate($image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
            imagesetpixel($image, mt_rand(0, $width), mt_rand(0, $height), $color);
        }
        
        for ($i = 0; $i < 5; $i++) {
            $color = imagecolorallocate($image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $color);
        }
        
        $fontSize = 18;
        
        for ($i = 0; $i < strlen($code); $i++) {
            $color = imagecolorallocate($image, mt_rand(50, 150), mt_rand(50, 150), mt_rand(50, 150));
            $angle = mt_rand(-15, 15);
            $x = 20 + $i * 25;
            $y = 28;
            imagestring($image, 5, $x, 10, $code[$i], $color);
        }
        
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    private function generateCode($length = 4)
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $code;
    }

    public function doLogin(Request $request): Json
    {
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        
        if (empty($username) || empty($password)) {
            return json(['code' => 0, 'msg' => '请输入账号和密码']);
        }
        
        $user = User::where('username', $username)->find();
        
        if (!$user) {
            return json(['code' => 0, 'msg' => '账号不存在']);
        }
        
        if ($user->status != 1) {
            return json(['code' => 0, 'msg' => '账号已被禁用']);
        }
        
        if (!password_verify($password, $user->password)) {
            return json(['code' => 0, 'msg' => '密码错误']);
        }
        
        if ($user->role == 3) {
            return json(['code' => 0, 'msg' => '请使用管理员登录入口']);
        }
        
        Session::set('user', [
            'id' => $user->id,
            'username' => $user->username,
            'real_name' => $user->real_name,
            'role' => $user->role,
        ]);
        
        User::where('id', $user->id)->update(['last_login_time' => date('Y-m-d H:i:s')]);
        
        return json(['code' => 1, 'msg' => '登录成功', 'url' => '/home']);
    }

    public function doAdminLogin(Request $request): Json
    {
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        $captcha = strtolower($request->post('captcha', ''));
        
        if (empty($username) || empty($password) || empty($captcha)) {
            return json(['code' => 0, 'msg' => '请输入账号、密码和验证码']);
        }
        
        if (Session::get('captcha') != $captcha) {
            return json(['code' => 0, 'msg' => '验证码错误']);
        }
        
        $user = User::where('username', $username)->find();
        
        if (!$user) {
            return json(['code' => 0, 'msg' => '账号不存在']);
        }
        
        if ($user->role != 3) {
            return json(['code' => 0, 'msg' => '非管理员账号']);
        }
        
        if ($user->status != 1) {
            return json(['code' => 0, 'msg' => '账号已被禁用']);
        }
        
        if (!password_verify($password, $user->password)) {
            return json(['code' => 0, 'msg' => '密码错误']);
        }
        
        Session::set('user', [
            'id' => $user->id,
            'username' => $user->username,
            'real_name' => $user->real_name,
            'role' => $user->role,
        ]);
        
        User::where('id', $user->id)->update(['last_login_time' => date('Y-m-d H:i:s')]);
        
        return json(['code' => 1, 'msg' => '登录成功', 'url' => '/admin']);
    }

    public function doRegister(Request $request): Json
    {
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        $confirmPassword = $request->post('confirm_password', '');
        $realName = $request->post('real_name', '');
        $phone = $request->post('phone', '');
        $captcha = strtolower($request->post('captcha', ''));
        $role = (int)$request->post('role', 1);
        
        if (empty($username) || empty($password) || empty($realName)) {
            return json(['code' => 0, 'msg' => '请填写必填项']);
        }
        
        if ($password != $confirmPassword) {
            return json(['code' => 0, 'msg' => '两次密码不一致']);
        }
        
        if (strlen($password) < 6) {
            return json(['code' => 0, 'msg' => '密码长度不能少于6位']);
        }
        
        if (Session::get('captcha') != $captcha) {
            return json(['code' => 0, 'msg' => '验证码错误']);
        }
        
        if (User::where('username', $username)->find()) {
            return json(['code' => 0, 'msg' => '账号已存在']);
        }
        
        $user = User::create([
            'username' => $username,
            'password' => $password,
            'real_name' => $realName,
            'phone' => $phone,
            'role' => $role,
            'status' => 1,
        ]);
        
        if ($user) {
            return json(['code' => 1, 'msg' => '注册成功', 'url' => '/login']);
        }
        
        return json(['code' => 0, 'msg' => '注册失败']);
    }

    public function logout()
    {
        Session::clear();
        return redirect('/login');
    }

    public function adminLogout()
    {
        Session::clear();
        return redirect('/admin/login');
    }
}
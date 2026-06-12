<?php
// 后台登录校验中间件

declare(strict_types=1);

namespace app\middleware;

use think\facade\Session;
//用于拦截后台请求，校验管理员登录状态与角色权限，在控制器执行前统一校验
class CheckAdminLogin
{
    public function handle($request, \Closure $next)
    {
        $user = Session::get('user');  //：从 SESSION 读取登录存入的用户信息

        if (!$user) {
            return redirect('/admin/login');
        }

        if ($user['role'] != 3) {
            return redirect('/login'); //已登录但角色编号不等于 3（非管理员），跳转前端登录 /login
        }

        return $next($request);
    }
}
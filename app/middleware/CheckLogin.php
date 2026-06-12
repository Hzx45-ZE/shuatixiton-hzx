<?php
declare(strict_types=1);

namespace app\middleware;

use think\facade\Session;
// 前端登录校验中间件
// 用于拦截前端请求，校验用户登录状态，在控制器执行前统一校验
class CheckLogin
{
    public function handle($request, \Closure $next)
    {
        $user = Session::get('user');  //：从 SESSION 读取登录存入的用户信息

        if (!$user) {
            return redirect('/login'); //未登录，跳转前端登录 /login
        }

        return $next($request); //已登录，继续执行后续操作
    }
}
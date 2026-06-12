<?php
declare(strict_types=1);

namespace app\controller;

use think\Request;
use think\Response;

/**
 * 错误控制器
 * ThinkPHP 异常处理链会路由到此控制器，统一返回 JSON 避免 HTML 污染 API 响应
 */
class Error
{
    public function __call($method, $args)
    {
        $request = app('request');
        
        // AJAX/JSON 请求返回 JSON
        if ($request->isAjax() || $request->isJson()) {
            return json(['code' => 0, 'msg' => '服务器内部错误，请稍后重试']);
        }
        
        // 普通请求返回 HTML 错误页
        return Response::create('页面错误！请稍后再试～', 'html', 500);
    }
}
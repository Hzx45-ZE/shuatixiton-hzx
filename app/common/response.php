<?php
declare(strict_types=1);

namespace app\common;

use think\response\Json;

class Response
{
    public static function success($data = null, string $msg = '操作成功'): Json
    {
        return json([
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'time' => time(),
        ]);
    }

    public static function error(string $msg = '操作失败', int $code = 0): Json
    {
        return json([
            'code' => $code,
            'msg'  => $msg,
            'data' => null,
            'time' => time(),
        ]);
    }

    public static function paginate($list, $total, $page = 1, $limit = 20): Json
    {
        return json([
            'code'  => 1,
            'msg'   => 'success',
            'data'  => $list,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'time'  => time(),
        ]);
    }
}

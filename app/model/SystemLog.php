<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 系统日志表 system_log
 */

class SystemLog extends Model
{
    protected $name = 'system_log';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function record(
        string $module,
        string $action,
        ?int $userId = null,
        ?string $url = null,
        ?string $method = null,
        ?string $params = null,
        ?string $ip = null,
        ?string $userAgent = null,
        ?int $responseCode = null,
        ?string $errorMsg = null,
        ?int $executionTime = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'module' => $module,
            'action' => $action,
            'method' => $method,
            'url' => $url,
            'params' => $params,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'response_code' => $responseCode,
            'error_msg' => $errorMsg,
            'execution_time' => $executionTime,
        ]);
    }
}

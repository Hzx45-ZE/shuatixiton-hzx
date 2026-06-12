<?php
namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 记录异常详细信息用于调试
        try {
            $logDir = app()->getRuntimePath();
            if ($logDir) {
                $logFile = $logDir . 'exception_handle.log';
                $logMsg = '[' . date('Y-m-d H:i:s') . '] ' . get_class($e) . ': ' . $e->getMessage() . "\n";
                $logMsg .= 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
                $logMsg .= "AJAX: " . ($request->isAjax() ? 'yes' : 'no') . " JSON: " . ($request->isJson() ? 'yes' : 'no') . "\n";
                $logMsg .= "URL: " . $request->url() . " Method: " . $request->method() . "\n";
                $logMsg .= $e->getTraceAsString() . "\n\n";
                @file_put_contents($logFile, $logMsg, FILE_APPEND);
            }
        } catch (\Throwable $ignore) {}

        try {
            // 对 AJAX/JSON 请求统一返回 JSON
            if ($request->isAjax() || $request->isJson()) {
                return json([
                    'code' => 0,
                    'msg'  => $this->getExceptionMessage($e),
                ]);
            }
        } catch (\Throwable $inner) {
            // 如果 render 自身也抛异常，兜底返回纯 JSON 字符串
            return Response::create(
                json_encode(['code' => 0, 'msg' => '服务器内部错误，请稍后重试'], JSON_UNESCAPED_UNICODE),
                200,
                ['Content-Type' => 'application/json; charset=utf-8']
            );
        }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
    
    private function getExceptionMessage(Throwable $e): string
    {
        if ($e instanceof ValidateException) {
            return $e->getMessage();
        }
        if ($e instanceof \TypeError || $e instanceof \Error) {
            return '服务器内部错误，请稍后重试';
        }
        $msg = $e->getMessage();
        return $msg ?: '服务器内部错误，请稍后重试';
    }
}

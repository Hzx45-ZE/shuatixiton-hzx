<?php
namespace app\controller;

use app\BaseController;
use app\service\AiService;

class Index extends BaseController
{
    public function index()
    {
        return redirect('/login');
    }

    public function testai()
    {
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>AI 测试</title>";
        echo "<style>body{font-family:'Microsoft YaHei',sans-serif;max-width:900px;margin:20px auto;padding:20px;background:#f5f5f5;}";
        echo "h2{color:#333;border-bottom:2px solid #667eea;padding-bottom:8px;}";
        echo ".card{background:white;border-radius:8px;padding:15px;margin:10px 0;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
        echo ".ok{color:#52c41a;}" . ".err{color:#ff4d4f;}";
        echo "pre{background:#f0f0f0;padding:10px;border-radius:4px;overflow-x:auto;font-size:12px;}</style>";
        echo "</head><body>";
        echo "<h1>AI 服务测试</h1>";

        echo "<div class='card'><h2>1. 配置读取</h2><table border='0' cellpadding='5'>";
        $config = config('ai');
        $checks = [
            'provider' => $config['provider'] ?? 'N/A',
            'api_key长度' => strlen($config['api_key'] ?? ''),
            'api_key前10位' => substr($config['api_key'] ?? '', 0, 10) . '...',
            'base_url' => $config['base_url'] ?? 'N/A',
            'model' => $config['model'] ?? 'N/A',
        ];
        foreach ($checks as $k => $v) {
            $color = ($v === 'N/A' || $v === '') ? 'err' : 'ok';
            echo "<tr><td><b>$k</b></td><td class='$color'>$v</td></tr>";
        }
        echo "</table></div>";

        echo "<div class='card'><h2>2. AI 服务状态</h2>";
        $service = new AiService();
        $isConfigured = $service->isConfigured();
        echo "<p>isConfigured(): <b class='" . ($isConfigured ? 'ok' : 'err') . "'>" . ($isConfigured ? 'true (已配置)' : 'false (未配置)') . "</b></p>";
        echo "</div>";

        echo "<div class='card'><h2>3. API 连通性测试</h2><pre>";
        set_time_limit(120);
        $result = $service->chat(
            '你是一个助手，用JSON回答。',
            '说"你好"并返回JSON: {"msg": "你好，世界"}',
            ['temperature' => 0.1, 'max_tokens' => 100]
        );
        if ($result['success']) {
            echo "<span class='ok'>API 调用成功!</span>\n" . htmlspecialchars($result['content']);
        } else {
            echo "<span class='err'>API 调用失败!</span>\n" . htmlspecialchars($result['error'] ?? '未知错误');
        }
        echo "</pre></div>";

        if ($isConfigured && $result['success']) {
            echo "<div class='card'><h2>4. 出题功能测试</h2><pre>";
            $questions = $service->generateQuestions('计算机网络', 'single_choice', 'medium', 2);
            if ($questions['success']) {
                echo "<span class='ok'>出题成功!</span>\n";
                print_r($questions['data']);
            } else {
                echo "<span class='err'>出题失败!</span>\n" . ($questions['error'] ?? '未知错误');
                if (isset($questions['data']['raw'])) {
                    echo "\n原始返回:\n" . htmlspecialchars($questions['data']['raw']);
                }
            }
            echo "</pre></div>";
        }

        echo "</body></html>";
        exit;
    }
}
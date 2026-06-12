<?php
declare(strict_types=1);

namespace app\service;

class AiService
{
    private $apiKey;
    private $baseUrl;
    private $model;
    private $timeout;
    private $temperature;
    private $maxTokens;

    public function __construct()
    {
        $config = config('ai');
        $this->apiKey      = $config['api_key'];
        $this->baseUrl     = rtrim($config['base_url'], '/');
        $this->model       = $config['model'];
        $this->temperature = (float) ($config['temperature'] ?? 0.7);
        $this->maxTokens   = (int) ($config['max_tokens'] ?? 4096);
        $this->timeout     = (int) ($config['timeout'] ?? 120);
    }

    /**
     * 检查 AI 服务是否已配置
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }

    /**
     * 调用大模型 Chat Completion 接口
     */
    public function chat(string $systemPrompt, string $userPrompt, array $options = []): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'AI 服务未配置，请在 config/ai.php 或 .env 中设置 api_key'];
        }

        $url = $this->baseUrl . '/chat/completions';

        $body = [
            'model'       => $options['model'] ?? $this->model,
            'temperature' => (float) ($options['temperature'] ?? $this->temperature),
            'max_tokens'  => (int) ($options['max_tokens'] ?? $this->maxTokens),
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userPrompt],
            ],
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_TIMEOUT        => (int) ($options['timeout'] ?? $this->timeout),
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_NOSIGNAL       => true,
            CURLOPT_FORBID_REUSE   => true,
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        $sslVerify = curl_getinfo($ch, CURLINFO_SSL_VERIFYRESULT);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        
        // 彻底释放 CURL 句柄，防止 Windows 下连接复用导致后续请求挂起
        curl_reset($ch);
        curl_close($ch);
        unset($ch);

        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            date('Y-m-d H:i:s') . " CURL Debug: url=$url, code=$httpCode, sslVerify=$sslVerify, contentType=$contentType, error='$error'\n",
            FILE_APPEND
        );

        if ($error) {
            return ['success' => false, 'error' => 'AI 服务请求失败: ' . $error];
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200 || isset($data['error'])) {
            return ['success' => false, 'error' => $data['error']['message'] ?? 'AI 服务返回错误'];
        }

        return [
            'success' => true,
            'content' => $data['choices'][0]['message']['content'] ?? '',
        ];
    }

    // ========== AI 随机出题 ==========

    /**
     * AI 根据知识点/章节随机生成题目
     *
     * @param string $subject     学科 / 知识点
     * @param string $type        题型: single_choice / multiple_choice / fill_blank / short_answer
     * @param string $difficulty  难度: easy / medium / hard
     * @param int    $count       生成数量
     */
    public function generateQuestions(string $subject, string $type = 'single_choice', string $difficulty = 'medium', int $count = 1): array
    {
        $typeMap = [
            'single_choice'   => '单选题',
            'multiple_choice' => '多选题',
            'judgment'        => '判断题',
            'fill_blank'      => '填空题',
            'short_answer'    => '简答题',
        ];

        $typeName = $typeMap[$type] ?? '单选题';

        if ($type === 'judgment') {
            $systemPrompt = <<<'PROMPT'
你是一个专业的信息技术课程出题老师。请严格按照用户要求生成判断题，并以 JSON 格式输出。
判断题只有两个选项：正确和错误。
输出格式要求：
{
  "questions": [
    {
      "title": "题目内容（一个陈述句，判断其正误）",
      "type": "判断题",
      "difficulty": "难度",
      "options": [
        {"key": "A", "content": "正确"},
        {"key": "B", "content": "错误"}
      ],
      "answer": "A表示正确，B表示错误",
      "analysis": "题目解析，说明为什么正确或错误",
      "score": 5
    }
  ]
}
PROMPT;
        } elseif ($type === 'fill_blank') {
            $systemPrompt = <<<'PROMPT'
你是一个专业的信息技术课程出题老师。请严格按照用户要求生成填空题，并以 JSON 格式输出。
填空题的题目中应包含下划线或括号表示填空位置，答案用分号分隔多个填空。
输出格式要求：
{
  "questions": [
    {
      "title": "MySQL默认监听的端口号是______，其默认字符集是______。",
      "type": "填空题",
      "difficulty": "难度",
      "options": [],
      "answer": "3306;utf8",
      "analysis": "题目解析",
      "score": 5
    }
  ]
}
注意：填空题的 options 必须为空数组 []，answer 中多个填空答案用英文分号 ; 分隔。
PROMPT;
        } elseif ($type === 'short_answer') {
            $systemPrompt = <<<'PROMPT'
你是一个专业的信息技术课程出题老师。请严格按照用户要求生成简答题，并以 JSON 格式输出。
简答题要求学生用文字回答，不需要选项。
输出格式要求：
{
  "questions": [
    {
      "title": "请简述MySQL中主键和外键的区别。",
      "type": "简答题",
      "difficulty": "难度",
      "options": [],
      "answer": "参考答案内容",
      "analysis": "题目解析",
      "score": 5
    }
  ]
}
注意：简答题的 options 必须为空数组 []，answer 为参考答案文本。
PROMPT;
        } elseif ($type === 'multiple_choice') {
            $systemPrompt = <<<'PROMPT'
你是一个专业的信息技术课程出题老师。请严格按照用户要求生成多选题，并以 JSON 格式输出。
多选题有4个选项（A、B、C、D），正确答案可能是一个或多个字母的组合，如 "AB" 或 "ACD"。
输出格式要求：
{
  "questions": [
    {
      "title": "题目内容",
      "type": "多选题",
      "difficulty": "难度",
      "options": [
        {"key": "A", "content": "选项A内容"},
        {"key": "B", "content": "选项B内容"},
        {"key": "C", "content": "选项C内容"},
        {"key": "D", "content": "选项D内容"}
      ],
      "answer": "ACD",
      "analysis": "题目解析，说明每个选项为什么对或错",
      "score": 5
    }
  ]
}
注意：多选题的 answer 是正确选项字母的组合，如 "AB"、"ACD" 等，不要加空格或其他分隔符。
PROMPT;
        } else {
            // 单选题（默认）
            $systemPrompt = <<<'PROMPT'
你是一个专业的信息技术课程出题老师。请严格按照用户要求生成单选题，并以 JSON 格式输出。
单选题有4个选项（A、B、C、D），只有一个正确答案。
输出格式要求：
{
  "questions": [
    {
      "title": "题目内容",
      "type": "单选题",
      "difficulty": "难度",
      "options": [
        {"key": "A", "content": "选项A内容"},
        {"key": "B", "content": "选项B内容"},
        {"key": "C", "content": "选项C内容"},
        {"key": "D", "content": "选项D内容"}
      ],
      "answer": "A",
      "analysis": "题目解析，说明为什么选这个答案",
      "score": 5
    }
  ]
}
注意：单选题的 answer 只有一个字母，如 "A"、"B"、"C" 或 "D"。
PROMPT;
        }

        $userPrompt = "请生成 {$count} 道关于「{$subject}」的{$typeName}，难度为{$difficulty}。";

        $result = $this->chat($systemPrompt, $userPrompt, [
            'temperature' => 0.8,
            'max_tokens'  => 4096,
        ]);

        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            date('Y-m-d H:i:s') . " generateQuestions chat result: success=" . ($result['success'] ? 'true' : 'false') . ", content_len=" . strlen($result['content'] ?? '') . "\n",
            FILE_APPEND
        );

        if (!$result['success']) {
            return $result;
        }

        $parsed = $this->parseJson($result['content']);
        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            date('Y-m-d H:i:s') . " parseJson result: " . json_encode($parsed, JSON_UNESCAPED_UNICODE) . "\n",
            FILE_APPEND
        );
        return ['success' => true, 'data' => $parsed];
    }

    // ========== AI 批改 ==========

    /**
     * AI 批改主观题（简答题/论述题）
     *
     * @param string $question      题目内容
     * @param string $referenceAnswer 参考答案
     * @param string $userAnswer    学生答案
     * @param int    $totalScore    满分
     */
    public function gradeAnswer(string $question, string $referenceAnswer, string $userAnswer, int $totalScore = 10): array
    {
        $systemPrompt = <<<PROMPT
你是一个专业的阅卷老师，请根据题目、参考答案和学生作答内容进行评分。
评分规则：
1. 完全正确或意思相同给满分
2. 部分正确按比例给分
3. 完全错误或未作答给 0 分
输出格式为 JSON：
{
  "score": 8,
  "is_correct": true,
  "comment": "评语：学生对核心概念理解正确，但缺少部分细节描述。"
}
PROMPT;

        $userPrompt = "题目：{$question}\n参考答案：{$referenceAnswer}\n学生答案：{$userAnswer}\n满分：{$totalScore}分\n请评分。";

        // 评分使用较短超时，避免长时间等待
        $result = $this->chat($systemPrompt, $userPrompt, [
            'temperature' => 0.3,
            'max_tokens'  => 1024,
            'timeout'     => 30,  // 30秒超时
        ]);

        if (!$result['success']) {
            return $result;
        }

        $parsed = $this->parseJson($result['content']);
        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            date('Y-m-d H:i:s') . " parseJson result: " . json_encode($parsed, JSON_UNESCAPED_UNICODE) . "\n",
            FILE_APPEND
        );
        return ['success' => true, 'data' => $parsed];
    }

    // ========== AI 学习报告 ==========

    /**
     * AI 生成学习报告
     *
     * @param array $stats 统计数据 [practiceCount, examCount, errorCount, correctRate, avgScore, chapters, ...]
     */
    public function generateReport(array $stats): array
    {
        $systemPrompt = <<<PROMPT
你是一个专业的学习分析师，请根据学生的学习数据生成一份学习报告。
报告应包含以下部分：
1. 总体评价：简要概括学习情况
2. 优势分析：学生在哪些方面表现较好
3. 薄弱环节：学生在哪些方面需要加强
4. 学习建议：具体的改进建议
输出格式为 JSON：
{
  "summary": "总体评价内容",
  "strengths": ["优势1", "优势2"],
  "weaknesses": ["薄弱点1", "薄弱点2"],
  "suggestions": ["建议1", "建议2"]
}
PROMPT;

        $userPrompt = "以下是学生的学习数据，请生成学习报告：\n" . json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $result = $this->chat($systemPrompt, $userPrompt, [
            'temperature' => 0.5,
            'max_tokens'  => 2048,
        ]);

        if (!$result['success']) {
            return $result;
        }

        $parsed = $this->parseJson($result['content']);
        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            date('Y-m-d H:i:s') . " parseJson result: " . json_encode($parsed, JSON_UNESCAPED_UNICODE) . "\n",
            FILE_APPEND
        );
        return ['success' => true, 'data' => $parsed];
    }

    /**
     * 解析 AI 返回的 JSON（处理 markdown 代码块包裹的情况）
     */
    private function parseJson(string $content)
    {
        $content = trim($content);
        file_put_contents(
            app()->getRuntimePath() . 'ai_generate_log.txt',
            date('Y-m-d H:i:s') . " parseJson input (first 500 chars): " . substr($content, 0, 500) . "\n",
            FILE_APPEND
        );

        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            file_put_contents(
                app()->getRuntimePath() . 'ai_generate_log.txt',
                date('Y-m-d H:i:s') . " JSON decode error: " . json_last_error_msg() . "\n",
                FILE_APPEND
            );
            return ['raw' => $content, 'error' => 'JSON 解析失败'];
        }

        return $decoded;
    }
}
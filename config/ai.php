<?php

return [
    // AI 服务提供商: openai / deepseek / qwen / zhipu
    'provider'  => env('ai.provider', 'deepseek'),

    // API 密钥
    'api_key'   => env('ai.api_key', ''),

    // API 基础地址
    'base_url'  => env('ai.base_url', 'https://api.deepseek.com/v1'),

    // 模型名称
    'model'     => env('ai.model', 'deepseek-chat'),

    // 请求超时（秒）
    'timeout'   => env('ai.timeout', 60),

    // 温度参数 (0-2, 越低越确定)
    'temperature' => env('ai.temperature', 0.7),

    // 最大 Token 数
    'max_tokens' => env('ai.max_tokens', 4096),

    // ---- AI 出题配置 ----
    'generate_question_enable' => env('ai.generate_question_enable', true),
    'generate_question_max'    => env('ai.generate_question_max', 5),

    // ---- AI 批改配置 ----
    'grade_enable'    => env('ai.grade_enable', true),
    'grade_auto'      => env('ai.grade_auto', false),

    // ---- AI 学习报告配置 ----
    'report_enable' => env('ai.report_enable', true),
];
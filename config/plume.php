<?php

return [
    'is_enabled' => env('PLUME_IS_ENABLED',  false), # 是否启用
    'app_name' => env('PLUME_APP_NAME', ''), # 应用名称
    'driver' => env('PLUME_DRIVER', 'redis'), # plume_log服务 模式 支持 http, redis, kafka
    'queue_name' => env('PLUME_QUEUE_NAME', 'plume_log_list'),
    'trace_deep' => env('PLUME_TRACE_DEEP', 8),
    'http' => [
        'host' => env('PLUME_HTTP_HOST', '127.0.0.1:8891'),
    ],
    'redis' => [
        'prefix' => '',
        'timeout' => 1,
        'url' => env('PLUME_REDIS_URL'),
        'host' => env('PLUME_REDIS_HOST', '127.0.0.1'),
        'password' => env('PLUME_REDIS_PASSWORD', ''),
        'port' => env('PLUME_REDIS_PORT', '6379'),
        'database' => env('PLUME_REDIS_DB', '0'),
    ],
    'kafka' => [
        'host' => env('PLUME_KAFKA_HOST', '127.0.0.1:9092'),
        'timeout' => env('PLUME_KAFKA_TIMEOUT', 1),
    ]
];

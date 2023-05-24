<?php

return [
    'is_enabled' => env('PLUME_IS_ENABLED',  false),
    'app_name' => env('PLUME_APP_NAME', ''),
    'redis' => [
        'prefix' => '',
        'timeout' => 1,
        'url' => env('PLUME_REDIS_URL'),
        'host' => env('PLUME_REDIS_HOST', '127.0.0.1'),
        'password' => env('PLUME_REDIS_PASSWORD', ''),
        'port' => env('PLUME_REDIS_PORT', '6379'),
        'database' => env('PLUME_REDIS_DB', '0'),
    ],
];

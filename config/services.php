<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'tenant_app' => [
        'url' => env('TENANT_APP_URL', 'http://localhost:8000'),
        'api_path_prefix' => env('TENANT_APP_API_PATH_PREFIX', 'api/admin'),
        'api_token' => env('TENANT_APP_API_TOKEN'),
        'timeout' => (int) env('TENANT_APP_TIMEOUT', 30),
        'timeout_max_cap' => (int) env('TENANT_APP_TIMEOUT_MAX_CAP', 15),
        'connect_timeout' => (int) env('TENANT_APP_CONNECT_TIMEOUT', 5),
        'dashboard_timeout' => (int) env('TENANT_APP_DASHBOARD_TIMEOUT', 8),
        'retry_attempts' => (int) env('TENANT_APP_RETRY_ATTEMPTS', 2),
        'retry_delay_ms' => env('TENANT_APP_RETRY_DELAY_MS', 250),
        'down_ttl_seconds' => env('TENANT_APP_DOWN_TTL_SECONDS', 60),
    ],

];

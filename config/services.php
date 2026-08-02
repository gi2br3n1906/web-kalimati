<?php

declare(strict_types=1);

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
        'token' => env('POSTMARK_TOKEN'),
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

    'llm' => [
        'provider' => env('LLM_PROVIDER', 'rag'),
        'url' => env('LLM_SERVICE_URL'),
        'api_key' => env('LLM_SERVICE_API_KEY'),
        'timeout' => (int) env('LLM_SERVICE_TIMEOUT', 15),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
        'url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/interactions'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 30),
    ],

    'iot' => [
        'webhook_secret' => env('IOT_WEBHOOK_SECRET'),
    ],

];

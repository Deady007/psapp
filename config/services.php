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

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'chunk_model' => env('GEMINI_CHUNK_MODEL', 'gemini-2.0-flash-lite'),
        'merge_model' => env('GEMINI_MERGE_MODEL', 'gemini-2.0-flash'),
        'refine_model' => env('GEMINI_REFINE_MODEL', 'gemini-2.5-flash-lite'),
        'heavy_model' => env('GEMINI_HEAVY_MODEL', 'gemini-2.5-pro'),
        'chunk_size' => env('GEMINI_CHUNK_SIZE', 12000),
        'max_chunks' => env('GEMINI_MAX_CHUNKS', 3),
        'refine_passes' => env('GEMINI_REFINE_PASSES', 1),
        'requirements_output_tokens' => env('GEMINI_REQUIREMENTS_OUTPUT_TOKENS', 4096),
        'merge_output_tokens' => env('GEMINI_MERGE_OUTPUT_TOKENS', 6144),
        'heavy_output_tokens' => env('GEMINI_HEAVY_OUTPUT_TOKENS', 8192),
        'single_pass_max_chars' => env('GEMINI_SINGLE_PASS_MAX_CHARS', 80000),
        'heavy_min_chars' => env('GEMINI_HEAVY_MIN_CHARS', 120000),
        'heavy_min_requirements' => env('GEMINI_HEAVY_MIN_REQUIREMENTS', 60),
        'endpoint' => env('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta'),
        'timeout' => env('GEMINI_TIMEOUT', 45),
        'verify' => env('GEMINI_SSL_VERIFY', storage_path('certs/cacert.pem')),
    ],

];

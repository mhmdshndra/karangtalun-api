<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile
    |--------------------------------------------------------------------------
    */

    'turnstile' => [
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
        'verify_url' => env('TURNSTILE_VERIFY_URL', 'https://challenges.cloudflare.com/turnstile/v0/siteverify'),
        'enabled' => env('TURNSTILE_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | OTP / Password Reset
    |--------------------------------------------------------------------------
    */

    'otp' => [
        'channel' => env('OTP_CHANNEL', 'log'),
        'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 10),
        'max_attempts' => env('OTP_MAX_ATTEMPTS', 5),
        'reset_token_expiry_minutes' => env('RESET_TOKEN_EXPIRY_MINUTES', 15),
        'debug' => env('OTP_DEBUG', false),
        'whatsapp_api_url' => env('WHATSAPP_API_URL'),
        'whatsapp_api_token' => env('WHATSAPP_API_TOKEN'),
        'sms_api_url' => env('SMS_API_URL'),
        'sms_api_token' => env('SMS_API_TOKEN'),
    ],

];

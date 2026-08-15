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

// Ubah dari string manual menjadi seperti ini:
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'redirect' => env('GOOGLE_REDIRECT_URL', 'http://localhost:8000/callback'),
    ],

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

    'dana' => [
        'client_id'     => env('DANA_CLIENT_ID', ''),
        'client_secret' => env('DANA_CLIENT_SECRET', ''),
        'merchant_id'   => env('DANA_MERCHANT_ID', ''),
        'private_key'   => env('DANA_PRIVATE_KEY', ''),
        'public_key'    => env('DANA_PUBLIC_KEY', ''),
        'env'           => env('DANA_ENV', 'sandbox'), // sandbox or production
    ],

    'gemini' => [
        'api_keys' => env('GEMINI_API_KEYS', ''),
    ],

];

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

    /*
    |--------------------------------------------------------------------------
    | CVL KRA KYC Service
    |--------------------------------------------------------------------------
    */
    'cvl_kra' => [
        'base_url' => env('CVL_API_BASE_URL', 'https://krapancheck.cvlindia.com/V3/api'),
        'username' => env('CVL_USER_NAME', ''),
        'password' => env('CVL_PASSWORD', ''),
        'pos_code' => env('CVL_POS_CODE', ''),
        'api_key' => env('CVL_API_KEY', ''),
        'aes_key' => env('CVL_AES_KEY', ''),           // Base64-encoded key for AES-192 (existing)
        'aes_key_256' => env('CVL_AES_KEY_256', ''),    // Raw UTF-8 key for AES-256 (SolicitImage)
        // 'environment' => env('CVL_ENVIRONMENT', 'LIVE'),
    ],

];

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
    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
        'smtp_username' => env('BREVO_USERNAME', '96ea70001@smtp-brevo.com'),
        'smtp_password' => env('BREVO_PASSWORD'),
        'smtp_host' => env('BREVO_HOST', 'smtp-relay.brevo.com'),
        'smtp_port' => env('BREVO_PORT', 587),
        'sms_sender' => env('BREVO_SMS_SENDER', 'GameStore'),
        'list_id' => env('BREVO_LIST_ID'),
    ],
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'likecard' => [
        // API Base URL
        'api_url' => env('LIKECARD_API_URL', 'https://taxes.like4app.com/online'),

        // Authentication Credentials
        'device_id' => env('LIKECARD_DEVICE_ID'),
        'email' => env('LIKECARD_EMAIL'),
        'security_code' => env('LIKECARD_SECURITY_CODE'),
        'phone' => env('LIKECARD_PHONE'),

        // Hash Generation Key
        'key' => env('LIKECARD_KEY'),

        // Encryption Keys for Serial Decryption
        'secret_key' => env('LIKECARD_SECRET_KEY'),
        'secret_iv' => env('LIKECARD_SECRET_IV'),

        // Default Language (1 = English, 2 = Arabic)
        'default_lang_id' => env('LIKECARD_LANG_ID', 1),

        // Cache Settings (in minutes)
        'cache_categories' => 300, // 5 hours = 300 minutes
        'cache_products' => 30,    // 30 minutes

        // Sync Settings
        'sync_batch_size' => env('LIKECARD_SYNC_BATCH_SIZE', 20),
        'sync_delay_ms' => env('LIKECARD_SYNC_DELAY_MS', 500), // milliseconds between API calls

        // Retry Settings
        'max_retries' => env('LIKECARD_MAX_RETRIES', 6),
        'retry_delay' => env('LIKECARD_RETRY_DELAY', 10), // seconds

        // Default Margin (percentage to add to cost price)
        'default_margin' => env('LIKECARD_DEFAULT_MARGIN', 10), 
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];

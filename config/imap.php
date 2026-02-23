<?php

return [
    /*
    |--------------------------------------------------------------------------
    | IMAP Default Connection
    |--------------------------------------------------------------------------
    |
    | This option defines the default IMAP connection that will be used.
    |
    */
    'default' => env('IMAP_DEFAULT', 'default'),

    /*
    |--------------------------------------------------------------------------
    | IMAP Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the IMAP connections for your application.
    |
    */
    'connections' => [
        'default' => [
            'host' => env('IMAP_HOST', 'imap.hostinger.com'),
            'port' => env('IMAP_PORT', 993),
            'encryption' => env('IMAP_ENCRYPTION', 'ssl'),
            'validate_cert' => env('IMAP_VALIDATE_CERT', true),
            'username' => env('IMAP_USERNAME', env('MAIL_USERNAME')),
            'password' => env('IMAP_PASSWORD', env('MAIL_PASSWORD')),
            'protocol' => env('IMAP_PROTOCOL', 'imap'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IMAP Options
    |--------------------------------------------------------------------------
    |
    | These options allow you to configure various IMAP settings.
    |
    */
    'options' => [
        'fetch_order' => 'desc',
        'fetch_limit' => 0,
        'sequence' => 'uid',
        'fetch_body' => true,
        'fetch_attachment' => true,
        'fetch_flags' => true,
        'soft_fail' => true,
        'boundary' => null,
        'charset' => 'UTF-8',
        'date_format' => 'd M Y H:i:s',
        'timezone' => 'UTC',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sent Folder Names
    |--------------------------------------------------------------------------
    |
    | List of possible sent folder names to try when looking for the Sent folder.
    | Different mail providers use different names.
    |
    */
    'sent_folders' => [
        'Sent',
        'Sent Items',
        'Sent Messages',
        'INBOX.Sent',
        'INBOX.Sent Items',
    ],
];

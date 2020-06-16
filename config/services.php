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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => '/login/google/callback',

        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    '99taxi' => [
        'sandbox' => env('NNTAXI_SANDBOX_ENABLED', true),
        'api_key' => env('NNTAXI_API_KEY'),
        'sandbox_api_key' => env('NNTAXI_SANDBOX_API_KEY'),

        'webhook_username' => env('NNTAXI_WEBHOOK_USERNAME'),
        'webhook_password' => env('NNTAXI_WEBHOOK_PASSWORD'),
    ],

    'wappa' => [
        'username' => env('WAPPA_USERNAME'),
        'password' => env('WAPPA_PASSWORD'),

        'client_id' => env('WAPPA_CLIENT_ID'),
        'client_secret' => env('WAPPA_CLIENT_SECRET'),

        'sandbox' => env('WAPPA_SANDBOX_ENABLED', true)
    ],

    'uber' => [
        'api_key' => env('UBER_API_KEY'),
        'client_id' => env('UBER_CLIENT_ID'),
    ],

];

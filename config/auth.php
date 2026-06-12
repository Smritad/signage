<?php

return [

'defaults' => [
    'guard' => env('AUTH_GUARD', 'web'),
    'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
],

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'custom' => [
        'driver' => 'session',
        'provider' => 'custom_users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'custom_users' => [
        'driver' => 'eloquent',
        'model' => App\Models\CustomUser::class,
    ],
],

'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
        'expire' => 60,
        'throttle' => 60,
    ],

    'custom_users' => [   // ✅ Add your custom_users broker here
        'provider' => 'custom_users',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
    ],
],

'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),


];

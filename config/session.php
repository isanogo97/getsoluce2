<?php

use Illuminate\Support\Str;

return [

    // On utilise désormais la table 'sessions'
    'driver'          => env('SESSION_DRIVER', 'database'),
    'lifetime'        => (int) env('SESSION_LIFETIME', 120),
    'expire_on_close' => (bool) env('SESSION_EXPIRE_ON_CLOSE', false),
    'encrypt'         => (bool) env('SESSION_ENCRYPT', false),

    // (uniquement pour driver=file, ignoré ici)
    'files'           => storage_path('framework/sessions'),

    // Paramètres pour driver=database
    'connection'      => env('SESSION_CONNECTION', null),
    'table'           => env('SESSION_TABLE', 'sessions'),

    'lottery'         => [2, 100],

    // Cookie settings (laisser tels quels)
    'cookie'          => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),
    'path'            => env('SESSION_PATH', '/'),
    'domain'          => env('SESSION_DOMAIN', null),
    'secure'          => (bool) env('SESSION_SECURE_COOKIE', false),
    'http_only'       => (bool) env('SESSION_HTTP_ONLY', true),
    'same_site'       => env('SESSION_SAME_SITE', 'lax'),
    'partitioned'     => (bool) env('SESSION_PARTITIONED_COOKIE', false),

];

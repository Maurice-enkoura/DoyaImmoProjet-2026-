<?php

return [
    'default' => env('NOTIFICATION_DEFAULT', 'database'),

    'channels' => [
        'database' => [
            'driver' => 'database',
            'table' => 'notifications',
            'connection' => env('DB_CONNECTION', 'mysql'),
        ],
        // ...
    ],
];
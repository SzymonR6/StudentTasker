<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'StudentTasker',
        'env' => getenv('APP_ENV') ?: 'development',
        'debug' => getenv('APP_DEBUG') === 'true',
    ],

    'database' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'port' => getenv('DB_PORT') ?: '5432',
        'name' => getenv('DB_NAME') ?: 'studenttasker',
        'user' => getenv('DB_USER') ?: 'studenttasker_user',
        'password' => getenv('DB_PASSWORD') ?: 'studenttasker_password',
    ],
];
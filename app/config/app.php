<?php

declare(strict_types=1);

/*
 * General application configuration.
 */

return [
    'name'  => $_ENV['APP_NAME'] ?? 'Tamboli Samaj Portal',
    'url'   => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'debug' => APP_DEBUG,

    'session' => [
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 86400),
        'secure'   => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'name'     => $_ENV['SESSION_NAME'] ?? 'TSP_SESSION',
    ],

    'pagination' => [
        'per_page' => 20,
    ],
];

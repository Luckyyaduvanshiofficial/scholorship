<?php

declare(strict_types=1);

/*
 * Application constants.
 * Loaded early — available everywhere without class instantiation.
 */

define('APP_ROOT', ROOT_PATH);
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('APP_TIMEZONE', $_ENV['APP_TIMEZONE'] ?? 'Asia/Kolkata');
define('APP_SECRET', $_ENV['APP_SECRET'] ?? '');
define('APP_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'));

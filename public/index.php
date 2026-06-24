<?php

declare(strict_types=1);

/**
 * Application Entry Point.
 *
 * Every HTTP request enters here. This file:
 *   1. Loads configuration
 *   2. Starts the session
 *   3. Initializes the application
 *   4. Dispatches the request
 */

// ─── Path Constants ───────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));

// ─── Composer Autoloader ──────────────────────────────────
require ROOT_PATH . '/vendor/autoload.php';

// ─── Load .env ────────────────────────────────────────────
$envFile = ROOT_PATH . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip comments
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Parse KEY=VALUE
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key           = trim($key);
            $value         = trim($value, " \t\n\r\0\x0B\"'");

            // Only set if not already defined
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

// ─── Load Configuration ───────────────────────────────────
require ROOT_PATH . '/app/config/constants.php';
require ROOT_PATH . '/app/config/paths.php';

// ─── Error Reporting ──────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// ─── Timezone ─────────────────────────────────────────────
date_default_timezone_set(APP_TIMEZONE);

// ─── Start Session ────────────────────────────────────────
\App\Core\Session::start();

// ─── Run Application ──────────────────────────────────────
$app = new \App\Core\App();
$app->run();

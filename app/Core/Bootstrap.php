<?php

declare(strict_types=1);

/**
 * Shared Bootstrap — Reusable application initialization.
 *
 * This file is loaded by entry points (public/index.php, public_html/index.php)
 * after defining ROOT_PATH. It handles:
 *   1. Loading .env
 *   2. Loading configuration
 *   3. Error reporting
 *   4. Timezone
 *   5. Session start
 *   6. Exception handler
 */

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
require ROOT_PATH . '/app/Config/constants.php';
require ROOT_PATH . '/app/Config/paths.php';

// ─── Global URL helpers (available in views during render) ─
if (!function_exists('admin_path')) {
    function admin_path(string $path = ''): string
    {
        return \App\Core\Url::admin($path);
    }
}

if (!function_exists('admin_dashboard_url')) {
    function admin_dashboard_url(): string
    {
        return APP_HOST === 'admin' ? admin_path() : \App\Core\Url::adminSite();
    }
}

// ─── Error Reporting ──────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// ─── Timezone ─────────────────────────────────────────────
date_default_timezone_set(APP_TIMEZONE);

// ─── Start Session ────────────────────────────────────────
\App\Core\Session::start();

// ─── Global Exception Handler ─────────────────────────────
set_exception_handler(function (\Throwable $e): void {
    \App\Core\Logger::error('Uncaught exception: ' . $e->getMessage(), [
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    $isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    $isAjax = (
        ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'
        || str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')
    );

    if ($isPost && !$isAjax) {
        try {
            \App\Core\Flash::set('error', 'A temporary error occurred. Please try again in a moment.');
        } catch (\Throwable $flashError) {
            // Session not writable — fall through to plain redirect
        }
        $referrer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referrer, true, 302);
        exit;
    }

    // GET / JSON — show the standard 500 page
    try {
        \App\Core\Response::abort(500, APP_DEBUG ? $e->getMessage() : '');
    } catch (\Throwable $abortError) {
        // Last-resort fallback if even abort fails (e.g. view missing)
        http_response_code(500);
        echo 'A temporary error occurred. Please try again.';
    }
});

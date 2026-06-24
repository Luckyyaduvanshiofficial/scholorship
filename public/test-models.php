<?php
/**
 * Model smoke test — verifies all three models work against the actual schema.
 * Hit this in browser: http://localhost:8000/test-models.php
 */

require __DIR__ . '/../vendor/autoload.php';

// Load .env
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && !str_starts_with($line, '#') && str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim(trim($value), "'\"");
        }
    }
}

// Path constants
define('APP_ROOT', dirname(__DIR__));
define('CONFIG_PATH', APP_ROOT . '/app/config');
define('CORE_PATH', APP_ROOT . '/app/core');
define('VIEW_PATH', APP_ROOT . '/app/views');
define('APP_DEBUG', true);

header('Content-Type: text/html; charset=utf-8');

try {
    \App\Core\Database::getInstance();

    $user = new \App\Models\User();
    echo "✅ User model — " . $user->count() . " users<br>";

    $student = new \App\Models\Student();
    echo "✅ Student model — " . $student->count() . " students<br>";

    $application = new \App\Models\Application();
    echo "✅ Application model — " . $application->count() . " applications<br>";

    echo "<br>All models working correctly ✓";
} catch (\Throwable $e) {
    echo "❌ Error: " . $e->getMessage();
    echo "<br>File: " . $e->getFile() . ":" . $e->getLine();
}

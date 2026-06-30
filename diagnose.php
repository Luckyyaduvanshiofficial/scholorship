<?php

declare(strict_types=1);

/**
 * Hostinger deployment diagnostic — upload to public_html/
 * Visit: https://tambolisamaj.online/diagnose.php
 * DELETE this file after fixing issues.
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

$root     = $_SERVER['DIAGNOSE_ROOT'] ?? __DIR__;
$docRoot  = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '(unknown)';
$httpHost = $_SERVER['HTTP_HOST'] ?? '(unknown)';

$checks = [];

function check(bool $ok, string $label, string $pass, string $fail): void {
    global $checks;
    $checks[] = ['ok' => $ok, 'label' => $label, 'msg' => $ok ? $pass : $fail];
}

// Structure
check(is_file($root . '/bootstrap.php'), 'bootstrap.php', 'Found at project root', 'Missing — upload full repo to public_html');
check(is_file($root . '/app/Core/Bootstrap.php'), 'app/Core/Bootstrap.php', 'Found (case OK for Linux)', 'Missing or wrong case — must be app/Core/ not app/core/');
check(is_file($root . '/vendor/autoload.php'), 'vendor/', 'Found', 'Missing — run composer install or upload vendor/');
check(is_file($root . '/.env'), '.env', 'Found', 'Missing — copy .env.example to .env and fill DB credentials');
check(is_file($root . '/main/index.php'), 'main/', 'Found', 'Missing main/ entry folder');
check(is_file($root . '/portal/index.php'), 'portal/', 'Found', 'Missing portal/ entry folder');
check(is_file($root . '/admin/index.php'), 'admin/', 'Found', 'Missing admin/ entry folder');
check(is_dir($root . '/portal/assets/css'), 'portal/assets/', 'Found', 'Missing — CSS will not load');

$writable = is_writable($root . '/storage') && is_writable($root . '/uploads');
check($writable, 'storage/ + uploads/', 'Writable', 'Not writable — chmod 755 or 775 in Hostinger File Manager');

// Document root mode
$entry = basename(str_replace('\\', '/', $docRoot));
$mode  = in_array($entry, ['main', 'portal', 'admin'], true) ? "Mode A (subfolder: {$entry})" : 'Mode B (unified public_html)';
check(true, 'Deploy mode', $mode, '');

// PHP
check(version_compare(PHP_VERSION, '8.1.0', '>='), 'PHP version', PHP_VERSION . ' OK', PHP_VERSION . ' — need PHP 8.1+ in Hostinger');

// DB
$dbOk    = false;
$dbError = 'No .env file';
if (is_file($root . '/.env')) {
    foreach (file($root . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line !== '' && !str_starts_with($line, '#') && str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $_ENV[trim($k)] = trim(trim($v), "'\"");
        }
    }
    try {
        $pdo = new PDO(
            'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? '') . ';charset=utf8mb4',
            $_ENV['DB_USER'] ?? '',
            $_ENV['DB_PASS'] ?? '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $dbOk = true;
    } catch (Throwable $e) {
        $dbError = $e->getMessage();
    }
}
check($dbOk, 'Database', 'Connected to ' . ($_ENV['DB_NAME'] ?? ''), 'Failed: ' . ($dbError ?? 'check .env DB_* values'));

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tamboli Samaj — Deploy Check</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
        h1 { font-size: 1.4rem; }
        .ok { color: #15803d; } .bad { color: #b91c1c; }
        li { margin: 0.4rem 0; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
        .warn { background: #fef3c7; padding: 1rem; border-radius: 8px; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <h1>Tamboli Samaj — Hostinger Deploy Check</h1>
    <p>HTTP host: <code><?= htmlspecialchars($httpHost) ?></code><br>
       Document root: <code><?= htmlspecialchars($docRoot) ?></code><br>
       Project root: <code><?= htmlspecialchars($root) ?></code></p>
    <ul>
        <?php foreach ($checks as $c): ?>
            <li class="<?= $c['ok'] ? 'ok' : 'bad' ?>">
                <?= $c['ok'] ? '✔' : '✘' ?> <strong><?= htmlspecialchars($c['label']) ?></strong> —
                <?= htmlspecialchars($c['msg']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="warn">
        <strong>403 Forbidden?</strong> Set subdomain document roots in Hostinger:<br>
        <code>tambolisamaj.online</code> → <code>public_html/main</code><br>
        <code>portal.tambolisamaj.online</code> → <code>public_html/portal</code><br>
        <code>admin.tambolisamaj.online</code> → <code>public_html/admin</code><br>
        Or use Mode B: all domains → <code>public_html</code> (root index.php handles routing).<br><br>
        <strong>Delete diagnose.php</strong> after your site works.
    </div>
</body>
</html>
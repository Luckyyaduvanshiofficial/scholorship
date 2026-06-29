<?php

declare(strict_types=1);

/**
 * Shared bootstrap loader for main / portal / admin entry points.
 * Each index.php must define APP_HOST before requiring this file.
 */

if (!defined('ROOT_PATH')) {
    // public_html/{host}/index.php → parent is public_html/
    $root = dirname(__DIR__);

    // Local repo: app/ lives two levels up (project root)
    if (!is_file($root . '/app/Core/Bootstrap.php')) {
        $root = dirname(__DIR__, 2);
    }

    define('ROOT_PATH', $root);
}

require ROOT_PATH . '/app/Core/Bootstrap.php';

$app = new \App\Core\App();
$app->run();
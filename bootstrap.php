<?php

declare(strict_types=1);

/**
 * Shared bootstrap loader for main / portal / admin entry points.
 * Each index.php must define APP_HOST before requiring this file.
 */

if (!defined('ROOT_PATH')) {
    // bootstrap.php sits at project root; entry points are in main/portal/admin/
    $root = __DIR__;

    if (!is_file($root . '/app/Core/Bootstrap.php')) {
        $root = dirname(__DIR__);
    }

    if (!is_file($root . '/app/Core/Bootstrap.php')) {
        $root = dirname(__DIR__, 2);
    }

    define('ROOT_PATH', $root);
}

require ROOT_PATH . '/app/Core/Bootstrap.php';

$app = new \App\Core\App();
$app->run();
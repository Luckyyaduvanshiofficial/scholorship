<?php

declare(strict_types=1);

/**
 * Application Entry Point — Portal.
 *
 * This is the entry point for portal.tambolisamaj.online.
 * It defines the host type and loads the shared bootstrap.
 */

// ─── Path Constants ───────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));

// ─── Host Identifier ──────────────────────────────────────
// Local dev entry — portal host (production uses public_html/portal/index.php)
if (!defined('APP_HOST')) {
    define('APP_HOST', 'portal');
}

// ─── Shared Bootstrap ─────────────────────────────────────
require ROOT_PATH . '/app/Core/Bootstrap.php';

// ─── Run Application ──────────────────────────────────────
$app = new \App\Core\App();
$app->run();

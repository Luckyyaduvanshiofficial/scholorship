<?php

declare(strict_types=1);

/**
 * Web Routes — Tamboli Samaj (Multi-site dispatcher).
 *
 * Hosts (set by each entry point via APP_HOST):
 *   site   → tambolisamaj.online        (main website)
 *   portal → portal.tambolisamaj.online (student portal)
 *   admin  → admin.tambolisamaj.online  (admin panel)
 *
 * @var \App\Core\Router $router
 */

$host = defined('APP_HOST') ? APP_HOST : 'portal';

$routeFile = match ($host) {
    'admin'  => __DIR__ . '/admin.php',
    'site'   => __DIR__ . '/site.php',
    default  => __DIR__ . '/portal.php',
};

require $routeFile;
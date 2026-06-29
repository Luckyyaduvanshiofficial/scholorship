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
define('APP_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'));

// Host identifier for multi-site routing: 'site', 'portal', 'admin'
// Entry points define this before loading bootstrap; fall back to 'portal'
if (!defined('APP_HOST')) {
    define('APP_HOST', $_ENV['APP_HOST'] ?? 'portal');
}

// Per-host base URLs (production subdomains)
define('SITE_URL', rtrim($_ENV['SITE_URL'] ?? 'https://tambolisamaj.online', '/'));
define('PORTAL_URL', rtrim($_ENV['PORTAL_URL'] ?? ($_ENV['APP_URL'] ?? 'https://portal.tambolisamaj.online'), '/'));
define('ADMIN_URL', rtrim($_ENV['ADMIN_URL'] ?? 'https://admin.tambolisamaj.online', '/'));

// Cross-subdomain session cookie domain (empty for local dev)
define('SESSION_DOMAIN', $_ENV['SESSION_DOMAIN'] ?? '');

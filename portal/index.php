<?php

declare(strict_types=1);

/**
 * Entry point — Student Portal (portal.tambolisamaj.online)
 * Document root: portal/ (project root on server: public_html/portal/)
 *
 * Deploy: copy contents of /public/ (assets, uploads, favicon, etc.)
 * into this folder alongside index.php.
 */

define('APP_HOST', 'portal');

require dirname(__DIR__) . '/bootstrap.php';
<?php

declare(strict_types=1);

/**
 * Fallback entry when the domain document root is public_html/ (not main/).
 * Hostinger: prefer pointing tambolisamaj.online → public_html/main
 */

define('APP_HOST', 'site');

require __DIR__ . '/bootstrap.php';
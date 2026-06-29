<?php

declare(strict_types=1);

/*
 * Path constants used throughout the application.
 */

define('CONFIG_PATH', APP_ROOT . '/app/Config');
define('CORE_PATH', APP_ROOT . '/app/Core');
define('VIEW_PATH', APP_ROOT . '/app/Views');
define('UPLOAD_PATH', APP_ROOT . '/uploads');
define('STORAGE_PATH', APP_ROOT . '/storage');
define('LOG_PATH', STORAGE_PATH . '/logs');

// Document root per host (local dev uses public/ for portal)
$publicPath = match (APP_HOST) {
    'site'   => APP_ROOT . '/public_html/main',
    'admin'  => APP_ROOT . '/public_html/admin',
    'portal' => is_dir(APP_ROOT . '/public_html/portal') && is_file(APP_ROOT . '/public_html/portal/index.php')
        ? APP_ROOT . '/public_html/portal'
        : APP_ROOT . '/public',
    default  => APP_ROOT . '/public',
};

define('PUBLIC_PATH', $publicPath);
define('ASSET_PATH', APP_ROOT . '/public/assets');

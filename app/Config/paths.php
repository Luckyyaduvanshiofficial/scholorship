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

// Document root per host (main / portal / admin at project root)
$publicPath = match (APP_HOST) {
    'site'   => APP_ROOT . '/main',
    'admin'  => APP_ROOT . '/admin',
    'portal' => APP_ROOT . '/portal',
    default  => APP_ROOT . '/portal',
};

define('PUBLIC_PATH', $publicPath);
define('ASSET_PATH', APP_ROOT . '/portal/assets');

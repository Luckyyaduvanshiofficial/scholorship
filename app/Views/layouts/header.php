<!DOCTYPE html>
<html lang="hi">  <!-- "en" → "hi" since primary content is Hindi -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5.0">
    <meta name="description" content="तम्बोली समाज विकास संस्था — प्रतिभा सम्मान एवं छात्रवृत्ति आवेदन पोर्टल">
    <meta name="theme-color" content="#8B0000">  <!-- maroon brand color, mobile browser bar -->
    <?php
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $isAuthPage = \App\Core\Auth::check() || 
                  str_contains($requestUri, '/admin') || 
                  str_contains($requestUri, '/dashboard') || 
                  str_contains($requestUri, '/dashboard/profile') || 
                  str_contains($requestUri, '/dashboard/applications');
    if ($isAuthPage): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>
    <title><?= \App\Core\Helpers::esc($title ?? 'Tamboli Samaj Portal') ?></title>

    <!-- Favicon / PWA -->
    <link rel="icon" type="image/png" href="<?= \App\Core\Url::portal('/favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= \App\Core\Url::asset('images/icons/icon-192x192.png') ?>">
    <link rel="manifest" href="<?= \App\Core\Url::portal('/manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Tamboli Portal">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= APP_URL . parse_url($requestUri, PHP_URL_PATH) ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= APP_URL . $requestUri ?>">
    <meta property="og:title" content="<?= \App\Core\Helpers::esc($title ?? 'Tamboli Samaj Portal') ?>">
    <meta property="og:description" content="तम्बोली समाज विकास संस्था — प्रतिभा सम्मान एवं छात्रवृत्ति आवेदन पोर्टल">
    <meta property="og:image" content="<?= \App\Core\Url::asset('images/share_banner.png') ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= APP_URL . $requestUri ?>">
    <meta name="twitter:title" content="<?= \App\Core\Helpers::esc($title ?? 'Tamboli Samaj Portal') ?>">
    <meta name="twitter:description" content="तम्बोली समाज विकास संस्था — प्रतिभा सम्मान एवं छात्रवृत्ति आवेदन पोर्टल">
    <meta name="twitter:image" content="<?= \App\Core\Url::asset('images/share_banner.png') ?>">

    <!-- Preconnect to CDNs -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <!-- Bootstrap 5 (critical, keep in head) -->
    <link href="<?= \App\Core\Url::asset('css/bootstrap.min.css') ?>" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Self-hosted Google Fonts (same-origin, production-cacheable) -->
    <link href="<?= \App\Core\Url::asset('fonts/fonts.css') ?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?= \App\Core\Url::asset('css/style.css?v=2.2.0') ?>" rel="stylesheet">
    <link href="<?= \App\Core\Url::asset('css/print.css?v=1.0.0') ?>" rel="stylesheet" media="print">

    <!-- Bootstrap 5 JS Bundle -->
    <script src="<?= \App\Core\Url::asset('js/bootstrap.bundle.min.js') ?>" defer></script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= \App\Core\Url::portal('/sw.js') ?>').catch(() => {});
            });
        }
    </script>
<?php $bodyClass = $bodyClass ?? ''; ?>
</head>
<body class="<?= \App\Core\Helpers::esc($bodyClass) ?>">

<!DOCTYPE html>
<html lang="hi">  <!-- "en" → "hi" since primary content is Hindi -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="तम्बोली समाज विकास संस्था — प्रतिभा सम्मान एवं छात्रवृत्ति आवेदन पोर्टल">
    <meta name="theme-color" content="#8B0000">  <!-- maroon brand color, mobile browser bar -->
    <?php
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $isAuthPage = \App\Core\Auth::check() || 
                  str_contains($requestUri, '/admin') || 
                  str_contains($requestUri, '/dashboard') || 
                  str_contains($requestUri, '/profile') || 
                  str_contains($requestUri, '/applications');
    if ($isAuthPage): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>
    <title><?= \App\Core\Helpers::esc($title ?? 'Tamboli Samaj Portal') ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= APP_URL . parse_url($requestUri, PHP_URL_PATH) ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= APP_URL . $requestUri ?>">
    <meta property="og:title" content="<?= \App\Core\Helpers::esc($title ?? 'Tamboli Samaj Portal') ?>">
    <meta property="og:description" content="तम्बोली समाज विकास संस्था — प्रतिभा सम्मान एवं छात्रवृत्ति आवेदन पोर्टल">
    <meta property="og:image" content="<?= APP_URL ?>/assets/images/share_banner.png">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= APP_URL . $requestUri ?>">
    <meta name="twitter:title" content="<?= \App\Core\Helpers::esc($title ?? 'Tamboli Samaj Portal') ?>">
    <meta name="twitter:description" content="तम्बोली समाज विकास संस्था — प्रतिभा सम्मान एवं छात्रवृत्ति आवेदन पोर्टल">
    <meta name="twitter:image" content="<?= APP_URL ?>/assets/images/share_banner.png">

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts: Inter + Manrope + Noto Sans Devanagari + Saira + Saira Stencil One -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..900&family=Manrope:wght@300..800&family=Noto+Sans+Devanagari:wght@100..900&family=Saira+Stencil+One&family=Saira:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link href="/assets/css/style.css?v=1.0.5" rel="stylesheet">

    <!-- Bootstrap 5 JS Bundle -->
    <script src="/assets/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
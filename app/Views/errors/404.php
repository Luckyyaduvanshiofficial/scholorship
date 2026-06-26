<?php
/**
 * Error 404 — Not Found
 * Standalone template matching site design system.
 */
declare(strict_types=1);

$errorCode    = $errorCode ?? 404;
$errorMessage = $errorMessage ?? 'The page you are looking for does not exist.';
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5.0">
    <title><?= $errorCode ?> - पृष्ठ नहीं मिला / Page Not Found</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">
    <!-- Bootstrap CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Self-hosted Google Fonts -->
    <link href="/assets/fonts/fonts.css" rel="stylesheet">
    <!-- Portal Design System Custom CSS -->
    <link href="/assets/css/style.css?v=2.1.0" rel="stylesheet">
</head>
<body class="bg-light">

<main class="tsp-auth-wrapper d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <div class="tsp-auth-logo-wrapper mb-3">
                        <img src="/assets/images/logo/logo-placeholder.svg" alt="logo">
                    </div>
                    <h1 class="h3 fw-bold mb-1" style="color:var(--accent);">तम्बोली समाज विकास संस्था</h1>
                    <p class="small text-muted mb-0">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</p>
                </div>
                <div class="card border-0 tsp-auth-card">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: rgba(139, 0, 0, 0.06);">
                                <i class="bi bi-search-heart" style="font-size: 3.6rem; color: var(--accent);"></i>
                            </div>
                        </div>
                        <h1 class="display-2 fw-bold mb-2" style="font-family: 'Manrope', sans-serif; letter-spacing: -2px; color: var(--accent); line-height: 1;"><?= $errorCode ?></h1>
                        <h2 class="fw-bold mb-3" style="font-size: 1.8rem; font-family: 'Manrope', 'Noto Sans Devanagari', sans-serif; color: var(--primary);">पृष्ठ नहीं मिला / Page Not Found</h2>
                        <p class="text-muted mb-4" style="font-size: 1.4rem; line-height: 1.6;">क्षमा करें, जिस पृष्ठ की आप तलाश कर रहे हैं वह उपलब्ध नहीं है या उसे हटा दिया गया है।</p>
                        <p class="text-muted mb-4 small" style="font-size: 1.2rem; font-style: italic;"><?= \App\Core\Helpers::esc($errorMessage) ?></p>

                        <a href="/" class="btn tsp-btn w-100 mt-2 mb-2 justify-content-center text-white" style="background:var(--nav-red); border-color:var(--nav-red); display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.6rem; border-radius: 5rem; font-size: 1.4rem; font-weight: 600;">
                            <i class="bi bi-house-door-fill"></i>
                            <span>मुख्य पृष्ठ पर जाएं / Back to Home</span>
                        </a>
                    </div>
                </div>
                <p class="text-center small text-muted mt-4"><i class="bi bi-shield-check me-1"></i> Tamboli Samaj Vikas Sanstha, Rajasthan</p>
            </div>
        </div>
    </div>
</main>

</body>
</html>

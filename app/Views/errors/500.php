<?php
/**
 * Error 500 — Internal Server Error
 * Standalone template matching site design system.
 */
declare(strict_types=1);

$errorCode    = $errorCode ?? 500;
$errorMessage = $errorMessage ?? 'Something went wrong. Please try again later.';
$showDebug    = defined('APP_DEBUG') && APP_DEBUG;
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5.0">
    <title><?= $errorCode ?> - सर्वर त्रुटि / Server Error</title>
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
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: rgba(220, 38, 38, 0.06);">
                                <i class="bi bi-exclamation-triangle-fill" style="font-size: 3.6rem; color: #dc2626;"></i>
                            </div>
                        </div>
                        <h1 class="display-2 fw-bold mb-2" style="font-family: 'Manrope', sans-serif; letter-spacing: -2px; color: var(--accent); line-height: 1;"><?= $errorCode ?></h1>
                        <h2 class="fw-bold mb-3" style="font-size: 1.8rem; font-family: 'Manrope', 'Noto Sans Devanagari', sans-serif; color: var(--primary);">सर्वर त्रुटि / Server Error</h2>
                        <p class="text-muted mb-4" style="font-size: 1.4rem; line-height: 1.6;">क्षमा करें, सर्वर पर एक आंतरिक त्रुटि हुई है। कृपया कुछ समय बाद पुनः प्रयास करें।</p>
                        
                        <a href="/" class="btn tsp-btn w-100 mt-2 mb-2 justify-content-center text-white" style="background:var(--nav-red); border-color:var(--nav-red); display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.6rem; border-radius: 5rem; font-size: 1.4rem; font-weight: 600;">
                            <i class="bi bi-house-door-fill"></i>
                            <span>मुख्य पृष्ठ पर जाएं / Back to Home</span>
                        </a>

                        <?php if ($showDebug): ?>
                            <details class="text-start mt-4" style="border-top: 1px solid var(--border-light); padding-top: 1.5rem;">
                                <summary class="small fw-semibold text-muted text-uppercase" style="cursor: pointer; outline: none; list-style: none; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="bi bi-bug-fill text-danger"></i>
                                    <span>Debug Info / डीबग विवरण देखें</span>
                                </summary>
                                <div class="mt-2 p-3 bg-dark text-white rounded-4 overflow-auto border border-secondary" style="max-height: 200px; font-family: monospace; font-size: 1.2rem; white-space: pre-wrap; word-break: break-all;">
                                    <?= \App\Core\Helpers::esc($errorMessage) ?>
                                </div>
                            </details>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="text-center small text-muted mt-4"><i class="bi bi-shield-check me-1"></i> Tamboli Samaj Vikas Sanstha, Rajasthan</p>
            </div>
        </div>
    </div>
</main>

</body>
</html>

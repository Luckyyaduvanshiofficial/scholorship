<?php
/**
 * Error 401 — Unauthorized
 * Wrapper (header/footer) handled by Response::abort().
 */
$errorCode    = $errorCode ?? 401;
$errorMessage = $errorMessage ?? 'You need to log in to access this page.';
?>

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-50">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle p-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-shield-lock-fill fs-1"></i>
                </div>
            </div>
            <h1 class="display-3 fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: -1px;"><?= $errorCode ?></h1>
            <h3 class="fw-bold mb-3" style="font-size: 1.5rem;">लॉगिन आवश्यक है / Authentication Required</h3>
            <p class="text-secondary mb-2" style="font-size: 1.25rem;">आपको इस पृष्ठ तक पहुँचने के लिए पहले लॉग इन करना होगा।</p>
            <p class="text-muted mb-4 small"><?= \App\Core\Helpers::esc($errorMessage) ?></p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/login" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 1.15rem;">
                    <i class="bi bi-box-arrow-in-right me-1"></i> लॉगिन करें / Log In
                </a>
                <a href="/" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold" style="font-size: 1.15rem;">
                    <i class="bi bi-house-door me-1"></i> मुख्य पृष्ठ / Home
                </a>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Error 403 — Forbidden
 * Wrapper (header/footer) handled by Response::abort().
 */
$errorCode    = $errorCode ?? 403;
$errorMessage = $errorMessage ?? 'You do not have permission to access this page.';
?>

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-50">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle p-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-shield-slash-fill fs-1"></i>
                </div>
            </div>
            <h1 class="display-3 fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: -1px;"><?= $errorCode ?></h1>
            <h3 class="fw-bold mb-3" style="font-size: 1.5rem;">पहुंच वर्जित है / Access Forbidden</h3>
            <p class="text-secondary mb-2" style="font-size: 1.25rem;">आपके पास इस पृष्ठ तक पहुँचने की अनुमति नहीं है।</p>
            <p class="text-muted mb-4 small"><?= \App\Core\Helpers::esc($errorMessage) ?></p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/dashboard" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 1.15rem;">
                    <i class="bi bi-speedometer2 me-1"></i> डैशबोर्ड / Dashboard
                </a>
                <a href="/" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold" style="font-size: 1.15rem;">
                    <i class="bi bi-house-door me-1"></i> मुख्य पृष्ठ / Home
                </a>
            </div>
        </div>
    </div>
</div>

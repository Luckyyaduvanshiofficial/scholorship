<?php
/**
 * Error 500 — Internal Server Error
 * Wrapper (header/footer) handled by Response::abort().
 */
$errorCode    = $errorCode ?? 500;
$errorMessage = $errorMessage ?? 'Something went wrong. Please try again later.';
?>

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-50">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle p-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
                </div>
            </div>
            <h1 class="display-3 fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: -1px;"><?= $errorCode ?></h1>
            <h3 class="fw-bold mb-3" style="font-size: 1.5rem;">सर्वर त्रुटि / Server Error</h3>
            <p class="text-secondary mb-2" style="font-size: 1.25rem;">क्षमा करें, सर्वर पर एक आंतरिक त्रुटि हुई है।</p>
            <p class="text-muted mb-4 small"><?= \App\Core\Helpers::esc($errorMessage) ?></p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 1.15rem;">
                    <i class="bi bi-house-door me-1"></i> मुख्य पृष्ठ / Home
                </a>
            </div>
        </div>
    </div>
</div>

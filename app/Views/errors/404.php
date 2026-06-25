<?php
/**
 * Error 404 — Not Found
 * Wrapper (header/footer) handled by Response::abort().
 */
$errorCode    = $errorCode ?? 404;
$errorMessage = $errorMessage ?? 'The page you are looking for does not exist.';
?>

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-50">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-secondary-subtle text-secondary rounded-circle p-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-search-heart-fill fs-1 text-secondary"></i>
                </div>
            </div>
            <h1 class="display-3 fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: -1px;"><?= $errorCode ?></h1>
            <h3 class="fw-bold mb-3" style="font-size: 1.5rem;">पृष्ठ नहीं मिला / Page Not Found</h3>
            <p class="text-secondary mb-2" style="font-size: 1.25rem;">क्षमा करें, जिस पृष्ठ की आप तलाश कर रहे हैं वह उपलब्ध नहीं है।</p>
            <p class="text-muted mb-4 small"><?= \App\Core\Helpers::esc($errorMessage) ?></p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 1.15rem;">
                    <i class="bi bi-house-door me-1"></i> मुख्य पृष्ठ / Home
                </a>
            </div>
        </div>
    </div>
</div>

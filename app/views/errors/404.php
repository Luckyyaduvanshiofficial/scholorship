<?php
/**
 * Error 404 — Not Found
 * Wrapper (header/footer) handled by Response::abort().
 */
$errorCode    = $errorCode ?? 404;
$errorMessage = $errorMessage ?? 'The page you are looking for does not exist.';
?>

<div class="container min-vh-50 d-flex align-items-center justify-content-center py-5">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-secondary"><?= $errorCode ?></h1>
        <h2 class="mb-3">Page Not Found</h2>
        <p class="text-muted mb-4"><?= \App\Core\Helpers::esc($errorMessage) ?></p>
        <a href="/" class="btn btn-success px-4">
            <i class="bi bi-house-door me-2"></i>Go Home
        </a>
    </div>
</div>

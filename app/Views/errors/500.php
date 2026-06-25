<?php
/**
 * Error 500 — Internal Server Error
 * Wrapper (header/footer) handled by Response::abort().
 */
$errorCode    = $errorCode ?? 500;
$errorMessage = $errorMessage ?? 'Something went wrong. Please try again later.';
?>

<div class="container min-vh-50 d-flex align-items-center justify-content-center py-5">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-danger"><?= $errorCode ?></h1>
        <h2 class="mb-3">Server Error</h2>
        <p class="text-muted mb-4"><?= \App\Core\Helpers::esc($errorMessage) ?></p>
        <a href="/" class="btn btn-success px-4">
            <i class="bi bi-house-door me-2"></i>Go Home
        </a>
    </div>
</div>

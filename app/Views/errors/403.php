<?php
/**
 * Error 403 — Forbidden
 * Wrapper (header/footer) handled by Response::abort().
 */
$errorCode    = $errorCode ?? 403;
$errorMessage = $errorMessage ?? 'You do not have permission to access this page.';
?>

<div class="container min-vh-50 d-flex align-items-center justify-content-center py-5">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-danger"><?= $errorCode ?></h1>
        <h2 class="mb-3">Forbidden</h2>
        <p class="text-muted mb-4"><?= \App\Core\Helpers::esc($errorMessage) ?></p>
        <div class="d-flex justify-content-center gap-2">
            <a href="/dashboard" class="btn btn-success px-4">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="/" class="btn btn-outline-secondary px-4">
                <i class="bi bi-house-door me-2"></i>Home
            </a>
        </div>
    </div>
</div>

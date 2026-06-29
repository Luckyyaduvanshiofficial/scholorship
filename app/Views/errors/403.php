<?php
/**
 * Error 403 — Forbidden
 * Standalone template matching site design system.
 */
declare(strict_types=1);

$errorCode    = $errorCode ?? 403;
$errorMessage = $errorMessage ?? 'You do not have permission to access this page.';
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5.0">
    <title><?= $errorCode ?> - पहुंच वर्जित / Access Forbidden</title>
<?php require __DIR__ . '/_head.php'; ?>
</head>
<body class="bg-light">

<main class="tsp-auth-wrapper d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <div class="tsp-auth-logo-wrapper mb-3">
                        <img src="<?= \App\Core\Url::asset('images/logo/logo-placeholder.svg') ?>" alt="logo">
                    </div>
                    <h1 class="h3 fw-bold mb-1" style="color:var(--accent);">तम्बोली समाज विकास संस्था</h1>
                    <p class="small text-muted mb-0">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</p>
                </div>
                <div class="card border-0 tsp-auth-card">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: rgba(234, 88, 12, 0.06);">
                                <i class="bi bi-shield-slash-fill" style="font-size: 3.6rem; color: #ea580c;"></i>
                            </div>
                        </div>
                        <h1 class="display-2 fw-bold mb-2" style="font-family: 'Manrope', sans-serif; letter-spacing: -2px; color: var(--accent); line-height: 1;"><?= $errorCode ?></h1>
                        <h2 class="fw-bold mb-3" style="font-size: 1.8rem; font-family: 'Manrope', 'Noto Sans Devanagari', sans-serif; color: var(--primary);">पहुंच वर्जित है / Access Forbidden</h2>
                        <p class="text-muted mb-4" style="font-size: 1.4rem; line-height: 1.6;">क्षमा करें, आपके पास इस पृष्ठ तक पहुँचने की अनुमति नहीं है।</p>
                        <p class="text-muted mb-4 small" style="font-size: 1.2rem; font-style: italic;"><?= \App\Core\Helpers::esc($errorMessage) ?></p>
                        
                        <div class="d-flex flex-column gap-2 mt-2">
                            <a href="<?= (APP_HOST === 'portal' || APP_HOST === 'site') ? '/dashboard' : \App\Core\Url::portal('/dashboard') ?>" class="btn tsp-btn w-100 justify-content-center text-white" style="background:var(--nav-red); border-color:var(--nav-red); display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.6rem; border-radius: 5rem; font-size: 1.4rem; font-weight: 600;">
                                <i class="bi bi-speedometer2"></i>
                                <span>डैशबोर्ड / Dashboard</span>
                            </a>
                            <a href="<?= \App\Core\Url::home() ?>" class="btn btn-outline-secondary w-100 justify-content-center" style="border-radius: 5rem; padding: 0.8rem 1.6rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 1.4rem; font-weight: 600;">
                                <i class="bi bi-house-door-fill"></i>
                                <span>मुख्य पृष्ठ / Home</span>
                            </a>
                        </div>
                    </div>
                </div>
                <p class="text-center small text-muted mt-4"><i class="bi bi-shield-check me-1"></i> Tamboli Samaj Vikas Sanstha, Rajasthan</p>
            </div>
        </div>
    </div>
</main>

</body>
</html>

<?php
use App\Core\Csrf;
use App\Core\Helpers;

$oldEmail = \App\Core\Flash::get('old_email');
$emailVal = !empty($oldEmail) ? $oldEmail[0] : '';

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<main class="tsp-auth-wrapper d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <div class="tsp-auth-logo-wrapper mb-3">
                        <img src="/assets/images/logo/logo-placeholder.svg" alt="logo">
                    </div>
                    <h1 class="h3 fw-bold mb-1" style="color:var(--nav-red);">तम्बोली समाज विकास संस्था</h1>
                    <p class="small text-muted mb-0">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</p>
                </div>
                <div class="card border-0 tsp-auth-card">
                    <div class="card-body p-4 p-md-5">
                        <form action="/login" method="post">
                            <?= Csrf::field() ?>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-2">लॉगिन प्रकार / Login As</label>
                                <div class="tsp-role-selector">
                                    <input type="radio" name="role" id="roleStudent" value="student" checked>
                                    <label for="roleStudent"><i class="bi bi-mortarboard"></i> छात्र / Student</label>

                                    <input type="radio" name="role" id="roleAdmin" value="admin">
                                    <label for="roleAdmin"><i class="bi bi-shield-lock"></i> एडमिन / Admin</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold">ईमेल / Email</label>
                                <input type="email" name="email" id="email" class="form-control tsp-input" placeholder="you@example.com" value="<?= Helpers::esc($emailVal) ?>" required autofocus>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label small fw-semibold mb-0">पासवर्ड / Password</label>
                                    <a href="/forgot-password" class="small text-decoration-none fw-semibold" style="color:var(--nav-red); font-size: 1.2rem;">पासवर्ड भूल गए? / Forgot?</a>
                                </div>
                                <div class="tsp-password-group position-relative">
                                    <input type="password" name="password" id="password" class="form-control tsp-input tsp-input-pw" placeholder="••••••••" required>
                                    <?php require VIEW_PATH . '/layouts/password-toggle.php'; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn tsp-btn w-100 mt-2 mb-2 justify-content-center" style="background:var(--nav-red); border-color:var(--nav-red);"><i class="bi bi-box-arrow-in-right me-1"></i> लॉगिन करें / Sign In</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Don't have an account? <a href="/register" class="fw-semibold" style="color:var(--nav-red);">Register here</a></small>
                        </div>
                    </div>
                </div>
                <p class="text-center small text-muted mt-4"><i class="bi bi-shield-check me-1"></i> Secure login · Tamboli Samaj Vikas Sanstha, Rajasthan</p>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
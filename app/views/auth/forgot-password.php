<?php
use App\Core\Csrf;

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
                    <h1 class="h3 fw-bold mb-1" style="color:var(--g);">पासवर्ड पुनः प्राप्त करें</h1>
                    <p class="small text-muted mb-0">Forgot Password Recovery</p>
                </div>
                <div class="card border-0 tsp-auth-card">
                    <div class="card-body p-4 p-md-5">
                        <p class="text-muted small text-center mb-4">अपना पंजीकृत ईमेल पता दर्ज करें। हम आपको अपना पासवर्ड रीसेट करने के लिए एक लिंक भेजेंगे। <br>Enter your registered email address below to receive a password reset link.</p>
                        <form action="/forgot-password" method="post">
                            <?= Csrf::field() ?>
                            <div class="mb-4">
                                <label for="email" class="form-label small fw-semibold">पंजीकृत ईमेल / Registered Email</label>
                                <div class="tsp-auth-input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" required autofocus>
                                </div>
                            </div>
                            <button type="submit" class="btn tsp-btn w-100 mt-2 mb-2 justify-content-center"><i class="bi bi-envelope-paper me-1"></i> रीसेट लिंक भेजें / Send Reset Link</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Remember your password? <a href="/login" class="fw-semibold" style="color:var(--g);">Sign in here</a></small>
                        </div>
                    </div>
                </div>
                <p class="text-center small text-muted mt-4"><i class="bi bi-shield-check me-1"></i> Secure request · Tamboli Samaj Vikas Sanstha, Rajasthan</p>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

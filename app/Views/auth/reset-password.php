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
                    <h1 class="h3 fw-bold mb-1" style="color:var(--nav-red);">नया पासवर्ड बनाएं</h1>
                    <p class="small text-muted mb-0">Reset Account Password</p>
                </div>
                <div class="card border-0 tsp-auth-card">
                    <div class="card-body p-4 p-md-5">
                        <form action="/reset-password" method="post">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="selector" value="<?= htmlspecialchars($selector) ?>">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            
                            <div class="mb-3">
                                <label for="password" class="form-label small fw-semibold">नया पासवर्ड / New Password</label>
                                <div class="tsp-auth-input-group tsp-password-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" minlength="6" required autofocus autocomplete="new-password">
                                    <?php require VIEW_PATH . '/layouts/password-toggle.php'; ?>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password_confirm" class="form-label small fw-semibold">पासवर्ड की पुष्टि करें / Confirm Password</label>
                                <div class="tsp-auth-input-group tsp-password-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="••••••••" minlength="6" required>
                                    <?php require VIEW_PATH . '/layouts/password-toggle.php'; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn tsp-btn w-100 mt-2 mb-2 justify-content-center" style="background:var(--nav-red); border-color:var(--nav-red);"><i class="bi bi-check-circle-fill me-1"></i> पासवर्ड अपडेट करें / Update Password</button>
                        </form>
                    </div>
                </div>
                <p class="text-center small text-muted mt-4"><i class="bi bi-shield-lock me-1"></i> Secure reset · Tamboli Samaj Vikas Sanstha, Rajasthan</p>
            </div>
        </div>
    </div>
</main>

<script>
// Client-side password confirmation match
(function() {
    var pwd = document.getElementById('password');
    var confirm = document.getElementById('password_confirm');
    var form = pwd && pwd.closest('form');
    if (!pwd || !confirm || !form) return;

    var feedback = document.createElement('div');
    feedback.className = 'invalid-feedback d-block';
    feedback.textContent = 'पासवर्ड मेल नहीं खाते / Passwords do not match';
    confirm.parentNode.appendChild(feedback);

    function checkMatch() {
        if (confirm.value.length > 0 && pwd.value !== confirm.value) {
            confirm.classList.add('is-invalid');
        } else {
            confirm.classList.remove('is-invalid');
            confirm.setCustomValidity('');
        }
    }

    form.addEventListener('submit', function(e) {
        if (pwd.value !== confirm.value) {
            confirm.classList.add('is-invalid');
            confirm.setCustomValidity('Passwords do not match');
        }
    });

    pwd.addEventListener('input', checkMatch);
    confirm.addEventListener('input', checkMatch);
})();
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

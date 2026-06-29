<?php
use App\Core\Csrf;
use App\Core\Helpers;
use App\Core\Flash;

$old = Flash::get('old');
$old = $old[0] ?? [];

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<main class="tsp-auth-wrapper d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-8 col-lg-7 col-xl-6">
                <div class="text-center mb-4">
                    <div class="tsp-auth-logo-wrapper mb-3">
                        <img src="<?= \App\Core\Url::asset('images/logo/logo-placeholder.svg') ?>" alt="logo">
                    </div>
                    <h1 class="h3 fw-bold mb-1" style="color:var(--nav-red);">छात्र पंजीकरण / Student Registration</h1>
                    <p class="small text-muted mb-0">पोर्टल पर अपना खाता बनाएं / Create account to apply</p>
                </div>
                <div class="card border-0 tsp-auth-card">
                    <div class="card-body p-4 p-md-5">
                        <form action="/register" method="post">
                            <?= Csrf::field() ?>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="first_name" class="form-label small fw-semibold">प्रथम नाम / First Name *</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First name" value="<?= Helpers::esc($old['first_name'] ?? '') ?>" required autofocus autocomplete="given-name">
                                </div>
                                <div class="col-sm-6">
                                    <label for="last_name" class="form-label small fw-semibold">अंतिम नाम / Last Name *</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last name" value="<?= Helpers::esc($old['last_name'] ?? '') ?>" required autocomplete="family-name">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="father_name" class="form-label small fw-semibold">पिता का नाम / Father's Name *</label>
                                <input type="text" name="father_name" id="father_name" class="form-control" placeholder="Father or guardian name" value="<?= Helpers::esc($old['father_name'] ?? '') ?>" required autocomplete="family-name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold">ईमेल / Email *</label>
                                <div class="tsp-auth-input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" value="<?= Helpers::esc($old['email'] ?? '') ?>" required autocomplete="email" inputmode="email">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label small fw-semibold">मोबाइल नंबर / Mobile Number *</label>
                                <div class="tsp-auth-input-group">
                                    <span class="input-group-text">+91</span>
                                    <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="9876543210" maxlength="10" pattern="[6-9]\d{9}" value="<?= Helpers::esc($old['mobile'] ?? '') ?>" required autocomplete="tel" inputmode="numeric">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label small fw-semibold">पता / Address *</label>
                                <textarea name="address" id="address" class="form-control" rows="2" placeholder="House, street, area" required autocomplete="street-address"><?= Helpers::esc($old['address'] ?? '') ?></textarea>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-4">
                                    <label for="city" class="form-label small fw-semibold">शहर / City</label>
                                    <input type="text" name="city" id="city" class="form-control" value="<?= Helpers::esc($old['city'] ?? '') ?>" autocomplete="address-level2">
                                </div>
                                <div class="col-sm-4">
                                    <label for="जिला" class="form-label small fw-semibold">जिला / District</label>
                                    <input type="text" name="district" id="district" class="form-control" value="<?= Helpers::esc($old['district'] ?? '') ?>" autocomplete="address-level1">
                                </div>
                                <div class="col-sm-4">
                                    <label for="pincode" class="form-label small fw-semibold">पिनकोड / Pincode</label>
                                    <input type="text" name="pincode" id="pincode" class="form-control" maxlength="6" pattern="\d{6}" value="<?= Helpers::esc($old['pincode'] ?? '') ?>" autocomplete="postal-code" inputmode="numeric">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-2">लिंग / Gender</label>
                                <div class="tsp-role-selector">
                                    <input type="radio" name="gender" id="genderMale" value="Male" <?= ($old['gender'] ?? '') === 'Male' ? 'checked' : '' ?> checked>
                                    <label for="genderMale"><i class="bi bi-gender-male"></i> पुरुष / Male</label>
                                    
                                    <input type="radio" name="gender" id="genderFemale" value="Female" <?= ($old['gender'] ?? '') === 'Female' ? 'checked' : '' ?>>
                                    <label for="genderFemale"><i class="bi bi-gender-female"></i> महिला / Female</label>
                                </div>
                            </div>
                            <div class="row g-2 mb-4">
                                <div class="col-sm-6">
                                    <label for="password" class="form-label small fw-semibold">पासवर्ड / Password *</label>
                                    <div class="tsp-auth-input-group tsp-password-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Min 6 chars" minlength="6" required>
                                        <?php require VIEW_PATH . '/layouts/password-toggle.php'; ?>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="password_confirm" class="form-label small fw-semibold">पुष्टि करें / Confirm *</label>
                                    <div class="tsp-auth-input-group tsp-password-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Re-enter password" minlength="6" required autocomplete="new-password">
                                        <?php require VIEW_PATH . '/layouts/password-toggle.php'; ?>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn tsp-btn w-100 mt-2 mb-2 justify-content-center" style="background:var(--nav-red); border-color:var(--nav-red);"><i class="bi bi-person-plus me-1"></i> खाता बनाएं / Register</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Already have an account? <a href="/login" class="fw-semibold" style="color:var(--nav-red);">Sign in</a></small>
                        </div>
                    </div>
                </div>
                <p class="text-center small text-muted mt-4"><i class="bi bi-shield-lock me-1"></i> Secure registration · Tamboli Samaj Vikas Sanstha, Rajasthan</p>
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

    // Also hook into form submit to catch native validation
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

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

<main class="min-vh-100 d-flex align-items-center py-5" style="background: var(--bg);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-7 col-lg-6 col-xl-5">
                <div class="text-center mb-4">
                    <img src="/assets/images/logo/logo-placeholder.svg" alt="logo" class="tsp-top-logo mb-3" style="width:5.5rem;height:5.5rem;">
                    <h1 class="h4 fw-bold" style="color:var(--g);">Student Registration</h1>
                    <p class="small text-muted">Create your account to apply for scholarships</p>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <form action="/register" method="post">
                            <?= Csrf::field() ?>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label for="first_name" class="form-label small fw-semibold">First Name *</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First name" value="<?= Helpers::esc($old['first_name'] ?? '') ?>" required autofocus>
                                </div>
                                <div class="col-sm-6">
                                    <label for="last_name" class="form-label small fw-semibold">Last Name *</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last name" value="<?= Helpers::esc($old['last_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="father_name" class="form-label small fw-semibold">Father/Guardian Name *</label>
                                <input type="text" name="father_name" id="father_name" class="form-control" placeholder="Father or guardian name" value="<?= Helpers::esc($old['father_name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" value="<?= Helpers::esc($old['email'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label small fw-semibold">Mobile Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text">+91</span>
                                    <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="9876543210" maxlength="10" pattern="[6-9]\d{9}" value="<?= Helpers::esc($old['mobile'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label small fw-semibold">Address *</label>
                                <textarea name="address" id="address" class="form-control" rows="2" placeholder="House, street, area" required><?= Helpers::esc($old['address'] ?? '') ?></textarea>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-4">
                                    <label for="city" class="form-label small fw-semibold">City</label>
                                    <input type="text" name="city" id="city" class="form-control" value="<?= Helpers::esc($old['city'] ?? '') ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label for="district" class="form-label small fw-semibold">District</label>
                                    <input type="text" name="district" id="district" class="form-control" value="<?= Helpers::esc($old['district'] ?? '') ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label for="pincode" class="form-label small fw-semibold">Pincode</label>
                                    <input type="text" name="pincode" id="pincode" class="form-control" maxlength="6" pattern="\d{6}" value="<?= Helpers::esc($old['pincode'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Gender</label>
                                <div class="d-flex gap-2">
                                    <input type="radio" class="btn-check" name="gender" id="genderMale" value="Male" <?= ($old['gender'] ?? '') === 'Male' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-secondary flex-fill" for="genderMale">Male</label>
                                    <input type="radio" class="btn-check" name="gender" id="genderFemale" value="Female" <?= ($old['gender'] ?? '') === 'Female' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-secondary flex-fill" for="genderFemale">Female</label>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label for="password" class="form-label small fw-semibold">Password *</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Min 6 characters" minlength="6" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="password_confirm" class="form-label small fw-semibold">Confirm *</label>
                                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Re-enter password" minlength="6" required>
                                </div>
                            </div>
                            <button type="submit" class="tsp-btn w-100 mt-2 mb-2"><i class="bi bi-person-plus me-1"></i> Create Account</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Already have an account? <a href="/login" class="fw-semibold" style="color:var(--g);">Sign in</a></small>
                        </div>
                    </div>
                </div>
                <p class="text-center small text-muted mt-3"><i class="bi bi-shield-lock me-1"></i> Secure registration · Tamboli Samaj Vikas Sanstha</p>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

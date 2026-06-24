<?php
use App\Core\Csrf;
use App\Core\Helpers;

$oldEmail = \App\Core\Flash::get('old_email');
$emailVal = !empty($oldEmail) ? $oldEmail[0] : '';

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<main class="min-vh-100 d-flex align-items-center py-5" style="background: var(--bg);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <img src="/assets/images/logo/logo-placeholder.svg" alt="logo" class="tsp-top-logo mb-3" style="width:6rem;height:6rem;">
                    <h1 class="h4 fw-bold" style="color:var(--g);">Tamboli Samaj Portal</h1>
                    <p class="small text-muted">Sign in to your account</p>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <form action="/login" method="post">
                            <?= Csrf::field() ?>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted text-uppercase">Login As</label>
                                <div class="d-flex gap-2">
                                    <input type="radio" class="btn-check" name="role" id="roleStudent" value="student" checked>
                                    <label class="btn btn-outline-success flex-fill" for="roleStudent"><i class="bi bi-mortarboard me-1"></i> Student</label>
                                    <input type="radio" class="btn-check" name="role" id="roleAdmin" value="admin">
                                    <label class="btn btn-outline-success flex-fill" for="roleAdmin"><i class="bi bi-shield-lock me-1"></i> Admin</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" value="<?= Helpers::esc($emailVal) ?>" required autofocus>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label small fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                            <button type="submit" class="tsp-btn w-100 mt-2 mb-2"><i class="bi bi-box-arrow-in-right me-1"></i> Sign In</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Don't have an account? <a href="/register" class="fw-semibold" style="color:var(--g);">Register here</a></small>
                        </div>
                    </div>
                </div>
                <p class="text-center small text-muted mt-3"><i class="bi bi-shield-check me-1"></i> Secure login · Tamboli Samaj Vikas Sanstha, Rajasthan</p>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

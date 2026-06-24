<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helpers;

$activeSession = $activeSession ?? [];
$old = Flash::get('old');
$old = $old[0] ?? [];

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<main class="tsp-sec bg-white min-vh-100">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-3 d-none d-lg-block">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="p-3 text-center border-bottom" style="background: var(--tsp-green); border-radius: 0.8rem 0.8rem 0 0;">
                            <div class="bg-white rounded-circle d-inline-flex p-1 mb-2">
                                <img src="/assets/images/logo/logo-placeholder.svg" width="36" height="36" alt="logo">
                            </div>
                            <div class="text-white fw-semibold small"><?= Helpers::esc(Auth::userName()) ?></div>
                            <div class="text-white-50" style="font-size: 0.75rem;">Student</div>
                        </div>
                        <nav class="nav flex-column p-2">
                            <a class="nav-link text-muted" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                            <a class="nav-link text-muted" href="/profile"><i class="bi bi-person me-2"></i> My Profile</a>
                            <a class="nav-link active fw-semibold" href="/applications" style="color: var(--tsp-green);"><i class="bi bi-file-earmark-text me-2"></i> Applications</a>
                            <a class="nav-link text-muted" href="/announcements"><i class="bi bi-megaphone me-2"></i> Announcements</a>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <a href="/applications/create" class="small text-muted text-decoration-none d-block mb-3">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>

                <h2 class="h4 fw-bold mb-1">Scholarship Application</h2>
                <p class="text-muted small mb-4">Session: <?= Helpers::esc($activeSession['session_name'] ?? 'N/A') ?></p>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <form action="/applications/scholarship" method="post" enctype="multipart/form-data">
                            <?= Csrf::field() ?>

                            <h5 class="fw-bold mb-3">Academic Details</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="class_year" class="form-label small fw-semibold">Class/Year *</label>
                                    <select name="class_year" id="class_year" class="form-select" required>
                                        <option value="">Select</option>
                                        <?php foreach (['10th', '12th', 'Graduation', 'Post Graduation'] as $cy): ?>
                                            <option value="<?= $cy ?>" <?= ($old['class_year'] ?? '') === $cy ? 'selected' : '' ?>>
                                                <?= $cy ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="percentage" class="form-label small fw-semibold">Percentage *</label>
                                    <input type="number" name="percentage" id="percentage"
                                           class="form-control" step="0.01" min="0" max="100"
                                           placeholder="e.g. 75.00"
                                           value="<?= Helpers::esc($old['percentage'] ?? '') ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="college_name" class="form-label small fw-semibold">College/School Name</label>
                                    <input type="text" name="college_name" id="college_name"
                                           class="form-control" value="<?= Helpers::esc($old['college_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="board_university" class="form-label small fw-semibold">Board/University</label>
                                    <input type="text" name="board_university" id="board_university"
                                           class="form-control" value="<?= Helpers::esc($old['board_university'] ?? '') ?>">
                                </div>
                            </div>

                            <hr class="my-3">

                            <h5 class="fw-bold mb-3">Required Documents</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="marksheet" class="form-label small fw-semibold">Marksheet *</label>
                                    <input type="file" name="marksheet" id="marksheet" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="form-text">JPG, PNG, or PDF. PDF up to 5 MB, image up to 2 MB.</div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="passbook" class="form-label small fw-semibold">Bank Passbook *</label>
                                    <input type="file" name="passbook" id="passbook" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="form-text">Upload the page with account holder, account number, and IFSC.</div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <h5 class="fw-bold mb-3">Bank Details</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="bank_name" class="form-label small fw-semibold">Bank Name *</label>
                                    <input type="text" name="bank_name" id="bank_name"
                                           class="form-control" value="<?= Helpers::esc($old['bank_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="account_number" class="form-label small fw-semibold">Account Number *</label>
                                    <input type="text" name="account_number" id="account_number"
                                           class="form-control" value="<?= Helpers::esc($old['account_number'] ?? '') ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="ifsc_code" class="form-label small fw-semibold">IFSC Code *</label>
                                    <input type="text" name="ifsc_code" id="ifsc_code"
                                           class="form-control" value="<?= Helpers::esc($old['ifsc_code'] ?? '') ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="family_income" class="form-label small fw-semibold">Annual Family Income</label>
                                    <input type="number" name="family_income" id="family_income"
                                           class="form-control" step="0.01"
                                           value="<?= Helpers::esc($old['family_income'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end mt-4">
                                <a href="/applications/create" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn tsp-btn px-4">
                                    <i class="bi bi-check-lg me-1"></i> Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

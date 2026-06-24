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

                <h2 class="h4 fw-bold mb-1">Pratibha Samman Registration</h2>
                <p class="text-muted small mb-4">Session: <?= Helpers::esc($activeSession['session_name'] ?? 'N/A') ?></p>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <form action="/applications/pratibha" method="post" enctype="multipart/form-data">
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
                                           placeholder="e.g. 85.00"
                                           value="<?= Helpers::esc($old['percentage'] ?? '') ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="college_name" class="form-label small fw-semibold">College/School</label>
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
                                    <label for="certificate" class="form-label small fw-semibold">Achievement Certificate *</label>
                                    <input type="file" name="certificate" id="certificate" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="form-text">Upload certificate, result proof, or official award document.</div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <h5 class="fw-bold mb-3">Achievement Details</h5>
                            <div class="row g-3">
                                <div class="col-sm-8">
                                    <label for="achievement_title" class="form-label small fw-semibold">Achievement Title *</label>
                                    <input type="text" name="achievement_title" id="achievement_title"
                                           class="form-control" placeholder="e.g. District Level Science Exhibition"
                                           value="<?= Helpers::esc($old['achievement_title'] ?? '') ?>" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="rank_position" class="form-label small fw-semibold">Rank/Position</label>
                                    <input type="text" name="rank_position" id="rank_position"
                                           class="form-control" placeholder="e.g. 1st"
                                           value="<?= Helpers::esc($old['rank_position'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="achievement_category" class="form-label small fw-semibold">Category</label>
                                    <select name="achievement_category" id="achievement_category" class="form-select">
                                        <option value="">Select</option>
                                        <?php foreach (['Academic', 'Sports', 'Cultural', 'Science', 'Arts', 'Other'] as $cat): ?>
                                            <option value="<?= $cat ?>" <?= ($old['achievement_category'] ?? '') === $cat ? 'selected' : '' ?>>
                                                <?= $cat ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="achievement_level" class="form-label small fw-semibold">Level</label>
                                    <select name="achievement_level" id="achievement_level" class="form-select">
                                        <option value="">Select</option>
                                        <?php foreach (['School', 'District', 'State', 'National', 'International'] as $lvl): ?>
                                            <option value="<?= $lvl ?>" <?= ($old['achievement_level'] ?? '') === $lvl ? 'selected' : '' ?>>
                                                <?= $lvl ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end mt-4">
                                <a href="/applications/create" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn tsp-btn px-4">
                                    <i class="bi bi-check-lg me-1"></i> Submit Registration
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

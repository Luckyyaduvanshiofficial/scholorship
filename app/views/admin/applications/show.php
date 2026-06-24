<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$app = $application ?? [];
$statusColor = function(string $s): string {
    return match($s) {
        'Approved' => 'success',
        'Rejected' => 'danger',
        'Disputed' => 'warning',
        default    => 'secondary',
    };
};

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
                        <div class="p-3 text-center border-bottom" style="background: var(--tsp-green-dark); border-radius: 0.8rem 0.8rem 0 0;">
                            <div class="bg-white rounded-circle d-inline-flex p-1 mb-2">
                                <img src="/assets/images/logo/logo-placeholder.svg" width="36" height="36" alt="logo">
                            </div>
                            <div class="text-white fw-semibold small"><?= Helpers::esc(Auth::userName()) ?></div>
                            <div class="text-white-50" style="font-size: 0.75rem;">Admin</div>
                        </div>
                        <nav class="nav flex-column p-2">
                            <a class="nav-link text-muted" href="/admin"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                            <a class="nav-link text-muted" href="/admin/students"><i class="bi bi-people me-2"></i> Students</a>
                            <a class="nav-link active fw-semibold" href="/admin/applications" style="color: var(--tsp-green);"><i class="bi bi-file-earmark-text me-2"></i> Applications</a>
                            <a class="nav-link text-muted" href="/admin/announcements"><i class="bi bi-megaphone me-2"></i> Announcements</a>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <a href="/admin/applications" class="small text-muted text-decoration-none d-block mb-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Applications
                </a>

                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <h2 class="h4 fw-bold mb-0">Review Application</h2>
                    <span class="badge rounded-pill bg-<?= $statusColor($app['status_name'] ?? 'Pending') ?> fs-6">
                        <?= Helpers::esc($app['status_name'] ?? 'Pending') ?>
                    </span>
                </div>
                <p class="text-muted small mb-4">
                    ID: <strong>TSVS-<?= date('Y') ?>-<?= str_pad((string) ($app['id'] ?? 0), 6, '0', STR_PAD_LEFT) ?></strong>
                    &middot; Submitted: <?= !empty($app['submitted_at']) ? date('d M Y, h:i A', strtotime($app['submitted_at'])) : 'N/A' ?>
                </p>

                <!-- Action buttons -->
                <?php if (($app['status_name'] ?? '') === 'Pending' || ($app['status_name'] ?? '') === 'Disputed'): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-3">Actions</h5>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <form action="/admin/applications/<?= (int) $app['id'] ?>/approve" method="post">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle me-1"></i> Approve
                                    </button>
                                </form>
                            </div>
                            <div class="col-sm-4">
                                <form action="/admin/applications/<?= (int) $app['id'] ?>/reject" method="post">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-x-circle me-1"></i> Reject
                                    </button>
                                </form>
                            </div>
                            <div class="col-sm-4">
                                <button type="button" class="btn btn-warning w-100" data-bs-toggle="collapse" data-bs-target="#disputeForm">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Dispute
                                </button>
                            </div>
                        </div>

                        <div class="collapse mt-3" id="disputeForm">
                            <div class="card card-body bg-light border-0">
                                <form action="/admin/applications/<?= (int) $app['id'] ?>/dispute" method="post">
                                    <?= Csrf::field() ?>
                                    <label class="form-label small fw-semibold">Dispute Message</label>
                                    <textarea name="dispute_message" class="form-control mb-2" rows="2"
                                              placeholder="Explain why this application is being disputed..." required></textarea>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-send me-1"></i> Mark as Disputed
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Application details -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-3">Student Details</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="small text-muted d-block">Name</label>
                                <span class="fw-medium"><?= Helpers::esc(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></span>
                            </div>
                            <div class="col-sm-6">
                                <label class="small text-muted d-block">Student Code</label>
                                <span class="fw-medium"><?= Helpers::esc($app['student_code'] ?? '-') ?></span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                            <h5 class="fw-bold mb-3">Bank Details</h5>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block">Bank Name</label>
                                    <span class="fw-medium"><?= Helpers::esc($app['bank_name'] ?? '-') ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block">Account Number</label>
                                    <span class="fw-medium"><?= Helpers::esc($app['account_number'] ?? '-') ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block">IFSC Code</label>
                                    <span class="fw-medium"><?= Helpers::esc($app['ifsc_code'] ?? '-') ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block">Annual Family Income</label>
                                    <span class="fw-medium"><?= !empty($app['family_income']) ? '₹ ' . number_format((float) $app['family_income'], 2) : '-' ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <h5 class="fw-bold mb-3">Achievement Details</h5>
                            <div class="row g-3">
                                <div class="col-sm-8">
                                    <label class="small text-muted d-block">Achievement</label>
                                    <span class="fw-medium"><?= Helpers::esc($app['achievement_title'] ?? '-') ?></span>
                                </div>
                                <div class="col-sm-4">
                                    <label class="small text-muted d-block">Rank</label>
                                    <span class="fw-medium"><?= Helpers::esc($app['rank_position'] ?? '-') ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block">Category</label>
                                    <span class="fw-medium"><?= Helpers::esc($app['achievement_category'] ?? '-') ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block">Level</label>
                                    <span class="fw-medium"><?= Helpers::esc($app['achievement_level'] ?? '-') ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <hr class="my-3">

                        <h5 class="fw-bold mb-3">Submitted Documents</h5>
                        <?php if (empty($app['documents'])): ?>
                            <p class="text-muted mb-0">No documents are attached to this application.</p>
                        <?php else: ?>
                            <div class="row g-2">
                                <?php foreach ($app['documents'] as $document): ?>
                                    <div class="col-sm-6">
                                        <div class="border rounded p-3 h-100">
                                            <div class="fw-semibold">
                                                <i class="bi bi-file-earmark-check me-1" style="color: var(--g);"></i>
                                                <?= Helpers::esc($document['document_type'] ?? 'Document') ?>
                                            </div>
                                            <div class="small text-muted">
                                                <a href="/uploads/applications/<?= $app['id'] ?>/<?= $document['stored_name'] ?>" target="_blank" class="text-decoration-underline text-primary fw-semibold">
                                                    <?= Helpers::esc($document['original_name'] ?? '') ?>
                                                </a>
                                            </div>
                                            <div class="small text-muted">
                                                Verification: <?= Helpers::esc($document['verification_status'] ?? 'pending') ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($app['dispute_message'])): ?>
                        <hr class="my-3">
                        <div class="alert alert-warning mb-0">
                            <strong>Current Dispute Message:</strong><br>
                            <?= Helpers::esc($app['dispute_message']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

<?php
use App\Core\Auth;
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
                <a href="/applications" class="small text-muted text-decoration-none d-block mb-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Applications
                </a>

                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <h2 class="h4 fw-bold mb-0">
                        <?= Helpers::esc($app['app_type_name'] ?? ucfirst($app['type'] ?? '')) ?>
                    </h2>
                    <span class="badge rounded-pill bg-<?= $statusColor($app['status_name'] ?? 'Pending') ?> fs-6">
                        <?= Helpers::esc($app['status_name'] ?? 'Pending') ?>
                    </span>
                </div>
                <p class="text-muted small mb-4">
                    Application ID: <strong>TSVS-<?= date('Y') ?>-<?= str_pad((string) ($app['id'] ?? 0), 6, '0', STR_PAD_LEFT) ?></strong>
                    &middot; Session: <?= Helpers::esc($app['session_name'] ?? 'N/A') ?>
                    &middot; Submitted: <?= !empty($app['submitted_at']) ? date('d M Y, h:i A', strtotime($app['submitted_at'])) : 'N/A' ?>
                </p>

                <?php if (!empty($app['dispute_message'])): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-1"></i> Dispute Message from Admin</h6>
                    <p class="mb-0"><?= Helpers::esc($app['dispute_message']) ?></p>
                </div>
                <?php endif; ?>

                <?php if (($app['status_name'] ?? '') === 'Disputed'): ?>
                <div class="card border-0 shadow-sm border-start border-warning border-4 mb-4">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold text-warning mb-2">
                            <i class="bi bi-arrow-counterclockwise"></i> विवाद समाधान एवं पुनः प्रस्तुति / Resolve & Resubmit
                        </h5>
                        <p class="text-muted small mb-3">
                            कृपया त्रुटि निवारण के लिए आवश्यक संशोधित दस्तावेज़ यहाँ अपलोड करें। आप एक या अधिक दस्तावेज़ अपडेट कर सकते हैं।
                            <br><span class="fst-italic">Please upload the corrected documents below to resolve the dispute and resubmit your application.</span>
                        </p>

                        <form action="/applications/<?= (int) $app['id'] ?>/resubmit" method="POST" enctype="multipart/form-data">
                            <?= \App\Core\Csrf::field() ?>
                            
                            <div class="row g-3 mb-3">
                                <!-- Marksheet (applicable to both types) -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">संशोधित अंकतालिका अपलोड करें / Upload Corrected Marksheet (JPG/PNG/PDF)</label>
                                    <input type="file" name="marksheet" class="form-control">
                                </div>

                                <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                    <!-- Passbook (scholarship only) -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">बैंक पासबुक अपलोड करें / Upload Corrected Bank Passbook (JPG/PNG/PDF)</label>
                                        <input type="file" name="passbook" class="form-control">
                                    </div>
                                <?php else: ?>
                                    <!-- Certificate (pratibha only) -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">योग्यता प्रमाणपत्र अपलोड करें / Upload Corrected Certificate (JPG/PNG/PDF)</label>
                                        <input type="file" name="certificate" class="form-control">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-warning fw-bold text-dark">
                                <i class="bi bi-send-fill me-1"></i> दस्तावेज़ सबमिट करें / Submit Documents & Resubmit
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

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
                                    <label class="small text-muted d-block">Achievement Title</label>
                                    <span class="fw-medium"><?= Helpers::esc($app['achievement_title'] ?? '-') ?></span>
                                </div>
                                <div class="col-sm-4">
                                    <label class="small text-muted d-block">Rank / Position</label>
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
                                                <?= Helpers::esc($document['original_name'] ?? '') ?>
                                            </div>
                                            <div class="small text-muted">
                                                Status: <?= Helpers::esc($document['verification_status'] ?? 'pending') ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

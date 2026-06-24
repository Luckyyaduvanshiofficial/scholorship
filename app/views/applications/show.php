<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$app = $application ?? [];
$statusBadgeClass = function(string $statusName): string {
    return match($statusName) {
        'Approved' => 'bg-success-subtle text-success border border-success-subtle',
        'Rejected' => 'bg-danger-subtle text-danger border border-danger-subtle',
        'Disputed' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        default    => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
    };
};

$statusTranslate = function(string $statusName): string {
    return match($statusName) {
        'Approved' => 'स्वीकृत (Approved)',
        'Rejected' => 'अस्वीकृत (Rejected)',
        'Disputed' => 'त्रुटि/विवाद (Action Required)',
        default    => 'लंबित (Pending)',
    };
};

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<!-- Dashboard Top Header -->
<header class="tsp-dash-header">
    <!-- Left: Menu Toggle Button -->
    <button class="tsp-dash-menu-toggle" id="tspSidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Center: Logo & Bilingual Title -->
    <div class="tsp-dash-logo-title-group d-flex flex-column align-items-center">
        <div class="d-flex align-items-center gap-2 mb-1">
            <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj Logo" width="36" height="36">
            <h1 class="tsp-dash-title-hi">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</h1>
        </div>
        <span class="tsp-dash-title-en">TAMBOLI SAMAJ VIKAS SANSTHA, RAJASTHAN</span>
    </div>

    <!-- Right: Student Profile Block & Logout -->
    <div class="tsp-dash-profile-block">
        <div class="tsp-dash-profile-info d-none d-md-flex align-items-end me-1">
            <span class="tsp-dash-profile-name"><?= Helpers::esc(Auth::userName()) ?></span>
            <span class="tsp-dash-profile-code"><?= Helpers::esc(Auth::studentCode()) ?></span>
        </div>
        <div class="tsp-dash-avatar me-2">
            <i class="bi bi-person-fill fs-5"></i>
        </div>
        <form action="/logout" method="post" class="m-0">
            <?= Csrf::field() ?>
            <button type="submit" class="tsp-dash-logout-btn shadow-sm">
                <i class="bi bi-box-arrow-right"></i>
                <span>लॉगआउट</span>
            </button>
        </form>
    </div>
</header>

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <!-- Sidebar -->
    <aside class="tsp-dash-sidebar" id="tspSidebar">
        <a href="/dashboard" class="tsp-dash-sidebar-link">
            <i class="bi bi-house-door-fill"></i>
            <span>डैशबोर्ड</span>
        </a>
        <a href="/applications/create" class="tsp-dash-sidebar-link">
            <i class="bi bi-pencil-square"></i>
            <span>आवेदन फॉर्म भरें</span>
        </a>
        <a href="/applications" class="tsp-dash-sidebar-link active">
            <i class="bi bi-file-earmark-text"></i>
            <span>मेरे आवेदन</span>
        </a>
        <a href="/applications" class="tsp-dash-sidebar-link">
            <i class="bi bi-clock-history"></i>
            <span>आवेदन की स्थिति</span>
        </a>
        <a href="/dashboard#help" class="tsp-dash-sidebar-link">
            <i class="bi bi-question-circle"></i>
            <span>सहायता</span>
        </a>
    </aside>

    <!-- Main Content Area -->
    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">
            
            <!-- Breadcrumbs and Action Header -->
            <div class="mb-4">
                <a href="/applications" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>आवेदनों की सूची पर वापस जाएं / Back to Applications</span>
                </a>
            </div>

            <!-- Page Title and Status -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="tsp-dash-welcome-title fs-3 mb-1">
                        <?= Helpers::esc($app['app_type_name'] ?? ucfirst($app['type'] ?? '')) ?>
                    </h2>
                    <p class="text-muted small mb-0">
                        आवेदन आईडी: <strong class="text-dark">TSVS-<?= date('Y') ?>-<?= str_pad((string) ($app['id'] ?? 0), 6, '0', STR_PAD_LEFT) ?></strong>
                        &middot; सत्र: <strong class="text-dark"><?= Helpers::esc($app['session_name'] ?? 'N/A') ?></strong>
                        &middot; जमा तिथि: <strong class="text-dark"><?= !empty($app['submitted_at']) ? date('d M Y, h:i A', strtotime($app['submitted_at'])) : 'N/A' ?></strong>
                    </p>
                </div>
                <span class="badge py-2 px-3 rounded-pill fw-semibold fs-6 <?= $statusBadgeClass($app['status_name'] ?? 'Pending') ?>">
                    <?= $statusTranslate($app['status_name'] ?? 'Pending') ?>
                </span>
            </div>

            <!-- Dispute Warning Notice -->
            <?php if (!empty($app['dispute_message'])): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4 p-4" style="border-radius: 1rem; border-left: 5px solid #d97706 !important;">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">संशोधन की आवश्यकता (Action Required)</h5>
                            <p class="mb-0 text-muted-800 small"><?= Helpers::esc($app['dispute_message']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Dispute Resolution Resubmission Form -->
            <?php if (($app['status_name'] ?? '') === 'Disputed'): ?>
                <div class="card border-0 shadow-sm border-start border-warning border-4 mb-4" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis rounded-circle" style="width: 36px; height: 36px;">
                                <i class="bi bi-arrow-counterclockwise fs-5"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0 text-dark">विवाद समाधान एवं पुनः प्रस्तुति / Resolve & Resubmit</h4>
                        </div>
                        <p class="text-muted small mb-4" style="line-height: 1.6;">
                            कृपया त्रुटि निवारण के लिए आवश्यक संशोधित दस्तावेज़ यहाँ अपलोड करें। आप एक या अधिक दस्तावेज़ अपडेट कर सकते हैं।
                            <br><span class="fst-italic text-muted">Please upload the corrected documents below to resolve the dispute and resubmit your application.</span>
                        </p>

                        <form action="/applications/<?= (int) $app['id'] ?>/resubmit" method="POST" enctype="multipart/form-data">
                            <?= Csrf::field() ?>
                            
                            <div class="row g-4 mb-4">
                                <!-- Marksheet -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">संशोधित अंकतालिका अपलोड करें / Upload Corrected Marksheet (JPG/PNG/PDF)</label>
                                    <input type="file" name="marksheet" class="form-control border-2 py-2" style="border-radius: 0.5rem;" accept=".jpg,.jpeg,.png,.pdf">
                                </div>

                                <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                    <!-- Passbook -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">बैंक पासबुक अपलोड करें / Upload Corrected Bank Passbook (JPG/PNG/PDF)</label>
                                        <input type="file" name="passbook" class="form-control border-2 py-2" style="border-radius: 0.5rem;" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                <?php else: ?>
                                    <!-- Certificate -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">योग्यता प्रमाणपत्र अपलोड करें / Upload Corrected Certificate (JPG/PNG/PDF)</label>
                                        <input type="file" name="certificate" class="form-control border-2 py-2" style="border-radius: 0.5rem;" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2.5 rounded-pill shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> दस्तावेज़ सबमिट करें / Submit Documents
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Application Details Grid -->
            <div class="row g-4">
                <!-- Left: Application Details Form Data -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                        <div class="card-body p-4 p-md-5">
                            
                            <!-- Student Section -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 30px; height: 30px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <h4 class="h6 fw-bold mb-0 text-dark">विद्यार्थी का विवरण / Student Details</h4>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <span class="small text-muted d-block mb-1">विद्यार्थी का नाम (Name)</span>
                                    <span class="fw-semibold text-dark"><?= Helpers::esc(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="small text-muted d-block mb-1">विद्यार्थी कोड (Student Code)</span>
                                    <span class="fw-semibold text-dark"><?= Helpers::esc($app['student_code'] ?? '-') ?></span>
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: #e2e8f0;">

                            <!-- Type-Specific Details -->
                            <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 30px; height: 30px;">
                                        <i class="bi bi-bank"></i>
                                    </div>
                                    <h4 class="h6 fw-bold mb-0 text-dark">बैंक खाता विवरण / Bank Details</h4>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">बैंक का नाम (Bank Name)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['bank_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">खाता संख्या (Account Number)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['account_number'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">IFSC कोड (IFSC Code)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['ifsc_code'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">वार्षिक पारिवारिक आय (Annual Family Income)</span>
                                        <span class="fw-semibold text-dark"><?= !empty($app['family_income']) ? '₹ ' . number_format((float) $app['family_income'], 2) : '-' ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 30px; height: 30px;">
                                        <i class="bi bi-trophy"></i>
                                    </div>
                                    <h4 class="h6 fw-bold mb-0 text-dark">उपलब्धि विवरण / Achievement Details</h4>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-8">
                                        <span class="small text-muted d-block mb-1">उपलब्धि का नाम (Achievement Title)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['achievement_title'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="small text-muted d-block mb-1">रैंक / स्थान (Rank / Position)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['rank_position'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">श्रेणी (Category)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['achievement_category'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">स्तर (Level)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['achievement_level'] ?? '-') ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <!-- Right: Submitted Documents Checklist -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                        <div class="card-body p-4">
                            <h4 class="h6 fw-bold text-dark mb-3">अपलोड किए गए दस्तावेज़ / Uploaded Documents</h4>
                            
                            <?php if (empty($app['documents'])): ?>
                                <p class="text-muted small mb-0">कोई दस्तावेज़ संलग्न नहीं है।</p>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($app['documents'] as $document): ?>
                                        <div class="p-3 border rounded" style="background: #f8fafc; border-radius: 0.75rem !important;">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="bi bi-file-earmark-check fs-5 text-success"></i>
                                                <span class="fw-bold text-dark small">
                                                    <?= Helpers::esc($document['document_type'] ?? 'Document') ?>
                                                </span>
                                            </div>
                                            <div class="text-muted small text-truncate mb-2" title="<?= Helpers::esc($document['original_name'] ?? '') ?>">
                                                <?= Helpers::esc($document['original_name'] ?? '') ?>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="small text-muted">स्थिति / Status:</span>
                                                <span class="badge rounded-pill fw-semibold bg-light text-dark border small">
                                                    <?= Helpers::esc($document['verification_status'] ?? 'pending') ?>
                                                </span>
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
</div>

<!-- Inline Sidebar Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('tspSidebarToggle');
    const sidebar = document.getElementById('tspSidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('collapsed');
        });
    }
    
    // Auto collapse sidebar on small screens when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991.98) {
            if (sidebar && !sidebar.classList.contains('collapsed') && !sidebar.contains(e.target) && e.target !== toggleBtn) {
                sidebar.classList.add('collapsed');
            }
        }
    });
});
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

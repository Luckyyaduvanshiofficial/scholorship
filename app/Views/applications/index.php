<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$applications = $applications ?? [];
$statusBadgeClass = function(string $statusName): string {
    return match($statusName) {
        'Draft'              => 'bg-secondary text-white',
        'Submitted'          => 'bg-primary text-white',
        'Under Review'       => 'bg-warning text-dark',
        'Approved'           => 'bg-success text-white',
        'Rejected'           => 'bg-danger text-white',
        'Pending Correction' => 'bg-orange',
        'Resubmitted'        => 'bg-purple',
        default              => 'bg-secondary text-white',
    };
};

$statusTranslate = function(string $statusName): string {
    return match($statusName) {
        'Draft'              => 'ड्राफ्ट (Draft)',
        'Submitted'        => 'जमा किया गया (Submitted)',
        'Under Review'     => 'समीक्षाधीन (Under Review)',
        'Approved'         => 'स्वीकृत (Approved)',
        'Rejected'         => 'अस्वीकृत (Rejected)',
        'Pending Correction' => 'सुधार लंबित (Pending Correction)',
        'Resubmitted'      => 'पुनः जमा (Resubmitted)',
        default            => $statusName,
    };
};

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <?php
    $activeLink = 'applications';
    require VIEW_PATH . '/layouts/student-sidebar.php';
    ?>

    <!-- Main Content Area -->
    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">
            
            <!-- Breadcrumbs and Action Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="tsp-dash-welcome-title fs-3 mb-1">मेरे आवेदन (My Applications)</h2>
                    <p class="text-muted small mb-0">अपने छात्रवृत्ति एवं प्रतिभा सम्मान आवेदनों की स्थिति यहाँ ट्रैक करें।</p>
                </div>
                <a href="/dashboard/applications/create" class="btn tsp-dash-welcome-btn shadow-sm py-2 px-3">
                    <i class="bi bi-plus-lg me-1"></i> नया आवेदन करें
                </a>
            </div>

            <?php if (empty($applications)): ?>
                <!-- Empty State Card -->
                <div class="card border-0 shadow-sm text-center py-5 px-4" style="border-radius: 1rem;">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 80px; height: 80px;">
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">कोई आवेदन नहीं मिला</h5>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 400px;">
                        आपने अभी तक प्रतिभा सम्मान या छात्रवृत्ति के लिए कोई आवेदन नहीं किया है। शुरू करने के लिए नीचे दिए गए बटन पर क्लिक करें।
                    </p>
                    <div>
                        <a href="/dashboard/applications/create" class="btn tsp-dash-welcome-btn shadow-sm py-2 px-4">
                            <i class="bi bi-pencil-square me-1"></i> आवेदन फॉर्म भरें
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Applications Card List -->
                <div class="row g-3">
                    <?php foreach ($applications as $app): ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden" style="border-radius: 1rem; border-left: 5px solid var(--maroon-dash) !important; transition: transform 0.2s, box-shadow 0.2s;">
                                <div class="card-body p-4">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                        <div class="d-flex gap-3 align-items-start">
                                            <!-- Application Icon -->
                                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" 
                                                 style="width: 50px; height: 50px; background: <?= $app['type'] === 'scholarship' ? '#eff6ff' : '#fffbeb' ?>; 
                                                        color: <?= $app['type'] === 'scholarship' ? '#2563eb' : '#d97706' ?>;">
                                                <?php if ($app['type'] === 'scholarship'): ?>
                                                    <i class="bi bi-mortarboard-fill fs-4"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-trophy-fill fs-4"></i>
                                                <?php endif; ?>
                                            </div>
                                            <!-- Application Meta Info -->
                                            <div>
                                                <h5 class="fw-bold mb-1 text-dark">
                                                    <?= Helpers::esc($app['app_type_name'] ?? ucfirst($app['type'])) ?>
                                                </h5>
                                                <div class="d-flex flex-wrap align-items-center gap-2 text-muted small">
                                                    <span>सत्र: <strong><?= Helpers::esc($app['session_name'] ?? 'N/A') ?></strong></span>
                                                    <span class="d-none d-sm-inline text-muted-300">&middot;</span>
                                                    <span>आवेदन आईडी: <strong>TSVS-<?= date('Y') ?>-<?= str_pad((string) $app['id'], 6, '0', STR_PAD_LEFT) ?></strong></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Badge & Action -->
                                        <div class="d-flex flex-column align-items-md-end gap-2">
                                            <span class="badge py-2 px-3 rounded-pill fw-semibold <?= $statusBadgeClass($app['status_name'] ?? 'Pending') ?>">
                                                <?= $statusTranslate($app['status_name'] ?? 'Pending') ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Correction Warning Message Box -->
                                    <?php if (in_array($app['status_name'] ?? '', ['Rejected', 'Pending Correction'], true) && !empty($app['rejection_reason'])): ?>
                                        <div class="mt-3 p-3 bg-warning-subtle text-warning-emphasis rounded border border-warning-subtle d-flex align-items-start gap-2 small">
                                            <i class="bi bi-exclamation-triangle-fill fs-5 mt-0.5 flex-shrink-0"></i>
                                            <div>
                                                <strong>संशोधन की आवश्यकता: </strong>
                                                <?= Helpers::esc($app['rejection_reason']) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Action Button footer in card -->
                                    <div class="mt-4 pt-3 border-top d-flex justify-content-end align-items-center">
                                        <a href="/dashboard/applications/<?= (int) $app['id'] ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
                                            <span>विवरण देखें / View Details</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>
</div>

<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

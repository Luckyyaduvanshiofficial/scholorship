<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$applications = $applications ?? [];
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
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="tsp-dash-welcome-title fs-3 mb-1">मेरे आवेदन (My Applications)</h2>
                    <p class="text-muted small mb-0">अपने छात्रवृत्ति एवं प्रतिभा सम्मान आवेदनों की स्थिति यहाँ ट्रैक करें।</p>
                </div>
                <a href="/applications/create" class="btn tsp-dash-welcome-btn shadow-sm py-2 px-3">
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
                        <a href="/applications/create" class="btn tsp-dash-welcome-btn shadow-sm py-2 px-4">
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

                                    <!-- Dispute Warning Message Box -->
                                    <?php if (($app['status_name'] ?? '') === 'Disputed' && !empty($app['dispute_message'])): ?>
                                        <div class="mt-3 p-3 bg-warning-subtle text-warning-emphasis rounded border border-warning-subtle d-flex align-items-start gap-2 small">
                                            <i class="bi bi-exclamation-triangle-fill fs-5 mt-0.5 flex-shrink-0"></i>
                                            <div>
                                                <strong>संशोधन की आवश्यकता: </strong>
                                                <?= Helpers::esc($app['dispute_message']) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Action Button footer in card -->
                                    <div class="mt-4 pt-3 border-top d-flex justify-content-end align-items-center">
                                        <a href="/applications/<?= (int) $app['id'] ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
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

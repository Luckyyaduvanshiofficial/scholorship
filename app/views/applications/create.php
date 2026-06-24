<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$types = $types ?? [];
$activeSession = $activeSession ?? [];
$existing = $existing ?? [];

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
        <a href="/applications/create" class="tsp-dash-sidebar-link active">
            <i class="bi bi-pencil-square"></i>
            <span>आवेदन फॉर्म भरें</span>
        </a>
        <a href="/applications" class="tsp-dash-sidebar-link">
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
            
            <!-- Breadcrumbs and Header -->
            <div class="mb-4">
                <a href="/dashboard" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>डैशबोर्ड पर वापस जाएं / Back to Dashboard</span>
                </a>
            </div>

            <div class="mb-4">
                <h2 class="tsp-dash-welcome-title fs-3 mb-1">नया आवेदन फॉर्म भरें / Start New Application</h2>
                <p class="text-muted small mb-0">
                    सक्रिय सत्र (Active Session): <strong class="text-dark"><?= Helpers::esc($activeSession['session_name'] ?? 'N/A') ?></strong>
                </p>
            </div>

            <!-- Selection Cards Grid -->
            <div class="row g-4">
                <?php foreach ($types as $type):
                    $isApplied = !empty($existing[$type['id']]);
                    $isScholarship = ($type['name'] === 'Scholarship');
                ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden" 
                         style="border-radius: 1.25rem; transition: transform 0.2s, box-shadow 0.2s; 
                                <?= $isApplied ? 'opacity: 0.7;' : '' ?>">
                        
                        <div class="card-body p-4 p-lg-5 text-center d-flex flex-column justify-content-between">
                            <div>
                                <!-- Icon Wrapper -->
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" 
                                     style="width: 70px; height: 70px; background: <?= $isScholarship ? '#eff6ff' : '#fffbeb' ?>; 
                                            color: <?= $isScholarship ? '#2563eb' : '#d97706' ?>;">
                                    <?php if ($isScholarship): ?>
                                        <i class="bi bi-mortarboard-fill fs-2"></i>
                                    <?php else: ?>
                                        <i class="bi bi-trophy-fill fs-2"></i>
                                    <?php endif; ?>
                                </div>

                                <!-- Title and Subtitle -->
                                <h3 class="h5 fw-bold mb-3 text-dark">
                                    <?= $isScholarship ? 'शिक्षा प्रोत्साहन छात्रवृत्ति योजना' : 'प्रतिभा सम्मान समारोह' ?>
                                    <span class="d-block fs-6 text-muted fw-normal mt-1">
                                        <?= Helpers::esc($type['name']) ?>
                                    </span>
                                </h3>

                                <p class="text-muted small mb-4" style="line-height: 1.6;">
                                    <?= $isScholarship 
                                        ? 'उच्च शिक्षा या बोर्ड कक्षाओं में अच्छे अंक प्राप्त करने वाले छात्र-छात्राओं के लिए वित्तीय सहायता योजना।' 
                                        : 'शैक्षणिक, खेलकूद, विज्ञान या कला के क्षेत्र में उल्लेखनीय प्रदर्शन करने वाले प्रतिभावान बच्चों का सम्मान।' ?>
                                </p>
                            </div>

                            <div>
                                <?php if ($isApplied): ?>
                                    <div class="d-inline-flex align-items-center gap-1 text-success fw-bold bg-success-subtle py-2 px-4 rounded-pill small border border-success-subtle">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>आप पहले ही आवेदन कर चुके हैं</span>
                                    </div>
                                <?php else: ?>
                                    <a href="/applications/<?= $isScholarship ? 'scholarship' : 'pratibha' ?>" 
                                       class="btn tsp-dash-welcome-btn shadow-sm py-2.5 px-4 w-100 rounded-pill fw-semibold">
                                        <span>आवेदन शुरू करें / Apply Now</span>
                                        <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
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

<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$role = 'student';

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
        <a href="/dashboard" class="tsp-dash-sidebar-link active">
            <i class="bi bi-house-door-fill"></i>
            <span>डैशबोर्ड</span>
        </a>
        <a href="/applications/create" class="tsp-dash-sidebar-link">
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
        <a href="#help" class="tsp-dash-sidebar-link" id="helpSidebarLink">
            <i class="bi bi-question-circle"></i>
            <span>सहायता</span>
        </a>
    </aside>

    <!-- Main Content Area -->
    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">
            
            <!-- Welcome Banner Card -->
            <div class="tsp-dash-welcome-card mb-4">
                <div class="text-start">
                    <h2 class="tsp-dash-welcome-title">नमस्ते <?= Helpers::esc(Auth::userName()) ?> 👋</h2>
                    <p class="tsp-dash-welcome-desc mb-4">
                        प्रतिभा सम्मान समारोह 2026 के लिए आवेदन करना शुरू करें। यह प्रक्रिया सरल और केवल एक फॉर्म भरने की है।
                    </p>
                    <a href="/applications/create" class="btn tsp-dash-welcome-btn shadow-sm">
                        <i class="bi bi-file-earmark-plus"></i>
                        <span>आवेदन फॉर्म भरना शुरू करें</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <img src="/assets/images/dashboard_welcome_illustration.png" alt="Welcome Illustration" class="tsp-dash-welcome-illustration d-none d-lg-block">
            </div>

            <!-- Cards Grid (How it works, Dates, Contact) -->
            <div class="tsp-dash-cards-grid">
                
                <!-- Card 1: यह कैसे काम करता है? -->
                <div class="tsp-dash-info-card">
                    <div class="tsp-dash-info-header">
                        <div class="tsp-dash-info-icon-wrapper green">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h3 class="tsp-dash-info-title">यह कैसे काम करता है?</h3>
                    </div>
                    <ul class="tsp-dash-check-list">
                        <li class="tsp-dash-check-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>आवेदन फॉर्म भरें</span>
                        </li>
                        <li class="tsp-dash-check-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>फॉर्म सबमिट करें</span>
                        </li>
                        <li class="tsp-dash-check-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>सत्यापन प्रक्रिया</span>
                        </li>
                        <li class="tsp-dash-check-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>चयन होने पर सूचना प्राप्त करें</span>
                        </li>
                    </ul>
                </div>

                <!-- Card 2: महत्वपूर्ण तिथियाँ -->
                <div class="tsp-dash-info-card">
                    <div class="tsp-dash-info-header">
                        <div class="tsp-dash-info-icon-wrapper blue">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <h3 class="tsp-dash-info-title">महत्वपूर्ण तिथियाँ</h3>
                    </div>
                    <ul class="tsp-dash-date-list text-start">
                        <li class="tsp-dash-date-item">
                            <i class="bi bi-calendar-event"></i>
                            <div>
                                <div class="tsp-dash-date-label">आवेदन प्रारंभ तिथि</div>
                                <div class="tsp-dash-date-val">01 जनवरी 2026</div>
                            </div>
                        </li>
                        <li class="tsp-dash-date-item">
                            <i class="bi bi-file-earmark-check"></i>
                            <div>
                                <div class="tsp-dash-date-label">आवेदन की अंतिम तिथि</div>
                                <div class="tsp-dash-date-val">30 जून 2026</div>
                            </div>
                        </li>
                        <li class="tsp-dash-date-item">
                            <i class="bi bi-lock"></i>
                            <div>
                                <div class="tsp-dash-date-label">प्रतिभा सम्मान समारोह</div>
                                <div class="tsp-dash-date-val">09 अगस्त 2026</div>
                            </div>
                        </li>
                        <li class="tsp-dash-date-item">
                            <i class="bi bi-geo-alt"></i>
                            <div>
                                <div class="tsp-dash-date-label">स्थान</div>
                                <div class="tsp-dash-date-val">कोटा, राजस्थान</div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Card 3: सहायता एवं संपर्क -->
                <div class="tsp-dash-info-card" id="help">
                    <div class="tsp-dash-info-header">
                        <div class="tsp-dash-info-icon-wrapper orange">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <h3 class="tsp-dash-info-title">सहायता एवं संपर्क</h3>
                    </div>
                    <div class="tsp-dash-contact-list">
                        <div class="tsp-dash-contact-item">
                            <div class="tsp-dash-contact-info">
                                <span class="tsp-dash-contact-name">श्री महेंद्र सिंह ढोंकावत</span>
                                <span class="tsp-dash-contact-phone">8432307146</span>
                            </div>
                            <a href="tel:8432307146" class="tsp-dash-call-btn" title="Call">
                                <i class="bi bi-telephone-fill"></i>
                            </a>
                        </div>
                        <div class="tsp-dash-contact-item">
                            <div class="tsp-dash-contact-info">
                                <span class="tsp-dash-contact-name">श्री विनय सिंह</span>
                                <span class="tsp-dash-contact-phone">9414336466</span>
                            </div>
                            <a href="tel:9414336466" class="tsp-dash-call-btn" title="Call">
                                <i class="bi bi-telephone-fill"></i>
                            </a>
                        </div>
                        <div class="tsp-dash-contact-item">
                            <div class="tsp-dash-contact-info">
                                <span class="tsp-dash-contact-name">श्री सुभाष धम्मनियां</span>
                                <span class="tsp-dash-contact-phone">9829771477</span>
                            </div>
                            <a href="tel:9829771477" class="tsp-dash-call-btn" title="Call">
                                <i class="bi bi-telephone-fill"></i>
                            </a>
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
    
    // Sidebar help navigation link scroll behavior
    const helpLink = document.getElementById('helpSidebarLink');
    if (helpLink) {
        helpLink.addEventListener('click', function(e) {
            const helpCard = document.getElementById('help');
            if (helpCard) {
                e.preventDefault();
                helpCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                helpCard.style.outline = '2px solid var(--maroon-dash)';
                setTimeout(() => {
                    helpCard.style.outline = 'none';
                }, 2000);
            }
        });
    }
});
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

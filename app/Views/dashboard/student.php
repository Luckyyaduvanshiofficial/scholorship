<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$role = 'student';

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <?php
    $activeLink = 'dashboard';
    require VIEW_PATH . '/layouts/student-sidebar.php';
    ?>

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
                <img src="/assets/images/dashboard_welcome_illustration.png" alt="Welcome Illustration" class="tsp-dash-welcome-illustration d-none d-lg-block" width="1024" height="1024" loading="lazy">
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

<!-- Sidebar toggle -->
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

<!-- Help scroll behavior for student sidebar -->
<script>
(function () {
    'use strict';
    var helpLink = document.getElementById('helpSidebarLink');
    if (!helpLink) return;
    helpLink.addEventListener('click', function (e) {
        var helpCard = document.getElementById('help');
        if (helpCard) {
            e.preventDefault();
            helpCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            helpCard.style.outline = '2px solid var(--maroon-dash)';
            setTimeout(function () { helpCard.style.outline = 'none'; }, 2000);
        }
    });
})();
</script>

<?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>

</body>
</html>

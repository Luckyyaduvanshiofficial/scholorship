<?php
use App\Core\Auth;
?>

<footer class="tsp-site-footer">
    <div class="container">

        <div class="row g-4 py-5">

            <!-- Brand col -->
            <div class="col-lg-4 col-md-6">
                <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                    <img src="/assets/images/logo/logo-placeholder.svg"
                         alt="Tamboli Samaj Logo"
                         width="40" height="40"
                         loading="lazy">
                    <span class="fw-bold text-white" style="font-size: 0.95rem;">तम्बोली समाज विकास संस्था</span>
                </a>
                <p class="small text-white-50 mb-3">
                    राजस्थान के तम्बोली समाज के मेधावी विद्यार्थियों को सम्मानित करने एवं छात्रवृत्ति प्रदान करने हेतु समर्पित डिजिटल पोर्टल।
                </p>
                <div class="small text-white-50">
                    <i class="bi bi-envelope-fill me-1 text-warning"></i>
                    <a href="mailto:contact@tambolisamaj.org" class="text-white-50 text-decoration-none">
                        contact@tambolisamaj.org
                    </a>
                </div>
            </div>

            <!-- Quick links -->
            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="tsp-footer-heading">पोर्टल / Portal</h6>
                <ul class="list-unstyled tsp-footer-links">
                    <li><a href="/">मुख्य पृष्ठ</a></li>
                    <li><a href="/applications/create">आवेदन करें</a></li>
                    <li><a href="/#status-tracker">स्थिति जांचें</a></li>
                    <li><a href="/#announcements">सूचनाएं</a></li>
                    <li><a href="/#help">सहायता</a></li>
                </ul>
            </div>

            <!-- Auth links -->
            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="tsp-footer-heading">खाता / Account</h6>
                <ul class="list-unstyled tsp-footer-links">
                    <?php if (Auth::guest()): ?>
                        <li><a href="/login">लॉगिन</a></li>
                        <li><a href="/register">पंजीकरण</a></li>
                    <?php else: ?>
                        <?php
                        $dashHref = Auth::isAdmin()
                            ? '/admin'
                            : (Auth::isRepresentative() ? '/representative' : '/dashboard');
                        ?>
                        <li><a href="<?= $dashHref ?>">डैशबोर्ड</a></li>
                        <li><a href="/profile">प्रोफाइल</a></li>
                        <li><a href="/applications">मेरे आवेदन</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Event info -->
            <div class="col-lg-4 col-md-6">
                <h6 class="tsp-footer-heading">समारोह 2026 / Event</h6>
                <ul class="list-unstyled tsp-footer-links">
                    <li class="d-flex gap-2 mb-2">
                        <i class="bi bi-calendar-event-fill text-warning flex-shrink-0 mt-1"></i>
                        <span>9 अगस्त 2026 / 9 August 2026</span>
                    </li>
                    <li class="d-flex gap-2 mb-2">
                        <i class="bi bi-geo-alt-fill text-warning flex-shrink-0 mt-1"></i>
                        <span>कोटा, राजस्थान / Kota, Rajasthan</span>
                    </li>
                    <li class="d-flex gap-2 mb-2">
                        <i class="bi bi-trophy-fill text-warning flex-shrink-0 mt-1"></i>
                        <span>प्रतिभा सम्मान — 75% या अधिक</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-mortarboard-fill text-warning flex-shrink-0 mt-1"></i>
                        <span>छात्रवृत्ति — स्कूल 80% / कॉलेज 70%</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom bar -->
        <div class="tsp-footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="small text-white-50">
                &copy; <?= date('Y') ?> तम्बोली समाज विकास संस्था, राजस्थान। सर्वाधिकार सुरक्षित।
            </div>
            <div class="small text-white-50">
                समारोह प्रतिवर्ष विभिन्न शहरों में आयोजित किया जाता है।
            </div>
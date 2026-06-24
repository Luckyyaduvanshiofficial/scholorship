<?php
use App\Core\Auth;
?>

<footer class="tsp-site-footer py-5 mt-5" style="background: #0f172a;">
    <div class="container">
        <div class="row g-4 justify-content-between mb-5">
            <!-- Brand description -->
            <div class="col-lg-4 col-md-6 text-start">
                <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                    <img src="/assets/images/logo/logo-placeholder.svg" alt="Logo" width="36" height="36">
                    <span class="fw-bold text-white font-heading" style="font-size: 1.5rem;">तम्बोली समाज विकास संस्था</span>
                </a>
                <p class="text-secondary small mb-3 lh-lg" style="color: #94a3b8 !important;">
                    राजस्थान के तम्बोली समाज के मेधावी विद्यार्थियों को प्रोत्साहित करने तथा उच्च शिक्षा एवं उज्ज्वल भविष्य के निर्माण हेतु समर्पित एकीकृत डिजिटल मंच।
                </p>
                <div class="small" style="color: #94a3b8 !important;">
                    <i class="bi bi-envelope-fill text-success me-2"></i>
                    <a href="mailto:contact@tambolisamaj.org" class="text-decoration-none" style="color: #94a3b8;">
                        contact@tambolisamaj.org
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 col-6 text-start">
                <h6 class="text-white fw-bold mb-3 font-heading" style="font-size: 1.3rem;">पोर्टल / Portal</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="/" class="text-decoration-none" style="color: #94a3b8;">मुख्य पृष्ठ / Home</a></li>
                    <li><a href="/applications/create" class="text-decoration-none" style="color: #94a3b8;">आवेदन / Apply Now</a></li>
                    <li><a href="/#status-tracker" class="text-decoration-none" style="color: #94a3b8;">स्थिति / Track Status</a></li>
                    <li><a href="/#announcements" class="text-decoration-none" style="color: #94a3b8;">सूचनाएं / Notices</a></li>
                </ul>
            </div>

            <!-- Auth links -->
            <div class="col-lg-2 col-md-6 col-6 text-start">
                <h6 class="text-white fw-bold mb-3 font-heading" style="font-size: 1.3rem;">खाता / Account</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <?php if (Auth::guest()): ?>
                        <li><a href="/login" class="text-decoration-none" style="color: #94a3b8;">लॉगिन / Login</a></li>
                        <li><a href="/register" class="text-decoration-none" style="color: #94a3b8;">पंजीकरण / Register</a></li>
                    <?php else: ?>
                        <?php
                        $dashHref = Auth::isAdmin()
                            ? '/admin'
                            : (Auth::isRepresentative() ? '/representative' : '/dashboard');
                        ?>
                        <li><a href="<?= $dashHref ?>" class="text-decoration-none" style="color: #94a3b8;">डैशबोर्ड / Dashboard</a></li>
                        <li><a href="/profile" class="text-decoration-none" style="color: #94a3b8;">प्रोफाइल / Profile</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Event info -->
            <div class="col-lg-3 col-md-6 text-start">
                <h6 class="text-white fw-bold mb-3 font-heading" style="font-size: 1.3rem;">वार्षिक समारोह 2026 / Event</h6>
                <ul class="list-unstyled d-flex flex-column gap-3 small" style="color: #94a3b8 !important;">
                    <li class="d-flex gap-2">
                        <i class="bi bi-calendar-event text-success mt-1"></i>
                        <span>9 अगस्त 2026 / 9 August 2026</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-geo-alt text-success mt-1"></i>
                        <span>कोटा, राजस्थान / Kota, Rajasthan</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-info-circle text-success mt-1"></i>
                        <span>प्रतिवर्ष विभिन्न शहरों में आयोजित</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr style="border-color: #334155;">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 small" style="color: #64748b !important;">
            <div>
                &copy; <?= date('Y') ?> तम्बोली समाज विकास संस्था, राजस्थान। सर्वाधिकार सुरक्षित।
            </div>
            <div class="d-flex gap-3">
                <span>Secured and Managed Digitally</span>
            </div>
        </div>
    </div>
</footer>
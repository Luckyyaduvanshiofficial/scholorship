<?php
use App\Core\Auth;
use App\Core\Helpers;

$dashHref = Auth::isAdmin()
    ? admin_dashboard_url()
    : (Auth::isRepresentative() ? '/representative' : '/dashboard');
?>

<!-- ── PREMIUM FOOTER ── -->
<footer class="tsp-premium-footer" id="help">
    <div class="container">
        <div class="tsp-footer-grid">
            <!-- Brand Column -->
            <div class="tsp-footer-col tsp-footer-brand-col">
                <div class="tsp-footer-brand">
                    <img src="<?= \App\Core\Url::asset('images/logo/logo-placeholder.svg') ?>" alt="Tamboli Samaj Logo" class="tsp-footer-logo">
                    <div>
                        <div class="tsp-footer-brand-hi">तम्बोली समाज विकास संस्था</div>
                        <div class="tsp-footer-brand-en">Tamboli Samaj Vikas Sanstha, Rajasthan</div>
                    </div>
                </div>
                <p class="tsp-footer-about">
                    प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल — समाज की प्रतिभाओं के सम्मान एवं उज्ज्वल भविष्य के लिए समर्पित।
                </p>
                <div class="tsp-footer-social">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="tsp-footer-col">
                <h2 class="tsp-footer-heading">त्वरित लिंक / Quick Links</h2>
                <ul class="tsp-footer-links">
                    <li><a href="/">मुख्य पृष्ठ / Home</a></li>
                    <li><a href="/dashboard/applications/create">आवेदन करें / Apply</a></li>
                    <li><a href="#status-tracker">स्थिति खोजें / Track Status</a></li>
                    <?php if (Auth::guest()): ?>
                        <li><a href="/login">लॉगिन / Login</a></li>
                        <li><a href="/register">पंजीकरण / Register</a></li>
                    <?php else: ?>
                        <li><a href="<?= Helpers::esc($dashHref) ?>">डैशबोर्ड / Dashboard</a></li>
                        <li><a href="/dashboard/profile">प्रोफाइल / Profile</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="tsp-footer-col">
                <h2 class="tsp-footer-heading">संपर्क / Contact</h2>
                <ul class="tsp-footer-contact">
                    <li>
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>कोटा, राजस्थान, भारत</span>
                    </li>
                    <li>
                        <i class="bi bi-telephone-fill"></i>
                        <span>0141-XXXXXXX</span>
                    </li>
                    <li>
                        <i class="bi bi-envelope-fill"></i>
                        <span>contact@tambolisamaj.org</span>
                    </li>
                    <li>
                        <i class="bi bi-globe"></i>
                        <span>www.tambolisamaj.org</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tsp-footer-bottom">
            <span>&copy; <?= date('Y') ?> तम्बोली समाज विकास संस्था। सभी अधिकार सुरक्षित।</span>
            <span class="tsp-footer-credit">Tamboli Samaj Vikas Sanstha, Rajasthan</span>
        </div>
    </div>
</footer>

<?php if (\App\Core\Auth::check()): ?>
    <?php require VIEW_PATH . '/layouts/mobile-bottom-nav.php'; ?>
<?php endif; ?>

</body>
</html>

<?php
/**
 * Shared Dashboard Footer Partial (compact maroon bar)
 *
 * Used by: admin dashboard, admin sub-pages, student dashboard, representative dashboard.
 * Replaces the public footer (tsp-site-footer-v2) and inline per-page footers.
 */
?>
<footer class="tsp-dash-footer">
    <div class="tsp-dash-footer-inner">
        <span>&copy; <?= date('Y') ?> तम्बोली समाज विकास संस्था, राजस्थान | सर्वाधिकार सुरक्षित</span>
        <div class="tsp-dash-footer-contacts">
            <span><i class="bi bi-telephone-fill me-1"></i>0141-XXXXXXX</span>
            <span class="tsp-dash-footer-sep">|</span>
            <span><i class="bi bi-envelope-fill me-1"></i>contact@tambolisamaj.org</span>
        </div>
    </div>
</footer>

<?php if (\App\Core\Auth::check()): ?>
    <?php require VIEW_PATH . '/layouts/mobile-bottom-nav.php'; ?>
<?php endif; ?>

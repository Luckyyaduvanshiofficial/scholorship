// Tamboli Samaj Portal — Custom JavaScript
// Bootstrap 5 initialization and shared utilities

document.addEventListener('DOMContentLoaded', () => {
    // Enable Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].forEach(el => new bootstrap.Tooltip(el));

    // Enable Bootstrap popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].forEach(el => new bootstrap.Popover(el));

    // Auto-dismiss flash alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        }, 5000);
    });

    // Active navbar links highlighting
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.tsp-site-header .tsp-nav-links .nav-link');
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (href !== '/' && href !== '#' && currentPath.startsWith(href))) {
            link.classList.add('active');
        }
    });

    // Premium navbar scroll state
    const premiumNavbar = document.getElementById('tspPremiumNavbar');
    if (premiumNavbar) {
        const updateNavbar = () => {
            if (window.scrollY > 10) {
                premiumNavbar.classList.add('scrolled');
            } else {
                premiumNavbar.classList.remove('scrolled');
            }
        };
        updateNavbar();
        window.addEventListener('scroll', updateNavbar, { passive: true });
    }

    // Smooth scroll for anchor links with sticky offset
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const navHeight = premiumNavbar ? premiumNavbar.offsetHeight : 0;
                const ticker = document.querySelector('.tsp-premium-ticker');
                const tickerHeight = ticker ? ticker.offsetHeight : 0;
                const offset = navHeight + tickerHeight + 16;
                const targetPosition = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
                });
            }
        });
    });

    // Pause premium ticker on hover is handled by CSS; this is a fallback
    // for any JS-driven ticker implementations in the future.
});

/**
 * Universal Form Guard — prevents double-submit on ALL forms.
 * Handles button disabling + loading spinner for every POST form on the site.
 * Add class "no-spinner" to buttons that manage their own loading state (e.g. AJAX uploads).
 */
(function() {
    const SUBMIT_REENABLE_MS = 30000; // safety timeout: re-enable after 30s

    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.tagName !== 'FORM') return;

        // Skip forms that already have a submitted flag
        if (form.dataset.formGuard === 'submitted') {
            e.preventDefault();
            return;
        }

        const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        if (submitBtns.length === 0) return;

        // Mark form as submitted to catch subsequent attempts on same event
        form.dataset.formGuard = 'submitted';

        submitBtns.forEach(function(btn) {
            // Skip AJAX-managed buttons
            if (btn.classList.contains('no-spinner')) return;

            // Prevent double-click via own property
            if (btn.dataset.guardDisabled) {
                e.preventDefault();
                return;
            }
            btn.dataset.guardDisabled = 'true';
            btn.disabled = true;

            // Save original content only once
            if (!btn.dataset.guardOriginalHtml) {
                btn.dataset.guardOriginalHtml = btn.innerHTML;
            }

            // Replace with spinner + processing text
            var loadingText = btn.dataset.loadingText || 'प्रोसेसिंग... / Processing...';
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ' + loadingText;
        });

        // Safety timeout: re-enable form after 30 seconds
        setTimeout(function() {
            delete form.dataset.formGuard;
            submitBtns.forEach(function(btn) {
                btn.disabled = false;
                delete btn.dataset.guardDisabled;
                if (btn.dataset.guardOriginalHtml) {
                    btn.innerHTML = btn.dataset.guardOriginalHtml;
                }
            });
        }, SUBMIT_REENABLE_MS);
    }, true); // use capture phase to catch events early
})();

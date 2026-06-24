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
});

<?php
/**
 * Shared Admin Sidebar Toggle Script
 *
 * Handles:
 *  - Desktop: collapse sidebar (hidden via 'collapsed' class)
 *  - Mobile:  off-canvas drawer via 'active' class + backdrop overlay
 */
?>
<script>
(function () {
    'use strict';

    const sidebar  = document.getElementById('sidebar');
    const toggle   = document.getElementById('sidebarToggle');
    const overlay  = document.getElementById('sidebarOverlay');

    if (!sidebar || !toggle) return;

    const syncToggleIcon = () => {
        const icon = toggle.querySelector('i');
        if (icon) {
            if (sidebar.classList.contains('active')) {
                icon.classList.remove('bi-list');
                icon.classList.add('bi-x');
            } else {
                icon.classList.remove('bi-x');
                icon.classList.add('bi-list');
            }
        }
    };

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        if (window.innerWidth < 992) {
            // Mobile: off-canvas drawer
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
            syncToggleIcon();
        } else {
            // Desktop: collapse sidebar
            sidebar.classList.toggle('collapsed');
        }
    });

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            syncToggleIcon();
        });
    }

    // Close button for mobile drawer
    const closeBtns = document.querySelectorAll('.tsp-sidebar-close');
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            syncToggleIcon();
        });
    });
})();
</script>

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

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        if (window.innerWidth < 992) {
            // Mobile: off-canvas drawer
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        } else {
            // Desktop: collapse sidebar
            sidebar.classList.toggle('collapsed');
        }
    });

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
})();
</script>

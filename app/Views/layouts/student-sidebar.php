<?php
/**
 * Shared Student Sidebar Layout Partial
 *
 * Variables expected from parent view:
 *   $activeLink (string) — 'dashboard' | 'apply' | 'applications' | 'profile'
 */
use App\Core\Csrf;
?>

<!-- Mobile sidebar backdrop overlay -->
<div class="tsp-sidebar-backdrop" id="sidebarOverlay"></div>

<!-- ── LEFT STUDENT NAVIGATION SIDEBAR ── -->
<aside class="tsp-dash-sidebar bg-white border-end d-flex flex-column py-4 px-3" id="sidebar">
    <!-- Close button for mobile -->
    <div class="d-flex align-items-center justify-content-between d-lg-none mb-3 pb-2 border-bottom">
        <span class="fw-bold text-dark" style="font-size: 1.45rem;">मेन्यू / Menu</span>
        <button type="button" class="btn-close tsp-sidebar-close" id="sidebarClose" aria-label="Close menu"></button>
    </div>

    <nav class="nav flex-column gap-2 flex-grow-1">
        <a href="/dashboard" class="tsp-dash-sidebar-link <?= ($activeLink === 'dashboard') ? 'active' : '' ?>">
            <i class="bi bi-house-door-fill"></i>
            <span>डैशबोर्ड</span>
        </a>
        <a href="/dashboard/applications/create" class="tsp-dash-sidebar-link <?= ($activeLink === 'apply') ? 'active' : '' ?>">
            <i class="bi bi-pencil-square"></i>
            <span>आवेदन फॉर्म भरें</span>
        </a>
        <a href="/dashboard/applications" class="tsp-dash-sidebar-link <?= ($activeLink === 'applications') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i>
            <span>मेरे आवेदन</span>
        </a>
        <a href="/dashboard/profile" class="tsp-dash-sidebar-link <?= ($activeLink === 'profile') ? 'active' : '' ?>">
            <i class="bi bi-person-fill"></i>
            <span>प्रोफाइल</span>
        </a>
        <a href="#help" class="tsp-dash-sidebar-link" id="helpSidebarLink">
            <i class="bi bi-question-circle"></i>
            <span>सहायता</span>
        </a>
    </nav>

    <!-- Sidebar footer logout -->
    <div class="mt-auto pt-3 border-top">
        <form action="/logout" method="post" class="m-0">
            <?= Csrf::field() ?>
            <button type="submit"
                    class="tsp-dash-sidebar-link w-100 border-0 bg-transparent text-danger fw-semibold px-3"
                    style="gap:1.2rem;">
                <i class="bi bi-box-arrow-right fs-4"></i>
                <span>लॉग आउट</span>
            </button>
        </form>
    </div>
</aside>

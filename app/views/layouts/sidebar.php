<?php
/**
 * Sidebar — rendered differently based on user role.
 *
 * Phase 1: Empty shell. Populated in later phases.
 *
 * Variables available:
 *   $role — 'student' | 'admin' | 'representative', or null for guest
 */
$role = $role ?? null;
?>
<?php if ($role === 'admin'): ?>
    <aside class="d-none d-lg-block bg-dark text-white" style="width: 250px; min-height: 100vh;">
        <div class="p-3">
            <h6 class="text-uppercase text-muted small fw-bold">Admin Panel</h6>
            <nav class="nav flex-column">
                <a class="nav-link text-white" href="/admin"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a class="nav-link text-white" href="/admin/students"><i class="bi bi-people me-2"></i> Students</a>
                <a class="nav-link text-white" href="/admin/applications"><i class="bi bi-file-earmark-text me-2"></i> Applications</a>
                <a class="nav-link text-white" href="/admin/representatives"><i class="bi bi-person-badge me-2"></i> Representatives</a>
                <a class="nav-link text-white" href="/admin/announcements"><i class="bi bi-megaphone me-2"></i> Announcements</a>
                <a class="nav-link text-white" href="/admin/settings"><i class="bi bi-gear me-2"></i> Settings</a>
            </nav>
        </div>
    </aside>
<?php elseif ($role === 'representative'): ?>
    <aside class="d-none d-lg-block bg-dark text-white" style="width: 250px; min-height: 100vh;">
        <div class="p-3">
            <h6 class="text-uppercase text-muted small fw-bold">Representative Panel</h6>
            <nav class="nav flex-column">
                <a class="nav-link text-white" href="/representative"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a class="nav-link text-white" href="/representative/applications"><i class="bi bi-file-earmark-text me-2"></i> Applications</a>
            </nav>
        </div>
    </aside>
<?php elseif ($role === 'student'): ?>
    <aside class="d-none d-lg-block bg-dark text-white" style="width: 250px; min-height: 100vh;">
        <div class="p-3">
            <h6 class="text-uppercase text-muted small fw-bold">Student Portal</h6>
            <nav class="nav flex-column">
                <a class="nav-link text-white" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a class="nav-link text-white" href="/profile"><i class="bi bi-person me-2"></i> My Profile</a>
                <a class="nav-link text-white" href="/academics"><i class="bi bi-book me-2"></i> Academics</a>
                <a class="nav-link text-white" href="/applications"><i class="bi bi-file-earmark-text me-2"></i> Applications</a>
                <a class="nav-link text-white" href="/announcements"><i class="bi bi-megaphone me-2"></i> Announcements</a>
            </nav>
        </div>
    </aside>
<?php endif; ?>

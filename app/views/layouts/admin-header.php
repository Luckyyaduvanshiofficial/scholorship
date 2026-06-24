<?php
/**
 * Shared Admin / Dashboard Header Partial
 *
 * Variables expected from parent view:
 *   $adminName  (string) — display name of logged-in user
 *   $adminEmail (string) — email of logged-in user
 *   $dashRole   (string) — 'admin' | 'representative' | 'student' (for dropdown links)
 *
 * Requires Bootstrap 5 JS (loaded in footer or inline) for dropdown.
 */
use App\Core\Csrf;

$dashRole   = $dashRole   ?? 'admin';
$adminName  = $adminName  ?? (\App\Core\Auth::userName() ?: 'Admin');
$adminEmail = $adminEmail ?? '';
?>

<!-- ── DASHBOARD TOP HEADER ── -->
<header class="tsp-adm-header">

    <!-- Left: Sidebar hamburger toggle -->
    <div class="tsp-adm-header-left">
        <button class="tsp-adm-toggle" id="sidebarToggle" aria-label="Toggle Navigation Sidebar">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <!-- Center: Logo + bilingual title -->
    <div class="tsp-adm-header-center">
        <img src="/assets/images/logo/logo-placeholder.svg"
             alt="Tamboli Samaj Logo"
             class="tsp-adm-logo">
        <div class="tsp-adm-title-group">
            <h1 class="tsp-adm-title-hi">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</h1>
            <span class="tsp-adm-title-en">Tamboli Samaj Vikas Sanstha, Rajasthan</span>
        </div>
    </div>

    <!-- Right: User profile dropdown -->
    <div class="tsp-adm-header-right">
        <div class="dropdown">
            <div class="tsp-adm-profile-trigger"
                 data-bs-toggle="dropdown"
                 aria-expanded="false"
                 role="button"
                 tabindex="0">
                <div class="tsp-adm-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="tsp-adm-user-info d-none d-md-flex">
                    <span class="tsp-adm-user-name"><?= htmlspecialchars($adminName) ?></span>
                    <?php if ($adminEmail): ?>
                        <span class="tsp-adm-user-email"><?= htmlspecialchars($adminEmail) ?></span>
                    <?php endif; ?>
                </div>
                <i class="bi bi-chevron-down tsp-adm-chevron"></i>
            </div>

            <ul class="dropdown-menu dropdown-menu-end tsp-adm-dropdown shadow border-0">
                <li>
                    <a class="dropdown-item tsp-adm-dropdown-item" href="/profile">
                        <i class="bi bi-person"></i>
                        <span>प्रोफाइल (Profile)</span>
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form action="/logout" method="post" class="m-0">
                        <?= Csrf::field() ?>
                        <button type="submit"
                                class="dropdown-item tsp-adm-dropdown-item tsp-adm-dropdown-item--danger border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>लॉग आउट (Logout)</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

</header>

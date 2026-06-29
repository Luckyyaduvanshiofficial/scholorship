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
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Url;

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
        <img src="<?= Url::asset('images/logo/logo-placeholder.svg') ?>"
             alt="Tamboli Samaj Logo"
             class="tsp-top-header-logo"
             style="width: 48px; height: 48px; margin-bottom: 2px; padding: 2px;">
        <div class="tsp-top-header-title-hi" style="font-size: 1.7rem; line-height: 1.15;">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</div>
        <div class="tsp-top-header-title-en" style="font-size: 1.05rem; margin-top: 0; letter-spacing: 0.05em;">TAMBOLI SAMAJ VIKAS SANSTHA, RAJASTHAN</div>
    </div>

    <!-- Right: User profile dropdown -->
    <div class="tsp-adm-header-right">
        <div class="dropdown">
            <div class="tsp-adm-profile-trigger"
                 data-bs-toggle="dropdown"
                 aria-expanded="false"
                 role="button"
                 tabindex="0"
                 aria-label="User profile menu">
                <div class="tsp-adm-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <?php 
                    $headerPhoto = Auth::profilePhoto();
                    if ($headerPhoto && !str_starts_with($headerPhoto, 'http') && APP_HOST !== 'portal' && APP_HOST !== 'site') {
                        $headerPhoto = Url::upload(ltrim($headerPhoto, '/'));
                    }
                    if ($headerPhoto): ?>
                        <img src="<?= htmlspecialchars($headerPhoto) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="bi bi-person-fill"></i>
                    <?php endif; ?>
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
                    <a class="dropdown-item tsp-adm-dropdown-item" href="<?= (APP_HOST === 'admin' && Auth::isAdmin()) ? admin_path('settings') : (APP_HOST === 'portal' || APP_HOST === 'site' ? '/dashboard/profile' : Url::portal('/dashboard/profile')) ?>">
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

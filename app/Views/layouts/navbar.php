<?php
use App\Core\Auth;
use App\Core\Helpers;

$dashHref = Auth::isAdmin()
    ? '/admin'
    : (Auth::isRepresentative() ? '/representative' : '/dashboard');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$isHome    = $uri === '/' || $uri === '/home';
$isApply   = str_starts_with($uri, '/applications/create');
$isTrack   = str_starts_with($uri, '#status-tracker') || str_starts_with($uri, '/?track');
$isLogin   = str_starts_with($uri, '/login');
$isRegister = str_starts_with($uri, '/register');
?>

<!-- ── CLASSIC CENTERED HEADER ── -->
<div class="tsp-classic-header">
    <div class="container">
        <a href="/" class="tsp-classic-brand" aria-label="Tamboli Samaj Portal Home"
           style="text-decoration:none; color:inherit;">
            <img src="/assets/images/logo/logo-placeholder.svg"
                 alt="Tamboli Samaj Vikas Sanstha Logo"
                 class="tsp-classic-logo"
                 width="88" height="88"
                 style="width:88px;height:88px;min-width:88px;min-height:88px;max-width:88px;max-height:88px;object-fit:contain;border-radius:50%;border:3px solid #c62828;box-shadow:0 2px 12px rgba(198,40,40,.18);display:block;">
            <h1 class="tsp-classic-title-hi"
                style="font-size:clamp(1.5rem,3vw,2.4rem);font-weight:700;color:#1a1a1a;margin:0;line-height:1.2;text-decoration:none;">
                प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल
            </h1>
            <p class="tsp-classic-title-en"
               style="font-size:clamp(0.8rem,1.5vw,1.1rem);font-weight:700;color:#c62828;margin:0;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;">
                TAMBOLI SAMAJ VIKAS SANSTHA, RAJASTHAN
            </p>
        </a>
    </div>
</div>

<!-- ── PILL NAVIGATION BAR ── -->
<div class="tsp-pill-navbar-wrap" id="tspPremiumNavbar">
    <div class="container">
        <nav class="tsp-pill-nav" aria-label="Primary navigation">

            <!-- Desktop Links -->
            <ul class="tsp-pill-nav-links d-none d-lg-flex" role="list">
                <li>
                    <a href="/" class="tsp-pill-link <?= $isHome ? 'active' : '' ?>" aria-current="<?= $isHome ? 'page' : 'false' ?>">
                        <i class="bi bi-house-fill" aria-hidden="true"></i>
                        <span>मुख्य पृष</span>
                    </a>
                </li>
                <li>
                    <a href="/applications/create" class="tsp-pill-link <?= $isApply ? 'active' : '' ?>" aria-current="<?= $isApply ? 'page' : 'false' ?>">
                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                        <span>आवेदन</span>
                    </a>
                </li>
                <li>
                    <a href="/#status-tracker" class="tsp-pill-link <?= $isTrack ? 'active' : '' ?>">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <span>स्थिति खोजें</span>
                    </a>
                </li>
                <?php if (Auth::guest()): ?>
                    <li>
                        <a href="/login" class="tsp-pill-link tsp-pill-login <?= $isLogin ? 'active' : '' ?>">
                            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                            <span>लॉगिन</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?= Helpers::esc($dashHref) ?>" class="tsp-pill-link">
                            <i class="bi bi-speedometer2" aria-hidden="true"></i>
                            <span>डैशबोर्ड</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Mobile Toggle -->
            <button class="tsp-pill-toggle d-lg-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#tspMobileNav"
                    aria-controls="tspMobileNav"
                    aria-label="मेनू खोलें">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
        </nav>
    </div>
</div>

<!-- ── Mobile Offcanvas Drawer ── -->
<div class="offcanvas offcanvas-end tsp-mobile-nav"
     tabindex="-1"
     id="tspMobileNav"
     aria-labelledby="tspMobileNavLabel">
    <div class="offcanvas-header">
        <div class="tsp-mobile-brand">
            <img src="/assets/images/logo/logo-placeholder.svg" alt="Logo" class="tsp-mobile-logo">
            <div>
                <div class="tsp-mobile-title-hi">प्रतिभा सम्मान पोर्टल</div>
                <div class="tsp-mobile-title-en">Tamboli Samaj</div>
            </div>
        </div>
        <button type="button" class="tsp-mobile-close" data-bs-dismiss="offcanvas" aria-label="मेनू बंद करें">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <ul class="tsp-mobile-nav-list">
            <li>
                <a href="/" class="<?= $isHome ? 'active' : '' ?>" data-bs-dismiss="offcanvas">
                    <i class="bi bi-house-fill"></i>
                    <span>मुख्य पृष्ठ / Home</span>
                </a>
            </li>
            <li>
                <a href="/applications/create" class="<?= $isApply ? 'active' : '' ?>" data-bs-dismiss="offcanvas">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>आवेदन करें / Apply</span>
                </a>
            </li>
            <li>
                <a href="/#status-tracker" data-bs-dismiss="offcanvas">
                    <i class="bi bi-search"></i>
                    <span>स्थिति खोजें / Track</span>
                </a>
            </li>
            <?php if (Auth::guest()): ?>
                <li>
                    <a href="/login" class="<?= $isLogin ? 'active' : '' ?>" data-bs-dismiss="offcanvas">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>लॉगिन / Login</span>
                    </a>
                </li>
                <li>
                    <a href="/register" class="<?= $isRegister ? 'active' : '' ?>" data-bs-dismiss="offcanvas">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>पंजीकरण / Register</span>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?= Helpers::esc($dashHref) ?>" data-bs-dismiss="offcanvas">
                        <i class="bi bi-speedometer2"></i>
                        <span>डैशबोर्ड / Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/profile" data-bs-dismiss="offcanvas">
                        <i class="bi bi-person-fill"></i>
                        <span>प्रोफाइल / Profile</span>
                    </a>
                </li>
                <li>
                    <a href="/applications" data-bs-dismiss="offcanvas">
                        <i class="bi bi-list-ul"></i>
                        <span>मेरे आवेदन / My Applications</span>
                    </a>
                </li>
                <li>
                    <form action="/logout" method="post" class="m-0">
                        <?= Csrf::field() ?>
                        <button type="submit" data-bs-dismiss="offcanvas" class="text-danger border-0 bg-transparent w-100 text-start d-flex align-items-center gap-2 py-1">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>लॉगआउट / Logout</span>
                        </button>
                    </form>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="offcanvas-footer">
        <a href="/applications/create" class="tsp-mobile-cta-btn" data-bs-dismiss="offcanvas">
            <i class="bi bi-pencil-square"></i>
            <span>आवेदन करें / Apply Now</span>
        </a>
    </div>
</div>

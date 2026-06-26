<?php
use App\Core\Auth;
use App\Core\Helpers;

$dashHref = Auth::isAdmin()
    ? '/admin'
    : (Auth::isRepresentative() ? '/representative' : '/dashboard');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$isHome = $uri === '/' || $uri === '/home';
$isApply = str_starts_with($uri, '/applications/create');
$isLogin = str_starts_with($uri, '/login');
$isRegister = str_starts_with($uri, '/register');
?>

<!-- ── PREMIUM GLASSMORPHIC NAVBAR ── -->
<header class="tsp-premium-navbar" id="tspPremiumNavbar">
    <div class="container">
        <nav class="tsp-navbar-inner" aria-label="Primary navigation">
            <!-- Brand -->
            <a href="/" class="tsp-navbar-brand">
                <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj Logo" class="tsp-navbar-logo">
                <div class="tsp-navbar-titles">
                    <span class="tsp-navbar-title-hi">प्रतिभा सम्मान पोर्टल</span>
                    <span class="tsp-navbar-title-en">Tamboli Samaj Vikas Sanstha</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <ul class="tsp-navbar-links d-none d-lg-flex">
                <li>
                    <a href="/" class="<?= $isHome ? 'active' : '' ?>">
                        <i class="bi bi-house-door-fill"></i>
                        <span>मुख्य पृष्ठ</span>
                    </a>
                </li>
                <li>
                    <a href="/applications/create" class="<?= $isApply ? 'active' : '' ?>">
                        <i class="bi bi-file-earmark-plus-fill"></i>
                        <span>आवेदन</span>
                    </a>
                </li>
                <li>
                    <a href="#status-tracker">
                        <i class="bi bi-search"></i>
                        <span>स्थिति खोजें</span>
                    </a>
                </li>
            </ul>

            <!-- Desktop CTA -->
            <div class="tsp-navbar-cta d-none d-lg-flex">
                <?php if (Auth::guest()): ?>
                    <a href="/login" class="tsp-navbar-link-btn <?= $isLogin ? 'active' : '' ?>">लॉगिन</a>
                    <a href="/register" class="tsp-navbar-btn-primary">पंजीकरण</a>
                <?php else: ?>
                    <a href="<?= Helpers::esc($dashHref) ?>" class="tsp-navbar-btn-primary">
                        <i class="bi bi-speedometer2"></i>
                        <span>डैशबोर्ड</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Toggle -->
            <button class="tsp-navbar-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#tspMobileNav" aria-controls="tspMobileNav" aria-label="Open menu">
                <i class="bi bi-list"></i>
            </button>
        </nav>
    </div>
</header>

<!-- Mobile Offcanvas Drawer -->
<div class="offcanvas offcanvas-end tsp-mobile-nav" tabindex="-1" id="tspMobileNav" aria-labelledby="tspMobileNavLabel">
    <div class="offcanvas-header">
        <div class="tsp-mobile-brand">
            <img src="/assets/images/logo/logo-placeholder.svg" alt="Logo" class="tsp-mobile-logo">
            <div>
                <div class="tsp-mobile-title-hi">प्रतिभा सम्मान पोर्टल</div>
                <div class="tsp-mobile-title-en">Tamboli Samaj</div>
            </div>
        </div>
        <button type="button" class="tsp-mobile-close" data-bs-dismiss="offcanvas" aria-label="Close menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <ul class="tsp-mobile-nav-list">
            <li>
                <a href="/" class="<?= $isHome ? 'active' : '' ?>" data-bs-dismiss="offcanvas">
                    <i class="bi bi-house-door-fill"></i>
                    <span>मुख्य पृष्ठ / Home</span>
                </a>
            </li>
            <li>
                <a href="/applications/create" class="<?= $isApply ? 'active' : '' ?>" data-bs-dismiss="offcanvas">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                    <span>आवेदन करें / Apply</span>
                </a>
            </li>
            <li>
                <a href="#status-tracker" data-bs-dismiss="offcanvas">
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
                    <a href="/logout" data-bs-dismiss="offcanvas" class="text-danger">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>लॉगआउट / Logout</span>
                    </a>
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

<?php
use App\Core\Auth;
use App\Core\Csrf;

$dashHref = Auth::isAdmin()
    ? admin_dashboard_url()
    : (Auth::isRepresentative() ? '/representative' : '/dashboard');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
?>

<!-- ── PART 1: WHITE TOP HEADER — centered logo + title ── -->
<header class="tsp-top-header">
    <div class="container position-relative">
        <!-- Centered logo + titles -->
        <div class="text-center">
            <img src="<?= \App\Core\Url::asset('images/logo/logo-placeholder.svg') ?>"
                 alt="Tamboli Samaj Logo"
                 class="tsp-top-header-logo">
            <div class="tsp-top-header-title-hi">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</div>
            <div class="tsp-top-header-title-en">TAMBOLI SAMAJ VIKAS SANSTHA, RAJASTHAN</div>
        </div>
    </div>
</header>

<!-- ── PART 2: RED PILL NAVBAR — 4 links with icons + login button ── -->
<nav class="tsp-pill-nav" aria-label="Primary navigation">
    <div class="container">
        <ul class="nav-pill-list">
            <li class="nav-pill-item">
                <a href="/" class="<?= ($uri === '/') ? 'active' : '' ?>">
                    <i class="bi bi-house-door-fill"></i><span>मुख्य पृष्ठ</span>
                </a>
            </li>
            <li class="nav-pill-item">
                <a href="/dashboard/applications/create" class="<?= str_starts_with($uri, '/dashboard/applications/create') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-plus-fill"></i><span>आवेदन</span>
                </a>
            </li>
            <li class="nav-pill-item">
                <a href="#status-tracker">
                    <i class="bi bi-search"></i><span>स्थिति खोजें</span>
                </a>
            </li>
            <?php if (Auth::guest()): ?>
                <li class="nav-pill-item tsp-auth-pill">
                    <a href="/login">
                        <i class="bi bi-box-arrow-in-right"></i><span>लॉगिन</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-pill-item tsp-auth-pill">
                    <a href="<?= $dashHref ?>">
                        <i class="bi bi-speedometer2"></i><span>डैशबोर्ड</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

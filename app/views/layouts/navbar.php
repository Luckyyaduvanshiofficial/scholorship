<?php
use App\Core\Auth;
use App\Core\Csrf;

$dashHref = Auth::isAdmin()
    ? '/admin'
    : (Auth::isRepresentative() ? '/representative' : '/dashboard');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
?>

<!-- ── PART 1: WHITE TOP HEADER — centered logo + title ── -->
<header class="tsp-top-header">
    <div class="container position-relative">
        <!-- Centered logo + titles -->
        <div class="text-center">
            <img src="/assets/images/logo/logo-placeholder.svg"
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
                <a href="/applications/create" class="<?= str_starts_with($uri, '/applications/create') ? 'active' : '' ?>">
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

<!-- ── PART 3: CREAM MARQUEE NOTICE STRIP ── -->
<section class="tsp-marquee-cream" aria-label="Important notices">
    <div class="container">
        <div class="tsp-marquee-inner">
            <div class="tsp-marquee-text">
                <marquee behavior="scroll" direction="left" scrollamount="4"
                         onmouseover="this.stop()" onmouseout="this.start()">
                    <i class="bi bi-megaphone-fill me-2" aria-hidden="true"></i>
                    🏆 प्रतिभा सम्मान समारोह 2026 - 9 अगस्त, 2026 को कोटा में आयोजित होगा &nbsp;/&nbsp; Pratibha Samman Samaroh 2026 will be held on 9 August 2026 in Kota.
                    &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                    छात्रवृत्ति के लिए मार्कशीट एवं बैंक पासबुक अपलोड अनिवार्य है &nbsp;/&nbsp; Marksheet and Bank Passbook upload is mandatory for Scholarship.
                    &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                    आवेदन जमा करने के बाद स्थिति मुख्य पृष्ठ पर ट्रैक करें &nbsp;/&nbsp; Track your application status on the homepage after submission.
                    &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                    विवाद होने पर डैशबोर्ड से दस्तावेज़ पुनः अपलोड करें &nbsp;/&nbsp; For disputed applications, re-upload documents via student dashboard.
                </marquee>
            </div>
        </div>
    </div>
</section>

<?php
use App\Core\Auth;
use App\Core\Csrf;
?>

<header class="tsp-site-header">
    <div class="container">

        <!-- Masthead -->
        <section class="tsp-masthead d-flex justify-content-center" aria-label="Portal masthead">
            <a href="/" class="tsp-brand d-inline-flex align-items-center text-decoration-none" aria-label="Tamboli Samaj Portal home">
                <img src="/assets/images/logo/logo-placeholder.svg"
                     alt="Tamboli Samaj Logo"
                     class="tsp-brand-logo"
                     width="76" height="76"
                     loading="eager">
                <span class="tsp-brand-copy">
                    <span class="tsp-brand-hi">तम्बोली समाज विकास संस्था, राजस्थान</span>
                    <span class="tsp-brand-en">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</span>
                </span>
            </a>
        </section>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg tsp-nav" id="mainNavbar" aria-label="Primary navigation">
            <button class="navbar-toggler tsp-navbar-toggler ms-auto" type="button"
                    data-bs-toggle="collapse" data-bs-target="#tspNavCollapse"
                    aria-expanded="false" aria-controls="tspNavCollapse"
                    aria-label="Open menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="tspNavCollapse">
                <ul class="navbar-nav tsp-nav-links mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/') ? 'active' : '' ?>" href="/">
                            <i class="bi bi-house-door-fill" aria-hidden="true"></i>
                            <span>मुख्य पृष्ठ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/applications/create') ? 'active' : '' ?>" href="/applications/create">
                            <i class="bi bi-file-earmark-plus-fill" aria-hidden="true"></i>
                            <span>आवेदन करें</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/applications') ? 'active' : '' ?>" href="/applications">
                            <i class="bi bi-list-ul" aria-hidden="true"></i>
                            <span>मेरे आवेदन</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#announcements">
                            <i class="bi bi-megaphone-fill" aria-hidden="true"></i>
                            <span>सूचनाएं</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#help">
                            <i class="bi bi-question-circle-fill" aria-hidden="true"></i>
                            <span>सहायता</span>
                        </a>
                    </li>
                </ul>

                <div class="tsp-nav-auth d-flex flex-wrap gap-2">
                    <?php if (Auth::guest()): ?>
                        <a href="/login" class="btn tsp-nav-btn">
                            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                            <span>लॉगिन</span>
                        </a>
                        <a href="/register" class="btn tsp-nav-btn-outline">
                            <i class="bi bi-person-plus-fill" aria-hidden="true"></i>
                            <span>पंजीकरण</span>
                        </a>
                    <?php else: ?>
                        <?php
                        $dashHref = Auth::isAdmin()
                            ? '/admin'
                            : (Auth::isRepresentative() ? '/representative' : '/dashboard');
                        ?>
                        <a href="<?= $dashHref ?>" class="btn tsp-nav-btn">
                            <i class="bi bi-speedometer2" aria-hidden="true"></i>
                            <span>डैशबोर्ड</span>
                        </a>
                        <form action="/logout" method="post" class="tsp-nav-logout m-0">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn">
                                <i class="bi bi-box-arrow-left" aria-hidden="true"></i>
                                <span>लॉगआउट</span>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

    </div>
</header>

<!-- Notice Strip -->
<section class="tsp-notice-strip" aria-label="Important notices">
    <div class="container">
        <div class="alert tsp-notice-inner mb-0" role="status">
            <div class="tsp-notice-marquee">
                <marquee behavior="scroll" direction="left" scrollamount="4"
                         onmouseover="this.stop()" onmouseout="this.start()">
                    <i class="bi bi-megaphone-fill me-2" aria-hidden="true"></i>
                    प्रतिभा सम्मान 2026 हेतु ऑनलाइन आवेदन प्रारम्भ है &nbsp;/&nbsp; Online applications for Pratibha Samman 2026 are open.
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
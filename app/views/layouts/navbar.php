<?php
use App\Core\Auth;
use App\Core\Csrf;
?>

<header class="tsp-navbar-wrapper">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light tsp-navbar-main px-0">
            <a href="/" class="navbar-brand d-flex align-items-center gap-2">
                <img src="/assets/images/logo/logo-placeholder.svg"
                     alt="Tamboli Samaj Logo"
                     width="42" height="42"
                     loading="eager">
                <span class="d-flex flex-column text-start" style="line-height: 1.2;">
                    <span class="fw-bold text-dark font-heading" style="font-size: 1.6rem; letter-spacing: -0.01em;">तम्बोली समाज विकास संस्था</span>
                    <span class="text-muted" style="font-size: 1.05rem; font-weight: 500;">राजस्थान · छात्रवृत्ति एवं प्रतिभा सम्मान</span>
                </span>
            </a>

            <button class="navbar-toggler border-0 shadow-none px-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#tspNavCollapse"
                    aria-expanded="false" aria-controls="tspNavCollapse"
                    aria-label="Open menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="tspNavCollapse">
                <ul class="navbar-nav mx-auto gap-1 gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/') ? 'active' : '' ?>" href="/">
                            <span>मुख्य पृष्ठ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/applications/create') ? 'active' : '' ?>" href="/applications/create">
                            <span>आवेदन करें</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/applications') ? 'active' : '' ?>" href="/applications">
                            <span>मेरे आवेदन</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#announcements">
                            <span>सूचनाएं</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#help">
                            <span>सहायता</span>
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    <?php if (Auth::guest()): ?>
                        <a href="/login" class="btn btn-link text-decoration-none text-dark fw-semibold px-3" style="font-size: 1.35rem;">
                            लॉगिन / Sign In
                        </a>
                        <a href="/applications/create" class="btn tsp-navbar-cta fw-bold px-4 py-2 rounded-pill" style="font-size: 1.3rem; border-radius: 20px !important;">
                            आवेदन / Apply Now
                        </a>
                    <?php else: ?>
                        <?php
                        $dashHref = Auth::isAdmin()
                            ? '/admin'
                            : (Auth::isRepresentative() ? '/representative' : '/dashboard');
                        ?>
                        <a href="<?= $dashHref ?>" class="btn btn-outline-dark fw-bold px-4 py-2 rounded-pill" style="font-size: 1.3rem; border-radius: 20px !important;">
                            डैशबोर्ड / Dashboard
                        </a>
                        <form action="/logout" method="post" class="m-0">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn btn-link text-decoration-none text-muted fw-semibold px-3" style="font-size: 1.35rem;">
                                लॉगआउट
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
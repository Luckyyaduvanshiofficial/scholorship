<?php
use App\Core\Auth;
use App\Core\Csrf;
?>

<header class="tsp-site-header">
    <div class="container">
        <section class="tsp-masthead d-flex justify-content-center" aria-label="Portal masthead">
            <a href="/" class="tsp-brand d-inline-flex align-items-center text-decoration-none" aria-label="Tamboli Samaj Portal home">
                <img src="/assets/images/logo/logo-placeholder.svg"
                     alt="Tamboli Samaj Logo"
                     class="tsp-brand-logo"
                     width="76" height="76"
                     loading="eager">
                <span class="tsp-brand-copy">
                    <span class="tsp-brand-hi">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</span>
                    <span class="tsp-brand-en">Tamboli Samaj Vikas Sanstha, Rajasthan</span>
                </span>
            </a>
        </section>

        <nav class="navbar navbar-expand-lg tsp-nav" id="mainNavbar" aria-label="Primary navigation">
            <button class="navbar-toggler tsp-navbar-toggler ms-auto" type="button"
                    data-bs-toggle="collapse" data-bs-target="#tspNavCollapse"
                    aria-expanded="false" aria-controls="tspNavCollapse" aria-label="Open menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="tspNavCollapse">
                <ul class="navbar-nav tsp-nav-links mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/"><i class="bi bi-house-door-fill" aria-hidden="true"></i><span>मुख्य पृष्ठ</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/applications/create"><i class="bi bi-file-earmark-plus-fill" aria-hidden="true"></i><span>आवेदन</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/applications"><i class="bi bi-search" aria-hidden="true"></i><span>स्थिति</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/#announcements"><i class="bi bi-megaphone-fill" aria-hidden="true"></i><span>घोषणाएं</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/#help"><i class="bi bi-question-circle-fill" aria-hidden="true"></i><span>सहायता</span></a></li>
                </ul>

                <div class="tsp-nav-auth d-flex flex-wrap gap-2">
                    <?php if (Auth::guest()): ?>
                        <a href="/login" class="btn tsp-nav-btn"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i><span>Login</span></a>
                        <a href="/register" class="btn tsp-nav-btn-outline"><i class="bi bi-person-plus-fill" aria-hidden="true"></i><span>Register</span></a>
                    <?php else: ?>
                        <a href="/dashboard" class="btn tsp-nav-btn"><i class="bi bi-speedometer2" aria-hidden="true"></i><span>Dashboard</span></a>
                        <form action="/logout" method="post" class="tsp-nav-logout m-0">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn"><i class="bi bi-box-arrow-left" aria-hidden="true"></i><span>Logout</span></button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
</header>

<section class="tsp-notice-strip" aria-label="Important notices">
    <div class="container">
        <div class="alert tsp-notice-inner mb-0" role="status">
            <h2 class="alert-heading"><i class="bi bi-info-circle" aria-hidden="true"></i> विद्यार्थियों हेतु सूचना</h2>
            <div class="tsp-notice-marquee">
                <marquee behavior="scroll" direction="left" scrollamount="4" onmouseover="this.stop()" onmouseout="this.start()">
                    प्रतिभा सम्मान 2026 हेतु ऑनलाइन पंजीकरण प्रारम्भ है |
                    छात्रवृत्ति आवेदन के लिए मार्कशीट एवं बैंक पासबुक अपलोड अनिवार्य है |
                    आवेदन जमा करने के बाद विद्यार्थी अपनी स्थिति पोर्टल पर देख सकते हैं |
                    विवाद/कमी होने पर संदेश विद्यार्थी डैशबोर्ड में दिखाई देगा |
                </marquee>
            </div>
        </div>
    </div>
</section>

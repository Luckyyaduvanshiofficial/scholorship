<?php
use App\Core\Auth;
use App\Core\Helpers;

$announcements = $announcements ?? [];

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<main>
    <section class="tsp-hero tsp-portal-hero">
        <div class="container">
            <div class="tsp-hero-kicker">Scholarship and Pratibha Samman 2026</div>
            <h1>Tamboli Samaj Online Application Portal</h1>
            <p>
                Apply online, upload documents, receive your reference number, and track the status of your
                scholarship or Pratibha Samman registration from one secure portal.
            </p>
            <div class="tsp-hero-btns">
                <?php if (Auth::guest()): ?>
                    <a href="/register" class="btn btn-hero-solid"><i class="bi bi-person-plus me-1"></i> Start Application</a>
                    <a href="/login" class="btn btn-hero-outline"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                <?php else: ?>
                    <a href="/dashboard" class="btn btn-hero-solid"><i class="bi bi-speedometer2 me-1"></i> Go to Dashboard</a>
                    <a href="/applications/create" class="btn btn-hero-outline"><i class="bi bi-file-earmark-plus me-1"></i> New Application</a>
                <?php endif; ?>
                <a href="#process" class="btn btn-hero-outline"><i class="bi bi-list-check me-1"></i> Process</a>
            </div>
        </div>
    </section>

    <section class="tsp-sec" id="services">
        <div class="container">
            <div class="tsp-sec-head">
                <span class="tsp-sec-head-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                <h2>Online Services</h2>
            </div>
            <div class="row g-3">
                <?php
                $services = [
                    ['bi-mortarboard-fill', 'Scholarship Application', 'Submit academic, bank, income, marksheet, and passbook details online.', '/applications/scholarship'],
                    ['bi-trophy-fill', 'Pratibha Samman', 'Register academic achievements and upload marksheet or award proof.', '/applications/pratibha'],
                    ['bi-file-check-fill', 'Application Tracking', 'View Submitted, Approved, Rejected, or Disputed status with admin message.', '/applications'],
                    ['bi-megaphone-fill', 'Announcements', 'Follow important dates, event notices, and portal updates in one place.', '#announcements'],
                ];
                foreach ($services as [$icon, $title, $body, $url]): ?>
                    <div class="col-sm-6 col-lg-3">
                        <a href="<?= Helpers::esc($url) ?>" class="text-decoration-none">
                            <div class="card tsp-card h-100 border-0">
                                <div class="card-body p-3 p-lg-4">
                                    <div class="tsp-card-icon mb-3"><i class="bi <?= Helpers::esc($icon) ?>"></i></div>
                                    <h5><?= Helpers::esc($title) ?></h5>
                                    <p><?= Helpers::esc($body) ?></p>
                                    <span class="tsp-card-link">Open <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tsp-sec tsp-sec-alt" id="process">
        <div class="container">
            <div class="tsp-sec-head">
                <span class="tsp-sec-head-icon"><i class="bi bi-diagram-3-fill"></i></span>
                <h2>Application Process</h2>
            </div>
            <div class="row g-3">
                <?php
                $steps = [
                    ['Register', 'Create a student account with mobile number, email, and password.'],
                    ['Complete Profile', 'Add personal details, address, education, and family information once.'],
                    ['Apply Online', 'Choose Scholarship, Pratibha Samman, or both for the active session.'],
                    ['Upload Documents', 'Attach JPG, PNG, or PDF copies of marksheet, passbook, and certificate.'],
                    ['Review', 'Representative and admin teams verify submitted details.'],
                    ['Track Result', 'Check Approved, Rejected, or Disputed status from the student dashboard.'],
                ];
                foreach ($steps as $index => [$title, $body]): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="tsp-guide h-100 bg-white p-3">
                            <div class="d-flex gap-2 align-items-start">
                                <span class="tsp-guide-num"><?= $index + 1 ?></span>
                                <div>
                                    <h6><?= Helpers::esc($title) ?></h6>
                                    <p class="mb-0"><?= Helpers::esc($body) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tsp-sec" id="announcements">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="tsp-sec-head">
                        <span class="tsp-sec-head-icon"><i class="bi bi-megaphone-fill"></i></span>
                        <h2>Announcement Board</h2>
                    </div>
                    <?php if (empty($announcements)): ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-2">Applications are planned for the 2026 session</h5>
                                <p class="text-muted mb-0">
                                    Latest official notices will appear here after admin announcement management is enabled.
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="vstack gap-3">
                            <?php foreach ($announcements as $notice): ?>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <h5 class="fw-bold mb-1"><?= Helpers::esc($notice['title'] ?? '') ?></h5>
                                        <p class="text-muted mb-0"><?= Helpers::esc(strip_tags((string) ($notice['content'] ?? ''))) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-5">
                    <div class="tsp-sec-head">
                        <span class="tsp-sec-head-icon"><i class="bi bi-calendar-event-fill"></i></span>
                        <h2>Important Dates</h2>
                    </div>
                    <div class="row g-3">
                        <?php
                        $dates = [
                            ['15', 'Jan 2026', 'Applications Open'],
                            ['28', 'Feb 2026', 'Last Date to Apply'],
                            ['09', 'Aug 2026', 'Pratibha Samman, Kota'],
                            ['09', 'Aug 2026', 'Scholarship Distribution'],
                        ];
                        foreach ($dates as [$day, $month, $label]): ?>
                            <div class="col-6">
                                <div class="tsp-date h-100">
                                    <div class="tsp-date-day"><?= Helpers::esc($day) ?></div>
                                    <div class="tsp-date-mon"><?= Helpers::esc($month) ?></div>
                                    <hr class="tsp-date-div">
                                    <div class="tsp-date-lab"><?= Helpers::esc($label) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="tsp-sec tsp-sec-alt" id="stats">
        <div class="container">
            <div class="row g-3">
                <?php
                $stats = [
                    ['100%', 'Online application goal'],
                    ['0', 'Manual data entry target'],
                    ['80%', 'Paperwork reduction target'],
                    ['2026', 'Current portal session'],
                ];
                foreach ($stats as [$value, $label]): ?>
                    <div class="col-6 col-lg-3">
                        <div class="tsp-stat">
                            <div class="tsp-stat-value"><?= Helpers::esc($value) ?></div>
                            <div class="tsp-stat-label"><?= Helpers::esc($label) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tsp-sec" id="contact">
        <div class="container">
            <div class="tsp-contact-band">
                <div>
                    <h2>Need help with an application?</h2>
                    <p>Contact the local Tamboli Samaj representative or portal administrator for correction and dispute support.</p>
                </div>
                <div class="tsp-contact-actions">
                    <a href="/register" class="tsp-btn"><i class="bi bi-person-plus me-1"></i> Register</a>
                    <a href="/login" class="tsp-btn-outline"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="tsp-ft">
    <div class="container">
        <div class="row">
            <div class="col-md-5 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj" class="tsp-ft-logo">
                    <div>
                        <h6 class="mb-0">Tamboli Samaj Vikas Sanstha</h6>
                        <small class="opacity-75">Rajasthan Community Portal</small>
                    </div>
                </div>
                <p class="opacity-75 mb-0">A yearly digital portal for scholarship, Pratibha Samman, announcements, and future community services.</p>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <h6>Quick Links</h6>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#process">Process</a></li>
                    <li><a href="/register">Register</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Contact</h6>
                <ul>
                    <li><i class="bi bi-envelope me-1"></i> contact@tambolisamaj.org</li>
                    <li><i class="bi bi-geo-alt me-1"></i> Rajasthan, India</li>
                    <li><i class="bi bi-shield-check me-1"></i> PHP Sessions and local upload storage</li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="text-center opacity-75">
            &copy; <?= date('Y') ?> Tamboli Samaj Vikas Sanstha. All rights reserved.
        </div>
    </div>
</footer>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

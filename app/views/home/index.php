<?php
use App\Core\Auth;
use App\Core\Helpers;

$announcements = $announcements ?? [];
$trackResult = $trackResult ?? null;
$trackError = $trackError ?? null;
$trackRef = $trackRef ?? '';

$quickLinks = [];
if (Auth::guest()) {
    $quickLinks = [
        ['label' => 'लॉगिन / Login', 'icon' => 'bi-box-arrow-in-right', 'href' => '/login'],
        ['label' => 'पंजीकरण / Register', 'icon' => 'bi-person-plus-fill', 'href' => '/register'],
        ['label' => 'आवेदन / Apply Online', 'icon' => 'bi-file-earmark-plus-fill', 'href' => '/applications/create'],
        ['label' => 'स्थिति / Track Status', 'icon' => 'bi-search', 'href' => '#status-tracker'],
    ];
} else {
    $quickLinks = [
        ['label' => 'डैशबोर्ड / Dashboard', 'icon' => 'bi-speedometer2', 'href' => Auth::isAdmin() ? '/admin' : (Auth::isRepresentative() ? '/representative' : '/dashboard')],
        ['label' => 'नया आवेदन / New Apply', 'icon' => 'bi-file-earmark-plus-fill', 'href' => '/applications/create'],
        ['label' => 'आवेदन सूची / My Applies', 'icon' => 'bi-list-ul', 'href' => '/applications'],
        ['label' => 'प्रोफाइल / Profile', 'icon' => 'bi-person-fill', 'href' => '/profile'],
    ];
}

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<main class="tsp-portal-main py-4 py-lg-5">
    <div class="container">

        <!-- Hero Banner -->
        <section class="tsp-hero-card mb-5">
            <div class="row g-4 align-items-center">

                <div class="col-lg-8 tsp-hero-copy">
                    <div class="tsp-hero-kicker-badge mb-3">
                        <i class="bi bi-star-fill text-warning me-1"></i>
                        वार्षिक प्रतिभा सम्मान एवं छात्रवृत्ति &nbsp;/&nbsp; Annual Pratibha Samman &amp; Scholarship
                    </div>

                    <h1 class="display-5 fw-black text-white mb-2">
                        तम्बोली समाज विकास संस्था
                    </h1>
                    <p class="text-white-50 mb-4" style="font-size: 1.05rem;">
                        तम्बोली समाज के मेधावी छात्र-छात्राओं को सम्मानित करने एवं छात्रवृत्ति प्रदान करने हेतु एकीकृत डिजिटल मंच।
                    </p>

                    <!-- Eligibility grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-2 text-white">
                                <i class="bi bi-trophy-fill text-warning fs-5 mt-1 flex-shrink-0"></i>
                                <div>
                                    <div class="fw-semibold mb-0" style="font-size: 0.92rem;">प्रतिभा सम्मान पात्रता</div>
                                    <div class="text-white-50" style="font-size: 0.82rem;">10वीं, 12वीं, स्नातक एवं स्नातकोत्तर — 75% या अधिक</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-2 text-white">
                                <i class="bi bi-mortarboard-fill text-info fs-5 mt-1 flex-shrink-0"></i>
                                <div>
                                    <div class="fw-semibold mb-0" style="font-size: 0.92rem;">छात्रवृत्ति पात्रता</div>
                                    <div class="text-white-50" style="font-size: 0.82rem;">स्कूल — 80% या अधिक &nbsp;|&nbsp; कॉलेज — 70% या अधिक</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA buttons -->
                    <div class="d-flex flex-wrap gap-3">
                        <?php if (Auth::guest()): ?>
                            <a href="/applications/create" class="btn btn-warning tsp-cta-btn fw-bold">
                                <i class="bi bi-file-earmark-plus-fill me-1"></i> आवेदन करें / Apply Now
                            </a>
                            <a href="#status-tracker" class="btn btn-outline-light tsp-cta-btn">
                                <i class="bi bi-search me-1"></i> स्थिति जांचें / Track Status
                            </a>
                        <?php else: ?>
                            <a href="<?= Auth::isAdmin() ? '/admin' : (Auth::isRepresentative() ? '/representative' : '/dashboard') ?>" class="btn btn-warning tsp-cta-btn fw-bold">
                                <i class="bi bi-speedometer2 me-1"></i> डैशबोर्ड / Dashboard
                            </a>
                            <a href="/applications/create" class="btn btn-outline-light tsp-cta-btn">
                                <i class="bi bi-file-earmark-plus-fill me-1"></i> नया आवेदन / New Application
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Event info box -->
                <div class="col-lg-4 tsp-hero-visual-col text-center">
                    <div class="tsp-hero-showcase-box">
                        <div class="tsp-showcase-badge">समारोह 2026 / Function 2026</div>
                        <div class="tsp-showcase-location">कोटा (Rajasthan)</div>
                        <div class="tsp-showcase-date">9 अगस्त 2026 / 9 Aug 2026</div>
                        <hr class="my-3 border-secondary">
                        <div class="d-flex justify-content-center gap-4 mb-2">
                            <div class="text-center text-white">
                                <div class="fw-bold fs-5">75%+</div>
                                <div class="text-white-50" style="font-size: 0.75rem;">Pratibha Samman</div>
                            </div>
                            <div class="text-center text-white">
                                <div class="fw-bold fs-5">80%+</div>
                                <div class="text-white-50" style="font-size: 0.75rem;">Scholarship</div>
                            </div>
                        </div>
                        <div class="small text-white-50 mt-2">
                            <i class="bi bi-geo-alt-fill text-warning me-1"></i>
                            प्रतिवर्ष विभिन्न शहरों में आयोजित
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Main Content -->
        <div class="row g-4 align-items-start">

            <!-- Left: Notice Board + Status Tracker -->
            <div class="col-lg-7">

                <!-- Notice Board -->
                <section class="card tsp-portal-card mb-4" id="announcements">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-megaphone-fill fs-5 text-danger" aria-hidden="true"></i>
                            <h2 class="h5 fw-bold mb-0">सूचना बोर्ड / Notice Board</h2>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-3">
                        <?php if (empty($announcements)): ?>
                            <ul class="list-group list-group-flush tsp-announcement-list">
                                <li class="list-group-item px-0">
                                    <a href="/applications/pratibha" class="tsp-announcement-link">
                                        <span class="tsp-announcement-dot"></span>
                                        <span>प्रतिभा सम्मान 2026 का ऑनलाइन फॉर्म उपलब्ध है। / Pratibha Samman 2026 online form is now open.</span>
                                        <span class="badge rounded-pill text-bg-danger ms-auto flex-shrink-0">NEW</span>
                                    </a>
                                </li>
                                <li class="list-group-item px-0">
                                    <a href="/applications/scholarship" class="tsp-announcement-link">
                                        <span class="tsp-announcement-dot"></span>
                                        <span>छात्रवृत्ति आवेदन हेतु सभी दस्तावेज़ अपलोड करना अनिवार्य है। / Document upload is mandatory for Scholarship.</span>
                                        <span class="badge rounded-pill text-bg-danger ms-auto flex-shrink-0">NEW</span>
                                    </a>
                                </li>
                                <li class="list-group-item px-0">
                                    <a href="/login" class="tsp-announcement-link">
                                        <span class="tsp-announcement-dot"></span>
                                        <span>आवेदन की स्थिति देखने के लिए लॉगिन करें। / Login to check your application status.</span>
                                    </a>
                                </li>
                            </ul>
                        <?php else: ?>
                            <ul class="list-group list-group-flush tsp-announcement-list">
                                <?php foreach ($announcements as $notice): ?>
                                    <li class="list-group-item px-0">
                                        <a href="#announcements" class="tsp-announcement-link">
                                            <span class="tsp-announcement-dot"></span>
                                            <span><?= Helpers::esc($notice['title'] ?? '') ?></span>
                                        </a>
                                        <?php if (!empty($notice['content'])): ?>
                                            <div class="tsp-announcement-body mt-1 text-muted small">
                                                <?= Helpers::esc(strip_tags((string) $notice['content'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Status Tracking -->
                <section class="card tsp-portal-card" id="status-tracker">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history fs-5 text-success" aria-hidden="true"></i>
                            <h2 class="h5 fw-bold mb-0">आवेदन स्थिति / Track Application</h2>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-3">
                        <p class="text-muted small mb-3">
                            संदर्भ संख्या (जैसे: <code>TSVS-2026-000001</code>) डालकर अपने आवेदन की स्थिति जांचें।
                        </p>

                        <form action="/#status-tracker" method="GET" class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="track_ref"
                                       class="form-control border-start-0"
                                       placeholder="TSVS-2026-000001"
                                       value="<?= Helpers::esc($trackRef) ?>"
                                       required
                                       autocomplete="off"
                                       aria-label="Reference number">
                                <button type="submit" class="btn btn-success fw-semibold px-4">
                                    खोजें / Track
                                </button>
                            </div>
                        </form>

                        <?php if ($trackError): ?>
                            <div class="alert alert-danger border-0 small">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                                <?= Helpers::esc($trackError) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($trackResult): ?>
                            <?php
                            $status = $trackResult['status_name'];
                            $steps = ['Pending' => 1, 'Disputed' => 2, 'Approved' => 3, 'Rejected' => 3];
                            $currentStep = $steps[$status] ?? 1;
                            $refCode = 'TSVS-2026-' . str_pad((string)$trackResult['id'], 6, '0', STR_PAD_LEFT);
                            ?>
                            <div class="p-3 bg-light rounded border border-light-subtle mt-2">

                                <!-- Applicant summary row -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-3 border-bottom">
                                    <div>
                                        <div class="fw-bold text-dark">
                                            <?= Helpers::esc($trackResult['first_name'] . ' ' . $trackResult['last_name']) ?>
                                        </div>
                                        <div class="text-muted small">
                                            <?= Helpers::esc($trackResult['app_type_name']) ?>
                                            &nbsp;&bull;&nbsp;
                                            <?= Helpers::esc($trackResult['session_name']) ?>
                                        </div>
                                    </div>
                                    <span class="badge bg-secondary"><?= Helpers::esc($refCode) ?></span>
                                </div>

                                <!-- Progress timeline -->
                                <div class="tsp-timeline-horizontal py-2">
                                    <div class="tsp-timeline-line">
                                        <div class="tsp-timeline-step <?= $currentStep >= 1 ? 'completed' : '' ?>">
                                            <div class="tsp-step-icon"><i class="bi bi-file-earmark-arrow-up-fill"></i></div>
                                            <div class="tsp-step-label">प्रस्तुत / Submitted</div>
                                        </div>
                                        <div class="tsp-timeline-step <?= $status === 'Disputed' ? 'disputed' : ($currentStep >= 2 ? 'completed' : '') ?>">
                                            <div class="tsp-step-icon"><i class="bi bi-search"></i></div>
                                            <div class="tsp-step-label">सत्यापन / Reviewing</div>
                                        </div>
                                        <div class="tsp-timeline-step <?= $status === 'Approved' ? 'approved' : ($status === 'Rejected' ? 'rejected' : '') ?>">
                                            <div class="tsp-step-icon">
                                                <?php if ($status === 'Approved'): ?>
                                                    <i class="bi bi-check-circle-fill"></i>
                                                <?php elseif ($status === 'Rejected'): ?>
                                                    <i class="bi bi-x-circle-fill"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-circle"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="tsp-step-label">
                                                <?php if ($status === 'Approved'): ?>स्वीकृत / Approved
                                                <?php elseif ($status === 'Rejected'): ?>अस्वीकृत / Rejected
                                                <?php else: ?>निर्णय / Decision
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status message -->
                                <?php if ($status === 'Disputed'): ?>
                                    <div class="alert alert-warning border-0 mt-3 mb-0 small">
                                        <h6 class="alert-heading fw-bold mb-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> आवेदन में त्रुटि / Dispute Remarks
                                        </h6>
                                        <p class="mb-2"><?= Helpers::esc($trackResult['dispute_message']) ?></p>
                                        <a href="/login" class="btn btn-warning btn-sm fw-bold w-100">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> लॉगिन करके दस्तावेज़ पुनः अपलोड करें / Login to Resolve
                                        </a>
                                    </div>
                                <?php elseif ($status === 'Approved'): ?>
                                    <div class="alert alert-success border-0 mt-3 mb-0 small">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        आपका आवेदन <strong>स्वीकृत</strong> हो चुका है। / Your application has been <strong>approved</strong>.
                                    </div>
                                <?php elseif ($status === 'Rejected'): ?>
                                    <div class="alert alert-danger border-0 mt-3 mb-0 small">
                                        <i class="bi bi-x-circle-fill me-1"></i>
                                        आपका आवेदन <strong>अस्वीकृत</strong> हो चुका है। / Your application has been <strong>rejected</strong>.
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info border-0 mt-3 mb-0 small">
                                        <i class="bi bi-info-circle-fill me-1"></i>
                                        आपका आवेदन समीक्षाधीन है। / Your application is currently under review.
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </section>

            </div>

            <!-- Right: Student Panel -->
            <div class="col-lg-5">
                <section class="card tsp-portal-card">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-people-fill fs-5 text-danger" aria-hidden="true"></i>
                            <h2 class="h5 fw-bold mb-0">विद्यार्थी पैनल / Student Panel</h2>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-3">

                        <div class="tsp-action-grid">
                            <?php foreach ($quickLinks as $link): ?>
                                <a class="tsp-action-tile" href="<?= Helpers::esc($link['href']) ?>">
                                    <span class="tsp-action-icon">
                                        <i class="bi <?= Helpers::esc($link['icon']) ?>" aria-hidden="true"></i>
                                    </span>
                                    <span class="tsp-action-label"><?= Helpers::esc($link['label']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <hr class="my-4">

                        <!-- How to Apply -->
                        <div class="tsp-panel-note" id="help">
                            <h3 class="h6 fw-bold mb-3">
                                <i class="bi bi-info-circle text-success me-1"></i>
                                आवेदन कैसे करें / How to Apply
                            </h3>
                            <ol class="small ps-3 mb-0" style="line-height: 1.8;">
                                <li>नया खाता <strong>पंजीकृत</strong> करें (Register)।</li>
                                <li><strong>प्रोफाइल</strong> और शैक्षणिक जानकारी भरें।</li>
                                <li>उचित फॉर्म चुनें — <strong>प्रतिभा सम्मान</strong> या <strong>छात्रवृत्ति</strong>।</li>
                                <li>आवश्यक दस्तावेज़ अपलोड कर <strong>सबमिट</strong> करें।</li>
                            </ol>
                        </div>

                        <hr class="my-4">

                        <!-- Helpline -->
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <i class="bi bi-envelope-fill text-success"></i>
                            <span>सहायता / Help: <a href="mailto:contact@tambolisamaj.org" class="text-success fw-semibold">contact@tambolisamaj.org</a></span>
                        </div>

                    </div>
                </section>
            </div>

        </div>
    </div>
</main>

<footer class="tsp-mini-footer">
    <div class="container py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <strong>तम्बोली समाज विकास संस्था</strong>
                <div class="small text-muted">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल — समारोह 9 अगस्त 2026, कोटा</div>
            </div>
            <div class="small text-muted">
                <i class="bi bi-envelope me-1"></i>
                <a href="mailto:contact@tambolisamaj.org" class="text-muted text-decoration-none">contact@tambolisamaj.org</a>
            </div>
        </div>
    </div>
</footer>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
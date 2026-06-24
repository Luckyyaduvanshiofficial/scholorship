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

<!-- ── HERO SECTION ── -->
<section class="tsp-hero-section py-5 py-lg-6">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-start">
                <div class="tsp-hero-badge mb-3 d-inline-flex align-items-center gap-2">
                    <span class="badge-dot"></span>
                    <span>वार्षिक प्रतिभा सम्मान एवं छात्रवृत्ति 2026 / Annual Scholarship 2026</span>
                </div>
                <h1 class="display-4 fw-extrabold mb-3 text-dark font-heading lh-sm" style="font-size: clamp(3.2rem, 5vw, 4.8rem); letter-spacing: -0.02em; font-weight: 800;">
                    तम्बोली समाज विकास संस्था, राजस्थान
                </h1>
                <p class="text-secondary mb-4 fs-5 font-subheading" style="line-height: 1.7; max-width: 520px; font-weight: 400;">
                    मेधावी छात्र-छात्राओं को सम्मानित करने एवं उच्च शिक्षा हेतु छात्रवृत्ति प्रदान करने का एकीकृत डिजिटल मंच।
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if (Auth::guest()): ?>
                        <a href="/applications/create" class="btn btn-success btn-lg px-4 py-3 rounded-pill fw-bold tsp-btn-primary shadow-sm" style="font-size: 1.45rem;">
                            आवेदन करें / Apply Now
                        </a>
                        <a href="#status-tracker" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-pill fw-bold shadow-sm" style="font-size: 1.45rem;">
                            स्थिति जांचें / Track Status
                        </a>
                    <?php else: ?>
                        <a href="<?= Auth::isAdmin() ? '/admin' : (Auth::isRepresentative() ? '/representative' : '/dashboard') ?>" class="btn btn-success btn-lg px-4 py-3 rounded-pill fw-bold tsp-btn-primary shadow-sm" style="font-size: 1.45rem;">
                            डैशबोर्ड / Go to Dashboard
                        </a>
                        <a href="/applications/create" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-pill fw-bold shadow-sm" style="font-size: 1.45rem;">
                            नया आवेदन / New Apply
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center text-lg-end">
                <div class="tsp-hero-illustration-wrapper">
                    <img src="/assets/images/student_scholarship_hero.png" alt="Student Celebration" class="img-fluid tsp-hero-image" style="max-height: 400px; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── STATISTICS SECTION ── -->
<section class="tsp-stats-section py-4 border-top border-bottom bg-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="py-3">
                    <div class="display-5 fw-bold text-dark font-heading" style="color: var(--accent) !important;">50,000+</div>
                    <div class="text-muted small fw-medium mt-1 uppercase" style="letter-spacing: 0.05em;">छात्र सम्मानित / Students Honored</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="py-3">
                    <div class="display-5 fw-bold text-dark font-heading" style="color: var(--accent) !important;">₹25L+</div>
                    <div class="text-muted small fw-medium mt-1 uppercase" style="letter-spacing: 0.05em;">छात्रवृत्ति वितरित / Scholarships Distributed</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="py-3">
                    <div class="display-5 fw-bold text-dark font-heading" style="color: var(--accent) !important;">20+</div>
                    <div class="text-muted small fw-medium mt-1 uppercase" style="letter-spacing: 0.05em;">वर्षों का सामाजिक योगदान / Years of Community Support</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── HOW IT WORKS SECTION ── -->
<section class="tsp-how-section py-5 py-lg-6 bg-light" id="how-it-works">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h3 fw-bold text-dark font-heading">आवेदन प्रक्रिया / How It Works</h2>
            <p class="text-muted">4 आसान चरणों में छात्रवृत्ति या प्रतिभा सम्मान के लिए आवेदन करें</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-3">
                <div class="tsp-step-card text-center p-4">
                    <div class="tsp-step-number mb-3">1</div>
                    <h3 class="h5 fw-bold font-heading mb-2">पंजीकरण / Register</h3>
                    <p class="text-muted small mb-0">पोर्टल पर अपनी बुनियादी जानकारी के साथ नया खाता पंजीकृत करें।</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tsp-step-card text-center p-4">
                    <div class="tsp-step-number mb-3">2</div>
                    <h3 class="h5 fw-bold font-heading mb-2">आवेदन पत्र / Apply</h3>
                    <p class="text-muted small mb-0">प्रतिभा सम्मान या छात्रवृत्ति में से अपना संबंधित फॉर्म भरें।</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tsp-step-card text-center p-4">
                    <div class="tsp-step-number mb-3">3</div>
                    <h3 class="h5 fw-bold font-heading mb-2">दस्तावेज़ / Upload Docs</h3>
                    <p class="text-muted small mb-0">मार्कशीट, बैंक पासबुक और प्रमाण पत्र सही प्रारूप में अपलोड करें।</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tsp-step-card text-center p-4">
                    <div class="tsp-step-number mb-3">4</div>
                    <h3 class="h5 fw-bold font-heading mb-2">स्थिति / Track Status</h3>
                    <p class="text-muted small mb-0">रेफरेंस कोड से अपने आवेदन के सत्यापन की स्थिति ट्रैक करें।</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── LATEST NOTICES & TRACKER SECTION ── -->
<section class="tsp-notices-section py-5 py-lg-6 bg-white" id="announcements">
    <div class="container">
        <div class="row g-5">
            <!-- Notices -->
            <div class="col-lg-7">
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <h2 class="h4 fw-bold text-dark font-heading mb-0">सूचना बोर्ड / Latest Notices</h2>
                    </div>
                </div>
                <div class="tsp-announcements-wrapper">
                    <?php if (empty($announcements)): ?>
                        <div class="tsp-notice-item p-4 mb-3 d-flex gap-3 align-items-start">
                            <span class="tsp-notice-bullet mt-2"></span>
                            <div>
                                <h4 class="h6 fw-bold mb-1">प्रतिभा सम्मान 2026 आवेदन खुला है। / Pratibha Samman 2026 Application Open.</h4>
                                <p class="text-muted small mb-0">योग्य छात्र अंतिम तिथि से पूर्व आवेदन करें। मार्कशीट एवं बैंक पासबुक अपलोड करना अनिवार्य है।</p>
                            </div>
                            <span class="badge bg-danger-subtle text-danger ms-auto">NEW</span>
                        </div>
                        <div class="tsp-notice-item p-4 mb-3 d-flex gap-3 align-items-start">
                            <span class="tsp-notice-bullet mt-2"></span>
                            <div>
                                <h4 class="h6 fw-bold mb-1">दस्तावेज़ अपलोड निर्देश / Document Upload Guidelines</h4>
                                <p class="text-muted small mb-0">सभी फाइलें स्पष्ट एवं पठनीय होनी चाहिए। पीडीएफ या जेपीजी प्रारूप ही स्वीकार्य हैं।</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($announcements as $notice): ?>
                            <div class="tsp-notice-item p-4 mb-3 d-flex gap-3 align-items-start">
                                <span class="tsp-notice-bullet mt-2"></span>
                                <div class="flex-grow-1">
                                    <h4 class="h6 fw-bold mb-1"><?= Helpers::esc($notice['title'] ?? '') ?></h4>
                                    <?php if (!empty($notice['content'])): ?>
                                        <p class="text-muted small mb-0"><?= Helpers::esc(strip_tags((string)$notice['content'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tracker -->
            <div class="col-lg-5" id="status-tracker">
                <div class="mb-4">
                    <h2 class="h4 fw-bold text-dark font-heading">आवेदन स्थिति / Track Application</h2>
                </div>
                <div class="tsp-tracker-card p-4">
                    <p class="text-muted small mb-3">
                        संदर्भ संख्या (जैसे: <code>TSVS-2026-000001</code>) डालकर अपने आवेदन की स्थिति जांचें।
                    </p>

                    <form action="/#status-tracker" method="GET" class="mb-3">
                        <div class="input-group tsp-search-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="track_ref"
                                   class="form-control border-start-0 shadow-none ps-2"
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
                        <div class="alert alert-danger border-0 small mt-2">
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
                        <div class="p-3 bg-light rounded mt-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-3 border-bottom">
                                <div>
                                    <div class="fw-bold text-dark">
                                        <?= Helpers::esc($trackResult['first_name'] . ' ' . $trackResult['last_name']) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <?= Helpers::esc($trackResult['app_type_name']) ?> &bull; <?= Helpers::esc($trackResult['session_name']) ?>
                                    </div>
                                </div>
                                <span class="badge bg-secondary"><?= Helpers::esc($refCode) ?></span>
                            </div>

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

                            <?php if ($status === 'Disputed'): ?>
                                <div class="alert alert-warning border-0 mt-3 mb-0 small">
                                    <h6 class="alert-heading fw-bold mb-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> त्रुटि निवारण / Dispute Remarks
                                    </h6>
                                    <p class="mb-2"><?= Helpers::esc($trackResult['dispute_message']) ?></p>
                                    <a href="/login" class="btn btn-warning btn-sm fw-bold w-100">
                                        लॉगिन करके दस्तावेज़ पुनः अपलोड करें / Login to Resolve
                                    </a>
                                </div>
                            <?php elseif ($status === 'Approved'): ?>
                                <div class="alert alert-success border-0 mt-3 mb-0 small">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    आपका आवेदन <strong>स्वीकृत</strong> हो चुका है। / Approved.
                                </div>
                            <?php elseif ($status === 'Rejected'): ?>
                                <div class="alert alert-danger border-0 mt-3 mb-0 small">
                                    <i class="bi bi-x-circle-fill me-1"></i>
                                    आपका आवेदन <strong>अस्वीकृत</strong> हो चुका है। / Rejected.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info border-0 mt-3 mb-0 small">
                                    <i class="bi bi-info-circle-fill me-1"></i>
                                    आपका आवेदन समीक्षाधीन है। / Under Review.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── STUDENT PORTAL ACTIONS ── -->
<section class="tsp-actions-section py-5 py-lg-6 bg-light" id="student-actions">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h3 fw-bold text-dark font-heading">त्वरित पोर्टल सेवाएं / Student Portal</h2>
            <p class="text-muted">पंजीकरण, लॉगिन अथवा अपने आवेदन से जुड़े कार्य यहां से करें</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
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
            </div>
        </div>
    </div>
</section>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
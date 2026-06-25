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

<!-- ── SECTION 1: CREAM HERO CARD ── -->
<section class="py-4 py-lg-5">
    <div class="container">
        <div class="tsp-hero-cream">
            <div class="row align-items-center g-4">
                <div class="col-md-7 col-lg-8">
                    <span class="tsp-hero-cream-badge">
                        <i class="bi bi-award-fill"></i> प्रतिभा सम्मान समारोह 2026
                    </span>
                    <h1 class="tsp-hero-cream-title">
                        प्रतिभा को सम्मान,<br>शिक्षा को प्रोत्साहन
                    </h1>
                    <p class="tsp-hero-cream-subtitle">
                        तम्बोली समाज के विद्यार्थियों की प्रतिभा, उच्च शिक्षा और<br>
                        उज्ज्वल भविष्य के लिए हमारा संकल्प।
                    </p>
                    <div class="tsp-hero-btn-group">
                        <?php if (Auth::guest()): ?>
                            <a href="/applications/create" class="tsp-hero-btn tsp-hero-btn-primary">
                                <i class="bi bi-pencil-square"></i> आवेदन करें
                            </a>
                            <a href="#status-tracker" class="tsp-hero-btn tsp-hero-btn-outline">
                                <i class="bi bi-search"></i> स्थिति देखें
                            </a>
                        <?php else: ?>
                            <a href="<?= Auth::isAdmin() ? '/admin' : (Auth::isRepresentative() ? '/representative' : '/dashboard') ?>" class="tsp-hero-btn tsp-hero-btn-primary">
                                <i class="bi bi-speedometer2"></i> डैशबोर्ड
                            </a>
                            <a href="/applications/create" class="tsp-hero-btn tsp-hero-btn-outline">
                                <i class="bi bi-file-earmark-plus-fill"></i> नया आवेदन
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-5 col-lg-4 text-center">
                    <img src="/assets/images/hero_student.png" alt="Scholarship Illustration" class="tsp-hero-cream-illustration img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 2: EVENT INFO BAR (3 cells) ── -->
<section class="pb-4 pb-lg-5">
    <div class="container">
        <div class="tsp-event-bar">
            <div class="tsp-event-bar-cell">
                <span class="tsp-event-bar-label"><i class="bi bi-calendar-event-fill"></i> आयोजन दिनांक</span>
                <span class="tsp-event-bar-value">9 अगस्त, 2026 (रविवार)</span>
            </div>
            <div class="tsp-event-bar-cell">
                <span class="tsp-event-bar-label"><i class="bi bi-geo-alt-fill"></i> स्थान</span>
                <span class="tsp-event-bar-value">कोटा, राजस्थान</span>
            </div>
            <div class="tsp-event-bar-cell">
                <span class="tsp-event-bar-label"><i class="bi bi-people-fill"></i> आयोजक</span>
                <span class="tsp-event-bar-value">तम्बोली समाज चेरिटेबल विकास समिति कोटा</span>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 3: GENERAL INSTRUCTIONS + ELIGIBILITY CRITERIA (2-col) ── -->
<section class="py-4 py-lg-5" id="instructions">
    <div class="container">
        <div class="row g-4">
            <!-- General Instructions -->
            <div class="col-lg-6">
                <div class="tsp-info-card">
                    <div class="tsp-info-card-head">
                        <span class="tsp-info-card-icon"><i class="bi bi-clipboard-check-fill"></i></span>
                        <h3 class="tsp-info-card-title">
                            सामान्य निर्देश
                            <small>General Instructions</small>
                        </h3>
                    </div>
                    <ul class="tsp-info-card-list">
                        <li><i class="bi bi-check2-circle"></i> आवेदन केवल ऑनलाइन माध्यम से ही स्वीकार किए जाएँगे।</li>
                        <li><i class="bi bi-check2-circle"></i> सभी दस्तावेज़ साफ़ एवं स्पष्ट होने चाहिए।</li>
                        <li><i class="bi bi-check2-circle"></i> भरे हुए आवेदन निर्धारित तिथि के पूर्व संबंधित प्रतिनिधि को भेजें।</li>
                        <li><i class="bi bi-check2-circle"></i> अपूर्ण या अप्रमाणित आवेदन निरस्त कर दिए जाएँगे।</li>
                        <li><i class="bi bi-check2-circle"></i> आवेदन की स्थिति पोर्टल पर लॉगिन करके देख सकते हैं।</li>
                        <li><i class="bi bi-check2-circle"></i> किसी भी प्रकार की जानकारी के लिए संस्था से संपर्क करें।</li>
                    </ul>
                    <a href="/instructions" class="tsp-info-card-link">
                        संपूर्ण निर्देश देखें <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Eligibility Criteria -->
            <div class="col-lg-6">
                <div class="tsp-info-card">
                    <div class="tsp-info-card-head">
                        <span class="tsp-info-card-icon"><i class="bi bi-patch-check-fill"></i></span>
                        <h3 class="tsp-info-card-title">
                            पात्रता मानदंड
                            <small>Eligibility Criteria</small>
                        </h3>
                    </div>
                    <ul class="tsp-info-card-list">
                        <li><i class="bi bi-check2-circle"></i> 10वीं, 12वीं, स्नातक, स्नातकोत्तर परीक्षा में 75% या अधिक अंक अनिवार्य।</li>
                        <li><i class="bi bi-check2-circle"></i> छात्रवृत्ति हेतु 10वीं, 11वीं, 12वीं में 80% तथा स्नातक, स्नातकोत्तर के लिए 70% या अधिक अंक।</li>
                        <li><i class="bi bi-check2-circle"></i> छात्र राजस्थान का स्थायी निवासी होना चाहिए।</li>
                        <li><i class="bi bi-check2-circle"></i> परिवार की वार्षिक आय निर्धारित सीमा के अंतर्गत होनी चाहिए।</li>
                        <li><i class="bi bi-check2-circle"></i> सभी अनिवार्य दस्तावेज़ स्वप्रमाणित (Self Attested) होने चाहिए।</li>
                        <li><i class="bi bi-check2-circle"></i> एक छात्र एक ही स्तर (कक्षा/कोर्स) के लिए आवेदन कर सकता है।</li>
                    </ul>
                    <a href="/criteria" class="tsp-info-card-link">
                        विस्तृत मानदंड देखें <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 4: ACTIVITIES STRIP (7 items) ── -->
<section class="py-4 py-lg-5">
    <div class="container">
        <div class="tsp-activities">
            <h2 class="tsp-activities-title">प्रतिभा सम्मान समारोह में प्रमुख गतिविधियां</h2>
            <p class="tsp-activities-subtitle">Key Activities of the Pratibha Samman Ceremony</p>
            <div class="tsp-activities-grid">
                <div class="tsp-activity-item">
                    <span class="tsp-activity-icon"><i class="bi bi-trophy-fill"></i></span>
                    <span class="tsp-activity-label">75%+ अंक वाले छात्रों का सम्मान</span>
                </div>
                <div class="tsp-activity-item">
                    <span class="tsp-activity-icon"><i class="bi bi-briefcase-fill"></i></span>
                    <span class="tsp-activity-label">कैरियर काउंसलिंग</span>
                </div>
                <div class="tsp-activity-item">
                    <span class="tsp-activity-icon"><i class="bi bi-mortarboard-fill"></i></span>
                    <span class="tsp-activity-label">छात्रवृत्ति वितरण</span>
                </div>
                <div class="tsp-activity-item">
                    <span class="tsp-activity-icon"><i class="bi bi-person-heart-fill"></i></span>
                    <span class="tsp-activity-label">वरिष्ठ नागरिकों का सम्मान</span>
                </div>
                <div class="tsp-activity-item">
                    <span class="tsp-activity-icon"><i class="bi bi-person-badge-fill"></i></span>
                    <span class="tsp-activity-label">सेवानिवृत्त सदस्यों का सम्मान</span>
                </div>
                <div class="tsp-activity-item">
                    <span class="tsp-activity-icon"><i class="bi bi-award-fill"></i></span>
                    <span class="tsp-activity-label">नवनियुक्त सदस्यों का सम्मान</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 5: LATEST NOTICES & TRACKER ── -->
<section class="py-4 py-lg-5 bg-white" id="announcements">
    <div class="container">
        <div class="row g-5">
            <!-- Notices -->
            <div class="col-lg-7">
                <div class="mb-4">
                    <h2 class="h4 fw-bold text-dark font-heading mb-0">सूचना बोर्ड / Latest Notices</h2>
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
                            <button type="submit" class="btn tsp-btn-register-solid fw-semibold px-4" style="border-radius:0 12px 12px 0;">
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

<!-- ── SECTION 5.5: DYNAMIC VERTICAL ANNOUNCEMENT MARQUEE ── -->
<section class="py-5 bg-light border-top border-bottom" style="border-color: #e2e8f0 !important;">
    <div class="container">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; background-color: #8B0000; overflow: hidden;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(255, 255, 255, 0.15);">
                    <i class="bi bi-megaphone-fill fs-4 text-warning"></i>
                    <h3 class="h5 fw-bold text-white m-0 font-heading" style="letter-spacing: 0.5px;">Announcements / घोषणाएं</h3>
                </div>
                
                <?php
                // Use database announcements, fallback if empty
                $marqueeList = $announcements;
                if (empty($marqueeList)) {
                    $marqueeList = [
                        [
                            'title' => 'प्रतिभा सम्मान 2026 आवेदन खुला है। / Pratibha Samman 2026 Application Open.',
                            'content' => 'योग्य छात्र अंतिम तिथि से पूर्व आवेदन करें। मार्कशीट एवं बैंक पासबुक अपलोड करना अनिवार्य है।',
                            'created_at' => date('Y-m-d H:i:s')
                        ],
                        [
                            'title' => 'दस्तावेज़ अपलोड निर्देश / Document Upload Guidelines',
                            'content' => 'सभी फाइलें स्पष्ट एवं पठनीय होनी चाहिए। पीडीएफ या जेपीजी प्रारूप ही स्वीकार्य हैं।',
                            'created_at' => date('Y-m-d H:i:s')
                        ]
                    ];
                }
                ?>
                
                <marquee direction="up" scrollamount="2" onmouseover="this.stop();" onmouseout="this.start();" style="height: 200px; cursor: pointer;">
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($marqueeList as $notice): 
                            $isNew = (time() - strtotime($notice['created_at'] ?? 'now')) < (2 * 24 * 3600); // 48 hours
                        ?>
                            <div class="d-flex gap-3 align-items-start py-2">
                                <div class="rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background-color: rgba(255, 255, 255, 0.15);">
                                    <i class="bi bi-chevron-right text-white fw-bold" style="font-size: 0.8rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="fw-bold mb-1 text-white" style="font-size: 1.25rem;">
                                        <?= htmlspecialchars($notice['title'] ?? '') ?>
                                        <?php if ($isNew): ?>
                                            <span class="badge rounded-pill bg-warning text-dark py-1 px-2 ms-2 fw-bold" style="font-size: 0.8rem; vertical-align: middle;">NEW</span>
                                        <?php endif; ?>
                                    </h4>
                                    <?php if (!empty($notice['content'])): ?>
                                        <p class="small mb-0 mt-1" style="font-size: 1.15rem; color: #f8fafc; opacity: 0.85; line-height: 1.5;"><?= htmlspecialchars(strip_tags((string)$notice['content'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </marquee>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 6: STUDENT PORTAL ACTIONS ── -->
<section class="py-4 py-lg-5 bg-light" id="student-actions">
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

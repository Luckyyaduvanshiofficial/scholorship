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
        ['label' => 'लॉगिन / Login', 'sublabel' => 'पोर्टल में प्रवेश', 'icon' => 'bi-box-arrow-in-right', 'href' => '/login'],
        ['label' => 'पंजीकरण / Register', 'sublabel' => 'नया खाता बनाएं', 'icon' => 'bi-person-plus-fill', 'href' => '/register'],
        ['label' => 'लॉगिन / Login', 'sublabel' => 'आवेदन करने के लिए लॉगिन करें', 'icon' => 'bi-box-arrow-in-right', 'href' => '/login'],
        ['label' => 'स्थिति / Track Status', 'sublabel' => 'आवेदन की स्थिति देखें', 'icon' => 'bi-search', 'href' => '#status-tracker'],
    ];
} else {
    $quickLinks = [
        ['label' => 'डैशबोर्ड / Dashboard', 'sublabel' => 'अपना पैनल देखें', 'icon' => 'bi-speedometer2', 'href' => Auth::isAdmin() ? '/admin' : (Auth::isRepresentative() ? '/representative' : '/dashboard')],
        ['label' => 'नया आवेदन / New Apply', 'sublabel' => 'नया आवेदन भरें', 'icon' => 'bi-file-earmark-plus-fill', 'href' => '/dashboard/applications/create'],
        ['label' => 'आवेदन सूची / My Applies', 'sublabel' => 'सभी आवेदन देखें', 'icon' => 'bi-list-ul', 'href' => '/dashboard/applications'],
        ['label' => 'प्रोफाइल / Profile', 'sublabel' => 'प्रोफाइल प्रबंधित करें', 'icon' => 'bi-person-fill', 'href' => '/dashboard/profile'],
    ];
}

// Prepare ticker messages from announcements or fallback
$tickerMessages = [];
if (!empty($announcements)) {
    foreach ($announcements as $notice) {
        $tickerMessages[] = Helpers::esc(strip_tags((string)($notice['title'] ?? '')));
    }
} else {
    $tickerMessages = [
        'प्रतिभा सम्मान समारोह 2026 - 9 अगस्त, 2026 को कोटा में आयोजित होगा / Pratibha Samman Samaroh 2026 will be held on 9 August 2026 in Kota.',
        'छात्रवृत्ति के लिए मार्कशीट एवं बैंक पासबुक अपलोड अनिवार्य है / Marksheet and Bank Passbook upload is mandatory for Scholarship.',
        'आवेदन जमा करने के बाद स्थिति मुख्य पृष्ठ पर ट्रैक करें / Track your application status on the homepage after submission.',
        'विवाद होने पर डैशबोर्ड से दस्तावेज़ पुनः अपलोड करें / For disputed applications, re-upload documents via student dashboard.'
    ];
}

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<!-- ── SECTION 1: MODERN ANNOUNCEMENT TICKER ── -->
<section class="tsp-premium-ticker" aria-label="Important notices">
    <div class="container">
        <div class="tsp-ticker-wrap">
            <span class="tsp-ticker-badge">
                <i class="bi bi-megaphone-fill"></i>
                <span>Updates</span>
            </span>
            <div class="tsp-ticker-track">
                <div class="tsp-ticker-content">
                    <?php foreach ($tickerMessages as $msg): ?>
                        <span class="tsp-ticker-item">
                            <i class="bi bi-dot"></i>
                            <?= $msg ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <div class="tsp-ticker-content" aria-hidden="true">
                    <?php foreach ($tickerMessages as $msg): ?>
                        <span class="tsp-ticker-item">
                            <i class="bi bi-dot"></i>
                            <?= $msg ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 2: PREMIUM HERO ── -->
<section class="tsp-premium-hero">
    <div class="tsp-hero-bg"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-7">
                <div class="tsp-hero-content">
                    <span class="tsp-hero-badge">
                        <i class="bi bi-award-fill"></i>
                        प्रतिभा सम्मान समारोह 2026
                    </span>
                    <h1 class="tsp-hero-title">
                        प्रतिभा को सम्मान,<br>
                        <span class="tsp-hero-title-accent">शिक्षा को प्रोत्साहन</span>
                    </h1>
                    <p class="tsp-hero-subtitle">
                        तम्बोली समाज के विद्यार्थियों की प्रतिभा, उच्च शिक्षा और उज्ज्वल भविष्य के लिए हमारा संकल्प।
                    </p>
                    <div class="tsp-hero-actions">
                        <?php if (Auth::guest()): ?>
                            <a href="/dashboard/applications/create" class="tsp-btn tsp-btn-primary tsp-btn-lg">
                                <i class="bi bi-pencil-square"></i>
                                <span>आवेदन करें / Apply</span>
                            </a>
                            <a href="#status-tracker" class="tsp-btn tsp-btn-outline tsp-btn-lg">
                                <i class="bi bi-search"></i>
                                <span>स्थिति देखें / Track</span>
                            </a>
                        <?php else: ?>
                            <a href="<?= Auth::isAdmin() ? '/admin' : (Auth::isRepresentative() ? '/representative' : '/dashboard') ?>" class="tsp-btn tsp-btn-primary tsp-btn-lg">
                                <i class="bi bi-speedometer2"></i>
                                <span>डैशबोर्ड / Dashboard</span>
                            </a>
                            <a href="/dashboard/applications/create" class="tsp-btn tsp-btn-outline tsp-btn-lg">
                                <i class="bi bi-file-earmark-plus-fill"></i>
                                <span>नया आवेदन / New Apply</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="tsp-hero-image-wrap">
                    <img src="/assets/images/hero_student.png" alt="Scholarship Illustration" class="tsp-hero-image img-fluid" width="500" height="500" fetchpriority="high">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 3: EVENT INFO CARDS ── -->
<section class="tsp-premium-section tsp-section-compact">
    <div class="container">
        <div class="row g-3 g-lg-4">
            <div class="col-md-4">
                <div class="tsp-event-card">
                    <div class="tsp-event-icon">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                    <div class="tsp-event-info">
                        <span class="tsp-event-label">आयोजन दिनांक / Date</span>
                        <span class="tsp-event-value">9 अगस्त, 2026</span>
                        <span class="tsp-event-meta">रविवार / Sunday</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tsp-event-card">
                    <div class="tsp-event-icon tsp-event-icon-gold">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div class="tsp-event-info">
                        <span class="tsp-event-label">स्थान / Venue</span>
                        <span class="tsp-event-value">कोटा, राजस्थान</span>
                        <span class="tsp-event-meta">Kota, Rajasthan</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tsp-event-card">
                    <div class="tsp-event-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="tsp-event-info">
                        <span class="tsp-event-label">आयोजक / Organizer</span>
                        <span class="tsp-event-value">तम्बोली समाज चेरिटेबल विकास समिति</span>
                        <span class="tsp-event-meta">Tamboli Samaj Charitable Vikas Samiti, Kota</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 4: INSTRUCTIONS + ELIGIBILITY ── -->
<section class="tsp-premium-section" id="instructions">
    <div class="container">
        <div class="row g-4">
            <!-- General Instructions -->
            <div class="col-lg-6">
                <div class="tsp-premium-card tsp-card-accent-maroon">
                    <div class="tsp-card-header">
                        <div class="tsp-card-icon">
                            <i class="bi bi-clipboard-check-fill"></i>
                        </div>
                        <div>
                            <h2 class="tsp-card-title">सामान्य निर्देश</h2>
                            <span class="tsp-card-subtitle">General Instructions</span>
                        </div>
                    </div>
                    <ul class="tsp-card-list">
                        <li><i class="bi bi-check2-circle"></i> आवेदन केवल ऑनलाइन माध्यम से ही स्वीकार किए जाएँगे।</li>
                        <li><i class="bi bi-check2-circle"></i> सभी दस्तावेज़ साफ़ एवं स्पष्ट होने चाहिए।</li>
                        <li><i class="bi bi-check2-circle"></i> भरे हुए आवेदन निर्धारित तिथि के पूर्व संबंधित प्रतिनिधि को भेजें।</li>
                        <li><i class="bi bi-check2-circle"></i> अपूर्ण या अप्रमाणित आवेदन निरस्त कर दिए जाएँगे।</li>
                        <li><i class="bi bi-check2-circle"></i> आवेदन की स्थिति पोर्टल पर लॉगिन करके देख सकते हैं।</li>
                        <li><i class="bi bi-check2-circle"></i> किसी भी प्रकार की जानकारी के लिए संस्था से संपर्क करें।</li>
                    </ul>
                    <a href="/instructions" class="tsp-card-link">
                        संपूर्ण निर्देश देखें <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Eligibility Criteria -->
            <div class="col-lg-6">
                <div class="tsp-premium-card tsp-card-accent-gold">
                    <div class="tsp-card-header">
                        <div class="tsp-card-icon tsp-card-icon-gold">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div>
                            <h2 class="tsp-card-title">पात्रता मानदंड</h2>
                            <span class="tsp-card-subtitle">Eligibility Criteria</span>
                        </div>
                    </div>
                    <ul class="tsp-card-list">
                        <li><i class="bi bi-check2-circle"></i> 10वीं, 12वीं, स्नातक, स्नातकोत्तर में 75% या अधिक अंक अनिवार्य।</li>
                        <li><i class="bi bi-check2-circle"></i> छात्रवृत्ति हेतु 10वीं-12वीं में 80% तथा स्नातक/स्नातकोत्तर में 70%।</li>
                        <li><i class="bi bi-check2-circle"></i> छात्र राजस्थान का स्थायी निवासी होना चाहिए।</li>
                        <li><i class="bi bi-check2-circle"></i> परिवार की वार्षिक आय निर्धारित सीमा के अंतर्गत होनी चाहिए।</li>
                        <li><i class="bi bi-check2-circle"></i> सभी अनिवार्य दस्तावेज़ स्वप्रमाणित (Self Attested) हों।</li>
                        <li><i class="bi bi-check2-circle"></i> एक छात्र एक ही स्तर (कक्षा/कोर्स) के लिए आवेदन कर सकता है।</li>
                    </ul>
                    <a href="/criteria" class="tsp-card-link">
                        विस्तृत मानदंड देखें <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 5: KEY ACTIVITIES BENTO GRID ── -->
<section class="tsp-premium-section tsp-section-cream">
    <div class="container">
        <div class="tsp-section-header text-center">
            <span class="tsp-section-eyebrow">समारोह की मुख्य झलक</span>
            <h2 class="tsp-section-title">प्रतिभा सम्मान समारोह में प्रमुख गतिविधियां</h2>
            <p class="tsp-section-desc">Key Activities of the Pratibha Samman Ceremony</p>
        </div>
        <div class="row g-3 g-lg-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="tsp-activity-card">
                    <div class="tsp-activity-icon-wrap">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <h3 class="tsp-activity-title">75%+ अंक वाले छात्रों का सम्मान</h3>
                    <p class="tsp-activity-desc">Honoring high-achieving students</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="tsp-activity-card">
                    <div class="tsp-activity-icon-wrap">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <h3 class="tsp-activity-title">कैरियर काउंसलिंग</h3>
                    <p class="tsp-activity-desc">Career guidance sessions</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="tsp-activity-card">
                    <div class="tsp-activity-icon-wrap">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h3 class="tsp-activity-title">छात्रवृत्ति वितरण</h3>
                    <p class="tsp-activity-desc">Scholarship distribution</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="tsp-activity-card">
                    <div class="tsp-activity-icon-wrap">
                        <i class="bi bi-person-heart-fill"></i>
                    </div>
                    <h3 class="tsp-activity-title">वरिष्ठ नागरिकों का सम्मान</h3>
                    <p class="tsp-activity-desc">Honoring senior citizens</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="tsp-activity-card">
                    <div class="tsp-activity-icon-wrap">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <h3 class="tsp-activity-title">सेवानिवृत्त सदस्यों का सम्मान</h3>
                    <p class="tsp-activity-desc">Honoring retired members</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="tsp-activity-card">
                    <div class="tsp-activity-icon-wrap">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h3 class="tsp-activity-title">नवनियुक्त सदस्यों का सम्मान</h3>
                    <p class="tsp-activity-desc">Honoring newly appointed members</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 6: NOTICES + TRACKER ── -->
<section class="tsp-premium-section" id="announcements">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <!-- Notices -->
            <div class="col-lg-7">
                <div class="tsp-section-header text-center text-lg-start">
                    <span class="tsp-section-eyebrow">ताज़ा जानकारी</span>
                    <h2 class="tsp-section-title">सूचना बोर्ड / Latest Notices</h2>
                </div>
                <div class="tsp-notices-list">
                    <?php if (empty($announcements)): ?>
                        <article class="tsp-notice-card">
                            <div class="tsp-notice-icon">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                            <div class="tsp-notice-body">
                                <div class="tsp-notice-meta">
                                    <span class="tsp-notice-badge tsp-notice-badge-new">NEW</span>
                                    <span class="tsp-notice-date"><?= date('d M Y') ?></span>
                                </div>
                                <h3 class="tsp-notice-title">प्रतिभा सम्मान 2026 आवेदन खुला है। / Pratibha Samman 2026 Application Open.</h3>
                                <p class="tsp-notice-text">योग्य छात्र अंतिम तिथि से पूर्व आवेदन करें। मार्कशीट एवं बैंक पासबुक अपलोड करना अनिवार्य है।</p>
                            </div>
                        </article>
                        <article class="tsp-notice-card">
                            <div class="tsp-notice-icon tsp-notice-icon-blue">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <div class="tsp-notice-body">
                                <div class="tsp-notice-meta">
                                    <span class="tsp-notice-date"><?= date('d M Y') ?></span>
                                </div>
                                <h3 class="tsp-notice-title">दस्तावेज़ अपलोड निर्देश / Document Upload Guidelines</h3>
                                <p class="tsp-notice-text">सभी फाइलें स्पष्ट एवं पठनीय होनी चाहिए। पीडीएफ या जेपीजी प्रारूप ही स्वीकार्य हैं।</p>
                            </div>
                        </article>
                    <?php else: ?>
                        <?php foreach ($announcements as $notice):
                            $isNew = (time() - strtotime($notice['created_at'] ?? 'now')) < (2 * 24 * 3600);
                            $noticeDate = date('d M Y', strtotime($notice['created_at'] ?? 'now'));
                        ?>
                            <article class="tsp-notice-card">
                                <div class="tsp-notice-icon <?= $isNew ? '' : 'tsp-notice-icon-blue' ?>">
                                    <i class="bi <?= $isNew ? 'bi-bell-fill' : 'bi-megaphone-fill' ?>"></i>
                                </div>
                                <div class="tsp-notice-body">
                                    <div class="tsp-notice-meta">
                                        <?php if ($isNew): ?>
                                            <span class="tsp-notice-badge tsp-notice-badge-new">NEW</span>
                                        <?php endif; ?>
                                        <span class="tsp-notice-date"><?= Helpers::esc($noticeDate) ?></span>
                                    </div>
                                    <h3 class="tsp-notice-title"><?= Helpers::esc($notice['title'] ?? '') ?></h3>
                                    <?php if (!empty($notice['content'])): ?>
                                        <p class="tsp-notice-text"><?= Helpers::esc(strip_tags((string)$notice['content'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tracker -->
            <div class="col-lg-5" id="status-tracker">
                <div class="tsp-section-header text-center text-lg-start">
                    <span class="tsp-section-eyebrow">आसान जांच</span>
                    <h2 class="tsp-section-title">आवेदन स्थिति / Track Application</h2>
                </div>
                <div class="tsp-tracker-card">
                    <p class="tsp-tracker-intro">
                        संदर्भ संख्या (जैसे: <code>TSVS-2026-000001</code>) डालकर अपने आवेदन की स्थिति जांचें।
                    </p>

                    <form action="/#status-tracker" method="GET" class="tsp-tracker-form">
                        <div class="tsp-tracker-input-wrap">
                            <span class="tsp-tracker-input-icon">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="track_ref"
                                   class="tsp-tracker-input"
                                   placeholder="TSVS-2026-000001"
                                   value="<?= Helpers::esc($trackRef) ?>"
                                   required
                                   autocomplete="off"
                                   aria-label="Reference number">
                            <button type="submit" class="tsp-tracker-submit">
                                खोजें
                            </button>
                        </div>
                    </form>

                    <?php if ($trackError): ?>
                        <div class="tsp-tracker-alert tsp-tracker-alert-error">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span><?= Helpers::esc($trackError) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($trackResult): ?>
                        <?php
                        $status = $trackResult['status_name'];
                        $steps = ['Pending' => 1, 'Disputed' => 2, 'Approved' => 3, 'Rejected' => 3];
                        $currentStep = $steps[$status] ?? 1;
                        $refCode = 'TSVS-2026-' . str_pad((string)$trackResult['id'], 6, '0', STR_PAD_LEFT);
                        ?>
                        <div class="tsp-tracker-result">
                            <div class="tsp-tracker-result-header">
                                <div>
                                    <div class="tsp-tracker-name">
                                        <?= Helpers::esc($trackResult['first_name'] . ' ' . $trackResult['last_name']) ?>
                                    </div>
                                    <div class="tsp-tracker-meta">
                                        <?= Helpers::esc($trackResult['app_type_name']) ?> &bull; <?= Helpers::esc($trackResult['session_name']) ?>
                                    </div>
                                </div>
                                <span class="tsp-tracker-ref"><?= Helpers::esc($refCode) ?></span>
                            </div>

                            <div class="tsp-timeline">
                                <div class="tsp-timeline-progress">
                                    <div class="tsp-timeline-progress-bar" style="width: <?= ($currentStep / 3) * 100 ?>%;"></div>
                                </div>
                                <div class="tsp-timeline-steps">
                                    <div class="tsp-timeline-step <?= $currentStep >= 1 ? 'completed' : '' ?>">
                                        <div class="tsp-timeline-dot">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <span class="tsp-timeline-label">प्रस्तुत</span>
                                        <span class="tsp-timeline-sublabel">Submitted</span>
                                    </div>
                                    <div class="tsp-timeline-step <?= $status === 'Disputed' ? 'disputed' : ($currentStep >= 2 ? 'completed' : '') ?>">
                                        <div class="tsp-timeline-dot">
                                            <?php if ($status === 'Disputed'): ?>
                                                <i class="bi bi-exclamation-lg"></i>
                                            <?php elseif ($currentStep >= 2): ?>
                                                <i class="bi bi-check-lg"></i>
                                            <?php else: ?>
                                                <span></span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="tsp-timeline-label">सत्यापन</span>
                                        <span class="tsp-timeline-sublabel">Reviewing</span>
                                    </div>
                                    <div class="tsp-timeline-step <?= $status === 'Approved' ? 'approved' : ($status === 'Rejected' ? 'rejected' : '') ?>">
                                        <div class="tsp-timeline-dot">
                                            <?php if ($status === 'Approved'): ?>
                                                <i class="bi bi-check-lg"></i>
                                            <?php elseif ($status === 'Rejected'): ?>
                                                <i class="bi bi-x-lg"></i>
                                            <?php else: ?>
                                                <span></span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="tsp-timeline-label">
                                            <?php if ($status === 'Approved'): ?>स्वीकृत
                                            <?php elseif ($status === 'Rejected'): ?>अस्वीकृत
                                            <?php else: ?>निर्णय
                                            <?php endif; ?>
                                        </span>
                                        <span class="tsp-timeline-sublabel">
                                            <?php if ($status === 'Approved'): ?>Approved
                                            <?php elseif ($status === 'Rejected'): ?>Rejected
                                            <?php else: ?>Decision
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <?php if ($status === 'Disputed'): ?>
                                <div class="tsp-tracker-alert tsp-tracker-alert-warning">
                                    <h6><i class="bi bi-exclamation-triangle-fill"></i> त्रुटि निवारण / Dispute Remarks</h6>
                                    <p><?= Helpers::esc($trackResult['dispute_message']) ?></p>
                                    <a href="/login" class="tsp-btn tsp-btn-warning tsp-btn-sm w-100">
                                        लॉगिन करके दस्तावेज़ पुनः अपलोड करें
                                    </a>
                                </div>
                            <?php elseif ($status === 'Approved'): ?>
                                <div class="tsp-tracker-alert tsp-tracker-alert-success">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>आपका आवेदन <strong>स्वीकृत</strong> हो चुका है। / Approved.</span>
                                </div>
                            <?php elseif ($status === 'Rejected'): ?>
                                <div class="tsp-tracker-alert tsp-tracker-alert-error">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>आपका आवेदन <strong>अस्वीकृत</strong> हो चुका है। / Rejected.</span>
                                </div>
                            <?php else: ?>
                                <div class="tsp-tracker-alert tsp-tracker-alert-info">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <span>आपका आवेदन समीक्षाधीन है। / Under Review.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SECTION 7: QUICK PORTAL ACTIONS ── -->
<section class="tsp-premium-section tsp-section-cream" id="student-actions">
    <div class="container">
        <div class="tsp-section-header text-center">
            <span class="tsp-section-eyebrow">छात्र पोर्टल</span>
            <h2 class="tsp-section-title">त्वरित पोर्टल सेवाएं / Student Portal</h2>
            <p class="tsp-section-desc">पंजीकरण, लॉगिन अथवा अपने आवेदन से जुड़े कार्य यहां से करें</p>
        </div>
        <div class="row g-3 g-lg-4 justify-content-center">
            <?php foreach ($quickLinks as $index => $link): ?>
                <div class="col-6 col-lg-3">
                    <a class="tsp-quick-card" href="<?= Helpers::esc($link['href']) ?>" style="--tsp-quick-delay: <?= $index ?>">
                        <div class="tsp-quick-icon">
                            <i class="bi <?= Helpers::esc($link['icon']) ?>" aria-hidden="true"></i>
                        </div>
                        <span class="tsp-quick-label"><?= Helpers::esc($link['label']) ?></span>
                        <span class="tsp-quick-sublabel"><?= Helpers::esc($link['sublabel']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
. '/layouts/footer.php'; ?>

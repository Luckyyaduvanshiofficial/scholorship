<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$role = 'student';

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <?php
    $activeLink = 'dashboard';
    require VIEW_PATH . '/layouts/student-sidebar.php';
    ?>

    <!-- Main Content Area -->
    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">

            <!-- ════════════════════════════════════ -->
            <!-- HERO: Welcome + Student Identity Bar -->
            <!-- ════════════════════════════════════ -->
            <div class="tsp-stu-hero mb-4">
                <div class="tsp-stu-hero-main">
                    <div class="tsp-stu-avatar-wrap">
                        <?php $photo = Auth::profilePhoto(); if ($photo): ?>
                            <img src="<?= Helpers::esc($photo) ?>" alt="Photo" class="tsp-stu-avatar-img">
                        <?php else: ?>
                            <div class="tsp-stu-avatar-placeholder">
                                <?= mb_substr(Helpers::esc(Auth::userName()), 0, 2, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                        <span class="tsp-stu-online-dot"></span>
                    </div>
                    <div class="tsp-stu-hero-text">
                        <h1 class="tsp-stu-greeting">नमस्ते, <?= Helpers::esc(Auth::userName()) ?> 👋</h1>
                        <div class="tsp-stu-meta">
                            <?php if ($studentCode): ?>
                                <span class="tsp-stu-badge tsp-stu-badge-outline">
                                    <i class="bi bi-person-badge"></i> <?= Helpers::esc($studentCode) ?>
                                </span>
                            <?php endif; ?>
                            <span class="tsp-stu-badge tsp-stu-badge-maroon">
                                <i class="bi bi-calendar-event"></i>
                                सत्र: <?= Helpers::esc($activeSession['session_name'] ?? 'N/A') ?>
                            </span>
                        </div>
                    </div>
                    <div class="tsp-stu-hero-action">
                        <?php if ($totalApps === 0): ?>
                            <a href="/dashboard/applications/create" class="btn tsp-btn-primary">
                                <i class="bi bi-plus-lg"></i> नया आवेदन
                            </a>
                        <?php elseif ($draftApps > 0): ?>
                            <a href="/dashboard/applications" class="btn tsp-btn-secondary">
                                <i class="bi bi-pencil"></i> ड्राफ्ट जारी रखें
                            </a>
                        <?php else: ?>
                            <a href="/dashboard/applications" class="btn tsp-btn-secondary">
                                <i class="bi bi-file-earmark-text"></i> मेरे आवेदन
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="tsp-stu-hero-sub">
                    प्रतिभा सम्मान समारोह 2026 के लिए आवेदन प्रक्रिया सरल और पूरी तरह ऑनलाइन है। नीचे अपने आवेदनों की स्थिति देखें।
                </p>
            </div>

            <!-- ════════════════════════════════════ -->
            <!-- STATS ROW: 4 KPI Cards              -->
            <!-- ════════════════════════════════════ -->
            <div class="tsp-stu-stats-grid mb-4">
                <div class="tsp-stu-stat-card">
                    <div class="tsp-stu-stat-icon" style="background: rgba(139, 26, 43, 0.1); color: #8B1A2B;">
                        <i class="bi bi-files"></i>
                    </div>
                    <div class="tsp-stu-stat-body">
                        <span class="tsp-stu-stat-num"><?= $totalApps ?></span>
                        <span class="tsp-stu-stat-label">कुल आवेदन</span>
                    </div>
                </div>
                <div class="tsp-stu-stat-card">
                    <div class="tsp-stu-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="tsp-stu-stat-body">
                        <span class="tsp-stu-stat-num"><?= $pendingApps + $draftApps ?></span>
                        <span class="tsp-stu-stat-label">लंबित / ड्राफ्ट</span>
                    </div>
                </div>
                <div class="tsp-stu-stat-card">
                    <div class="tsp-stu-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div class="tsp-stu-stat-body">
                        <span class="tsp-stu-stat-num"><?= $approvedApps ?></span>
                        <span class="tsp-stu-stat-label">स्वीकृत</span>
                    </div>
                </div>
                <div class="tsp-stu-stat-card">
                    <div class="tsp-stu-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="tsp-stu-stat-body">
                        <span class="tsp-stu-stat-num"><?= $profileCompletion ?>%</span>
                        <span class="tsp-stu-stat-label">प्रोफाइल पूर्णता</span>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════ -->
            <!-- APPLICATIONS SECTION                -->
            <!-- ════════════════════════════════════ -->
            <div class="tsp-stu-section-header">
                <h2 class="tsp-stu-section-title"><i class="bi bi-file-earmark-text me-2"></i>मेरे आवेदन</h2>
                <a href="/dashboard/applications" class="tsp-stu-section-link">सभी देखें <i class="bi bi-arrow-right"></i></a>
            </div>

            <?php if (count($applications) > 0): ?>
                <div class="tsp-stu-apps-list mb-4">
                    <?php foreach ($applications as $app):
                        $status = $app['status_name'] ?? 'Draft';
                        $typeIcon = ($app['type'] ?? '') === 'scholarship' ? 'bi-mortarboard' : 'bi-trophy';
                        $typeLabel = ($app['type'] ?? '') === 'scholarship' ? 'छात्रवृत्ति' : 'प्रतिभा सम्मान';
                        $isDraft = ($app['submitted_at'] === null || $status === 'Draft');

                        // Status badge style & label mapping
                        $badgeClass = 'bg-secondary text-white';
                        $statusLabel = 'ड्राफ्ट (Draft)';

                        if ($isDraft) {
                            $badgeClass = 'bg-secondary text-white';
                            $statusLabel = 'ड्राफ्ट (Draft)';
                        } else {
                            switch ($status) {
                                case 'Submitted':
                                    $badgeClass = 'bg-primary text-white';
                                    $statusLabel = 'जमा किया गया (Submitted)';
                                    break;
                                case 'Under Review':
                                    $badgeClass = 'bg-warning text-dark';
                                    $statusLabel = 'समीक्षाधीन (Under Review)';
                                    break;
                                case 'Approved':
                                    $badgeClass = 'bg-success text-white';
                                    $statusLabel = 'स्वीकृत (Approved)';
                                    break;
                                case 'Rejected':
                                    $badgeClass = 'bg-danger text-white';
                                    $statusLabel = 'अस्वीकृत (Rejected)';
                                    break;
                                case 'Pending Correction':
                                    $badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning';
                                    $statusLabel = 'सुधार लंबित (Pending Correction)';
                                    break;
                                case 'Resubmitted':
                                    $badgeClass = 'bg-info text-dark';
                                    $statusLabel = 'पुनः जमा (Resubmitted)';
                                    break;
                                default:
                                    $badgeClass = 'bg-secondary text-white';
                                    $statusLabel = $status;
                                    break;
                            }
                        }

                        $appNum = !empty($app['application_no']) ? $app['application_no'] : ('TSVS-' . date('Y', strtotime($app['created_at'] ?? 'now')) . '-' . str_pad((string) $app['id'], 6, '0', STR_PAD_LEFT));
                        $createdDate = !empty($app['created_at']) ? date('d M Y', strtotime($app['created_at'])) : '—';
                        $submittedDate = !empty($app['submitted_at']) ? date('d M Y', strtotime($app['submitted_at'])) : null;
                    ?>
                        <div class="tsp-stu-app-card">
                            <div class="tsp-stu-app-left">
                                <div class="tsp-stu-app-icon <?= ($app['type'] ?? '') === 'scholarship' ? 'tsp-icon-scholarship' : 'tsp-icon-pratibha' ?>">
                                    <i class="bi <?= $typeIcon ?>"></i>
                                </div>
                                <div class="tsp-stu-app-info">
                                    <div class="tsp-stu-app-top">
                                        <strong class="tsp-stu-app-type"><?= $typeLabel ?></strong>
                                        <span class="tsp-stu-app-id">#<?= Helpers::esc($appNum) ?></span>
                                    </div>
                                    <div class="tsp-stu-app-meta">
                                        <span><i class="bi bi-calendar3"></i> <?= $isDraft ? 'बनाया: ' . $createdDate : 'सबमिट: ' . ($submittedDate ?: $createdDate) ?></span>
                                        <span class="tsp-stu-app-session"><i class="bi bi-calendar-event"></i> <?= Helpers::esc($app['session_name'] ?? '') ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="tsp-stu-app-right">
                                <span class="badge py-2 px-3 rounded-pill <?= $badgeClass ?>" style="font-size: 0.85rem;"><?= $statusLabel ?></span>
                                
                                <?php if (in_array($status, ['Rejected', 'Pending Correction'], true) && !empty($app['correction_deadline'])): ?>
                                    <?php
                                    $dlTime = strtotime($app['correction_deadline']);
                                    $diff = $dlTime - time();
                                    ?>
                                    <?php if ($diff > 0): ?>
                                        <div class="mt-2 text-danger small font-monospace fw-bold" id="cd-<?= $app['id'] ?>" data-time="<?= $dlTime ?>">
                                            ⏳ Counting...
                                        </div>
                                        <script>
                                            (function() {
                                                const el = document.getElementById('cd-<?= $app['id'] ?>');
                                                const deadline = parseInt(el.getAttribute('data-time')) * 1000;
                                                function update() {
                                                    const now = new Date().getTime();
                                                    const t = deadline - now;
                                                    if (t <= 0) {
                                                        el.innerHTML = "⚠️ Expired";
                                                        return;
                                                    }
                                                    const days = Math.floor(t / (1000 * 60 * 60 * 24));
                                                    const hours = Math.floor((t % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                    const minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
                                                    const seconds = Math.floor((t % (1000 * 60)) / 1000);
                                                    el.innerHTML = `⏳ ${days}d ${hours}h ${minutes}m left`;
                                                }
                                                update();
                                                setInterval(update, 1000);
                                            })();
                                        </script>
                                    <?php else: ?>
                                        <div class="mt-2 text-danger small font-monospace fw-bold">⚠️ Expired</div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <div class="tsp-stu-app-actions mt-3">
                                    <?php if ($isDraft || in_array($status, ['Rejected', 'Pending Correction'], true)): ?>
                                        <a href="/dashboard/applications/<?= (int) $app['id'] ?>/edit" class="btn tsp-btn-sm tsp-btn-warning w-100 mt-2">
                                            <i class="bi bi-pencil"></i> <?= ($status === 'Rejected' || $status === 'Pending Correction') ? 'सुधार करें / Correct' : 'जारी रखें / Continue' ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="/dashboard/applications/<?= (int) $app['id'] ?>" class="btn tsp-btn-sm tsp-btn-outline w-100 mt-2">
                                            <i class="bi bi-eye"></i> देखें / View
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="tsp-stu-empty mb-4">
                    <div class="tsp-stu-empty-icon">
                        <i class="bi bi-file-earmark-plus"></i>
                    </div>
                    <h3 class="tsp-stu-empty-title">अभी तक कोई आवेदन नहीं</h3>
                    <p class="tsp-stu-empty-desc">
                        प्रतिभा सम्मान समारोह 2026 के लिए आवेदन करना शुरू करें। सभी पात्र छात्र आवेदन कर सकते हैं।
                    </p>
                    <a href="/dashboard/applications/create" class="btn tsp-btn-primary tsp-btn-lg">
                        <i class="bi bi-plus-lg"></i> आवेदन फॉर्म भरना शुरू करें
                    </a>
                </div>
            <?php endif; ?>

            <!-- ════════════════════════════════════ -->
            <!-- INFO CARDS: How it works | Dates |  -->
            <!-- Help                                -->
            <!-- ════════════════════════════════════ -->
            <div class="tsp-stu-cards-grid">
                
                <!-- Card 1: यह कैसे काम करता है? -->
                <div class="tsp-stu-card">
                    <div class="tsp-stu-card-head">
                        <div class="tsp-stu-card-icon tsp-icon-green">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h3 class="tsp-stu-card-title">यह कैसे काम करता है?</h3>
                    </div>
                    <ul class="tsp-stu-checklist">
                        <li><i class="bi bi-check-circle-fill" style="color: var(--accent, #16a34a);"></i> आवेदन फॉर्म भरें</li>
                        <li><i class="bi bi-check-circle-fill" style="color: var(--accent, #16a34a);"></i> फॉर्म सबमिट करें</li>
                        <li><i class="bi bi-check-circle-fill" style="color: var(--accent, #16a34a);"></i> सत्यापन प्रक्रिया</li>
                        <li><i class="bi bi-check-circle-fill" style="color: var(--accent, #16a34a);"></i> चयन होने पर सूचना प्राप्त करें</li>
                    </ul>
                </div>

                <!-- Card 2: महत्वपूर्ण तिथियाँ -->
                <div class="tsp-stu-card" id="help">
                    <div class="tsp-stu-card-head">
                        <div class="tsp-stu-card-icon tsp-icon-blue">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <h3 class="tsp-stu-card-title">महत्वपूर्ण तिथियाँ</h3>
                    </div>
                    <ul class="tsp-stu-datelist">
                        <li>
                            <i class="bi bi-calendar-event"></i>
                            <div><span class="tsp-stu-dl-label">आवेदन की अंतिम तिथि</span><span class="tsp-stu-dl-val"><?= Helpers::esc($appDeadline) ?></span></div>
                        </li>
                        <li>
                            <i class="bi bi-trophy"></i>
                            <div><span class="tsp-stu-dl-label">सम्मान समारोह</span><span class="tsp-stu-dl-val"><?= Helpers::esc($ceremonyDate) ?></span></div>
                        </li>
                        <li>
                            <i class="bi bi-geo-alt"></i>
                            <div><span class="tsp-stu-dl-label">स्थान</span><span class="tsp-stu-dl-val">कोटा, राजस्थान</span></div>
                        </li>
                    </ul>
                </div>

                <!-- Card 3: सहायता एवं संपर्क -->
                <div class="tsp-stu-card">
                    <div class="tsp-stu-card-head">
                        <div class="tsp-stu-card-icon tsp-icon-orange">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <h3 class="tsp-stu-card-title">सहायता एवं संपर्क</h3>
                    </div>
                    <div class="tsp-stu-contacts">
                        <div class="tsp-stu-contact-row">
                            <div class="tsp-stu-contact-info">
                                <span class="tsp-stu-contact-name">श्री महेंद्र सिंह ढोंकावत</span>
                                <span class="tsp-stu-contact-phone">8432307146</span>
                            </div>
                            <a href="tel:8432307146" class="tsp-stu-call-btn"><i class="bi bi-telephone-fill"></i></a>
                        </div>
                        <div class="tsp-stu-contact-row">
                            <div class="tsp-stu-contact-info">
                                <span class="tsp-stu-contact-name">श्री विनय सिंह</span>
                                <span class="tsp-stu-contact-phone">9414336466</span>
                            </div>
                            <a href="tel:9414336466" class="tsp-stu-call-btn"><i class="bi bi-telephone-fill"></i></a>
                        </div>
                        <div class="tsp-stu-contact-row">
                            <div class="tsp-stu-contact-info">
                                <span class="tsp-stu-contact-name">श्री सुभाष धम्मनियां</span>
                                <span class="tsp-stu-contact-phone">9829771477</span>
                            </div>
                            <a href="tel:9829771477" class="tsp-stu-call-btn"><i class="bi bi-telephone-fill"></i></a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>
</div>

<!-- Sidebar toggle -->
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

<!-- Help scroll -->
<script>
(function () {
    'use strict';
    var helpLink = document.getElementById('helpSidebarLink');
    if (!helpLink) return;
    helpLink.addEventListener('click', function (e) {
        var helpCard = document.getElementById('help');
        if (helpCard) {
            e.preventDefault();
            helpCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            helpCard.style.outline = '2px solid var(--maroon-dash)';
            setTimeout(function () { helpCard.style.outline = 'none'; }, 2000);
        }
    });
})();
</script>

<?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>

</body>
</html>

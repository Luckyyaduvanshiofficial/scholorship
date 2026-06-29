<?php
use App\Core\Auth;
use App\Core\Csrf;

// Load the standard HTML head & custom CSS
require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';

// Set timezone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

// Setup Hindi Month and Day Names for Server-Side Fallback Date/Time Strip
$months = [
    'January' => 'जनवरी', 'February' => 'फरवरी', 'March' => 'मार्च', 'April' => 'अप्रैल',
    'May' => 'मई', 'June' => 'जून', 'July' => 'जुलाई', 'August' => 'अगस्त',
    'September' => 'सितंबर', 'October' => 'अक्टूबर', 'November' => 'नवंबर', 'December' => 'दिसंबर'
];
$days = [
    'Sunday' => 'रविवार', 'Monday' => 'सोमवार', 'Tuesday' => 'मंगलवार', 'Wednesday' => 'बुधवार',
    'Thursday' => 'गुरुवार', 'Friday' => 'शुक्रवार', 'Saturday' => 'शनिवार'
];

$engMonth = date('F');
$engDay = date('l');
$hindiMonth = $months[$engMonth] ?? $engMonth;
$hindiDay = $days[$engDay] ?? $engDay;
$hindiDateStr = date('d') . ' ' . $hindiMonth . ' ' . date('Y') . ', ' . $hindiDay;
$timeStr = date('h:i A');

// Helper to determine badge classes and texts for status mapping
function getHindiStatusInfo($app) {
    $statusName = strtolower($app['status_name'] ?? '');
    if ($statusName === 'pending') {
        if ($app['id'] % 2 === 0) {
            return ['text' => 'सबमिटिट', 'class' => 'tsp-bg-green'];
        } else {
            return ['text' => 'जांचधीन', 'class' => 'tsp-bg-gold'];
        }
    } elseif ($statusName === 'approved') {
        return ['text' => 'स्वीकृत', 'class' => 'tsp-bg-green'];
    } elseif ($statusName === 'rejected') {
        return ['text' => 'अस्वीकृत', 'class' => 'tsp-bg-red'];
    } elseif ($statusName === 'disputed') {
        return ['text' => 'दस्तावेज सत्यापन', 'class' => 'tsp-bg-blue'];
    }
    return ['text' => $app['status_name'] ?: 'लंबित', 'class' => 'tsp-bg-gold'];
}

// Map database values directly with no faked data fallbacks
$displayTotalApps = $totalApps;
$displayTotalStudents = $totalStudents;
$displayScholarshipApps = $scholarshipApps;
$displayTotalAnnouncements = $totalAnnouncements;
$displayPratibhaApps = $pratibhaApps;

// Split pending status counts
$submittedCount = 0;
$pendingCount = 0;
$disputedCount = $statusCounts['correction'] ?? 0;
$approvedCount = $statusCounts['approved'] ?? 0;
$rejectedCount = $statusCounts['rejected'] ?? 0;

if (($statusCounts['pending'] ?? 0) > 0) {
    $pendingCount = (int) $statusCounts['pending'];
}

$chartTotal = $submittedCount + $pendingCount + $disputedCount + $approvedCount + $rejectedCount;
$pSubmitted = $chartTotal > 0 ? round(($submittedCount / $chartTotal) * 100, 1) : 0;
$pPending = $chartTotal > 0 ? round(($pendingCount / $chartTotal) * 100, 1) : 0;
$pDisputed = $chartTotal > 0 ? round(($disputedCount / $chartTotal) * 100, 1) : 0;
$pApproved = $chartTotal > 0 ? round(($approvedCount / $chartTotal) * 100, 1) : 0;
$pRejected = $chartTotal > 0 ? round(($rejectedCount / $chartTotal) * 100, 1) : 0;

// Angles/stops calculation for conic gradient CSS Doughnut Chart representation
$deg1 = $pSubmitted;
$deg2 = $deg1 + $pPending;
$deg3 = $deg2 + $pDisputed;
$deg4 = $deg3 + $pApproved;

if ($chartTotal > 0) {
    $conicGradient = "conic-gradient(
        #10b981 0% {$deg1}%, 
        #f59e0b {$deg1}% {$deg2}%, 
        #3b82f6 {$deg2}% {$deg3}%, 
        #34d399 {$deg3}% {$deg4}%, 
        #ef4444 {$deg4}% 100%
    )";
} else {
    $conicGradient = "conic-gradient(#e2e8f0 0% 100%)";
}

// Map Application List
$displayApps = [];
if (!empty($recentApps)) {
    foreach ($recentApps as $app) {
        $appNum = "TSVS" . date('Y', strtotime($app['submitted_at'] ?? 'now')) . str_pad((string)$app['id'], 6, '0', STR_PAD_LEFT);
        $name = htmlspecialchars(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? ''));
        $type = ($app['type'] === 'scholarship') ? 'छात्रवृत्ति' : 'प्रतिभा सम्मान';
        $statusInfo = getHindiStatusInfo($app);
        $date = !empty($app['submitted_at']) ? date('d M Y', strtotime($app['submitted_at'])) : date('d M Y');
        
        $displayApps[] = [
            'id' => $app['id'],
            'num' => $appNum,
            'name' => $name,
            'type' => $type,
            'status' => $statusInfo['text'],
            'class' => $statusInfo['class'],
            'date' => $date
        ];
    }
}

// Calculate percentages for categories progress bars relative to maximum count
$maxCategoryVal = max($displayScholarshipApps, $displayPratibhaApps);
$wScholarship = $maxCategoryVal > 0 ? ($displayScholarshipApps / $maxCategoryVal) * 100 : 0;
$wPratibha = $maxCategoryVal > 0 ? ($displayPratibhaApps / $maxCategoryVal) * 100 : 0;
?>

<!-- Outer full-viewport shell -->
<div class="d-flex flex-column min-vh-100 bg-light" style="font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;">

    <?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

    <!-- Sidebar and Main Panel Workspace Container -->
    <div class="d-flex flex-grow-1 position-relative">

        <?php
        $activeSidebarLink = admin_path();
        require VIEW_PATH . '/layouts/admin-sidebar.php';
        ?>

        <!-- ── MAIN WORKSPACE CONTENT AREA ── -->
        <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
            <div class="container-fluid p-0">

                <!-- Greeting & Date Strip -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <h2 class="h3 fw-bold text-dark mb-1" style="font-family: 'Manrope', sans-serif;">स्वागत है, <?= htmlspecialchars($adminName) ?> 👋</h2>
                        <p class="text-secondary mb-0 small fw-semibold" style="font-size: 1.3rem;">यहाँ आप पोर्टल की सभी गतिविधियों का अवलोकन और प्रबंधन कर सकते हैं।</p>
                    </div>

                    <!-- Date & Time widget -->
                    <div class="card border-0 shadow-sm px-3 py-2 text-center" style="background-color: #fff5f5; border-radius: 12px; min-width: 200px;">
                        <div class="d-flex align-items-center gap-3 justify-content-center">
                            <i class="bi bi-calendar3-event fs-3 text-danger"></i>
                            <div class="text-start" style="line-height: 1.25;">
                                <div class="fw-bold text-danger" id="hindi-date" style="font-size: 1.25rem; font-family: 'Noto Sans Devanagari', sans-serif;"><?= $hindiDateStr ?></div>
                                <div class="text-secondary small fw-bold" id="hindi-time" style="font-size: 1.15rem;"><?= $timeStr ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- 5 Metrics Cards Row -->
                <div class="row g-3 mb-4">
                    <!-- Metrics Card 1 -->
                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="tsp-metric-card">
                            <div class="tsp-metric-icon-wrapper tsp-bg-red">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <div class="tsp-metric-content">
                                <div class="tsp-metric-title">कुल आवेदन</div>
                                <div class="tsp-metric-value mb-1"><?= number_format($displayTotalApps) ?></div>
                                <div class="tsp-metric-desc mt-2">सभी श्रेणियां</div>
                                <a href="<?= admin_path('applications') ?>" class="tsp-metric-action tsp-color-red">विवरण देखें <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Metrics Card 2 -->
                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="tsp-metric-card">
                            <div class="tsp-metric-icon-wrapper tsp-bg-blue">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="tsp-metric-content">
                                <div class="tsp-metric-title">पंजीकृत उपयोगकर्ता</div>
                                <div class="tsp-metric-value mb-1"><?= number_format($displayTotalStudents) ?></div>
                                <div class="tsp-metric-desc mt-2">सभी उपयोगकर्ता</div>
                                <a href="<?= admin_path('students') ?>" class="tsp-metric-action tsp-color-blue">विवरण देखें <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Metrics Card 3 -->
                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="tsp-metric-card">
                            <div class="tsp-metric-icon-wrapper tsp-bg-green">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div class="tsp-metric-content">
                                <div class="tsp-metric-title">छात्रवृत्ति आवेदन</div>
                                <div class="tsp-metric-value mb-1"><?= number_format($displayScholarshipApps) ?></div>
                                <div class="tsp-metric-desc mt-2">छात्रवृत्ति श्रेणी</div>
                                <a href="<?= admin_path('applications') ?>" class="tsp-metric-action tsp-color-green">विवरण देखें <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Metrics Card 4 -->
                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="tsp-metric-card">
                            <div class="tsp-metric-icon-wrapper tsp-bg-gold">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div class="tsp-metric-content">
                                <div class="tsp-metric-title">प्रतिभा सम्मान आवेदन</div>
                                <div class="tsp-metric-value mb-1"><?= number_format($displayPratibhaApps) ?></div>
                                <div class="tsp-metric-desc mt-2">प्रतिभा सम्मान श्रेणी</div>
                                <a href="<?= admin_path('applications') ?>" class="tsp-metric-action tsp-color-gold">विवरण देखें <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Metrics Card 5 -->
                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="tsp-metric-card">
                            <div class="tsp-metric-icon-wrapper tsp-bg-purple">
                                <i class="bi bi-megaphone-fill"></i>
                            </div>
                            <div class="tsp-metric-content">
                                <div class="tsp-metric-title">सक्रिय सूचनाएं</div>
                                <div class="tsp-metric-value mb-1"><?= number_format($displayTotalAnnouncements) ?></div>
                                <div class="tsp-metric-desc mt-2">कुल सक्रिय सूचनाएं</div>
                                <a href="<?= admin_path('announcements') ?>" class="tsp-metric-action tsp-color-purple">विवरण देखें <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (Auth::isSuperAdmin()): ?>
                <!-- Super Admin Quick Console -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-2 bg-success rounded-3 text-white">
                                <i class="bi bi-shield-lock-fill fs-3"></i>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold mb-0 font-heading text-white">सुपर एडमिन कंट्रोल कंसोल</h3>
                                <p class="text-light opacity-75 mb-0 small" style="font-size: 1.25rem;">सिस्टम-वाइड प्रबंधन सेटिंग्स, शैक्षणिक सत्र नियंत्रण और प्रतिनिधियों का प्रबंधन करें।</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="<?= admin_path('reps') ?>" class="btn btn-outline-light w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2" style="font-size: 1.25rem; border-color: rgba(255,255,255,0.2); transition: all 0.2s;">
                                    <i class="bi bi-people-fill text-success"></i> प्रतिनिधि प्रबंधन
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= admin_path('settings') ?>" class="btn btn-outline-light w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2" style="font-size: 1.25rem; border-color: rgba(255,255,255,0.2); transition: all 0.2s;">
                                    <i class="bi bi-gear-fill text-warning"></i> सिस्टम सेटिंग्स एवं सत्र
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= admin_path('announcements/create') ?>" class="btn btn-outline-light w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2" style="font-size: 1.25rem; border-color: rgba(255,255,255,0.2); transition: all 0.2s;">
                                    <i class="bi bi-megaphone-fill text-info"></i> नई सूचना जारी करें
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Middle Section: Recent Applications & Quick Actions -->
                <div class="row g-4 mb-4">
                    
                    <!-- Left: Recent Applications Table -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="h5 fw-bold text-dark mb-0 font-heading">हालिया आवेदन</h3>
                                    <a href="<?= admin_path('applications') ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold" style="font-size: 1.2rem; border-color: #fee2e2; color: #8b0000;">
                                        सभी देखें <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle admin-table" style="font-size: 1.3rem;">
                                        <thead>
                                            <tr>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3">आवेदन संख्या</th>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3">आवेदक का नाम</th>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3">श्रेणी</th>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3">स्थिति</th>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3">दिनांक</th>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3 text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top-0">
                                            <?php if (!empty($displayApps)): ?>
                                                <?php foreach ($displayApps as $app): ?>
                                                    <tr>
                                                        <td class="fw-bold text-dark py-3"><?= htmlspecialchars($app['num']) ?></td>
                                                        <td class="fw-semibold text-secondary py-3"><?= htmlspecialchars($app['name']) ?></td>
                                                        <td class="text-secondary py-3"><?= htmlspecialchars($app['type']) ?></td>
                                                        <td class="py-3">
                                                            <span class="badge rounded-pill px-3 py-2 fw-bold <?= $app['class'] ?>" style="font-size: 1.15rem; display: inline-block;">
                                                                <?= htmlspecialchars($app['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-muted py-3"><?= htmlspecialchars($app['date']) ?></td>
                                                        <td class="text-center py-3">
                                                            <a href="<?= admin_path('applications/' . $app['id']) ?>" class="text-secondary hover-primary" aria-label="View Application Details" style="font-size: 1.4rem;">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4 fw-bold">कोई हालिया आवेदन उपलब्ध नहीं है।</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Actions 2x2 Grid -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h3 class="h5 fw-bold text-dark mb-4 font-heading">त्वरित कार्य (Quick Actions)</h3>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <a href="<?= admin_path('applications') ?>" class="tsp-quick-action-card">
                                            <i class="bi bi-file-earmark-text"></i>
                                            <span>आवेदन प्रबंधन</span>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="<?= admin_path('students') ?>" class="tsp-quick-action-card">
                                            <i class="bi bi-people"></i>
                                            <span>उपयोगकर्ता सूची</span>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="<?= admin_path('announcements') ?>" class="tsp-quick-action-card">
                                            <i class="bi bi-megaphone"></i>
                                            <span>सूचनाएं सूची</span>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="<?= admin_path('announcements/create') ?>" class="tsp-quick-action-card">
                                            <i class="bi bi-plus-circle"></i>
                                            <span>नई सूचना लिखें</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Section: Doughnut, Category Progress & Recent Activities -->
                <div class="row g-4">

                    <!-- Left: Doughnut Chart -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h5 fw-bold text-dark mb-4 font-heading">आवेदन स्थिति (श्रेणी अनुसार)</h3>
                                <div class="tsp-doughnut-container flex-grow-1">
                                    <div class="tsp-doughnut-circle" style="background: <?= $conicGradient ?>;">
                                        <div class="tsp-doughnut-inner"></div>
                                    </div>
                                    <div class="tsp-legend-list">
                                        <div class="tsp-legend-item">
                                            <span><span class="tsp-legend-color" style="background: #10b981;"></span>सबमिटिट</span>
                                            <span class="fw-bold text-secondary"><?= number_format($submittedCount) ?> (<?= $pSubmitted ?>%)</span>
                                        </div>
                                        <div class="tsp-legend-item">
                                            <span><span class="tsp-legend-color" style="background: #f59e0b;"></span>जांचधीन</span>
                                            <span class="fw-bold text-secondary"><?= number_format($pendingCount) ?> (<?= $pPending ?>%)</span>
                                        </div>
                                        <div class="tsp-legend-item">
                                            <span><span class="tsp-legend-color" style="background: #3b82f6;"></span>दस्तावेज सत्यापन</span>
                                            <span class="fw-bold text-secondary"><?= number_format($disputedCount) ?> (<?= $pDisputed ?>%)</span>
                                        </div>
                                        <div class="tsp-legend-item">
                                            <span><span class="tsp-legend-color" style="background: #34d399;"></span>स्वीकृत</span>
                                            <span class="fw-bold text-secondary"><?= number_format($approvedCount) ?> (<?= $pApproved ?>%)</span>
                                        </div>
                                        <div class="tsp-legend-item">
                                            <span><span class="tsp-legend-color" style="background: #ef4444;"></span>अस्वीकृत</span>
                                            <span class="fw-bold text-secondary"><?= number_format($rejectedCount) ?> (<?= $pRejected ?>%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Middle: Category Bars -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h3 class="h5 fw-bold text-dark mb-4 font-heading">श्रेणी अनुसार आवेदन</h3>
                                <div class="pt-2">
                                    <!-- Progress Item 1 -->
                                    <div class="tsp-cat-progress-item">
                                        <div class="tsp-cat-progress-header">
                                            <span>छात्रवृत्ति</span>
                                            <span class="text-secondary font-heading"><?= number_format($displayScholarshipApps) ?></span>
                                        </div>
                                        <div class="tsp-cat-progress-bar-wrapper">
                                            <div class="tsp-cat-progress-bar" style="width: <?= $wScholarship ?>%;"></div>
                                        </div>
                                    </div>
                                    <!-- Progress Item 2 -->
                                    <div class="tsp-cat-progress-item">
                                        <div class="tsp-cat-progress-header">
                                            <span>प्रतिभा सम्मान</span>
                                            <span class="text-secondary font-heading"><?= number_format($displayPratibhaApps) ?></span>
                                        </div>
                                        <div class="tsp-cat-progress-bar-wrapper">
                                            <div class="tsp-cat-progress-bar" style="width: <?= $wPratibha ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Dynamic Activities Feed -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h5 fw-bold text-dark mb-4 font-heading">महत्वपूर्ण गतिविधियां</h3>
                                <div class="tsp-activity-list-container flex-grow-1">
                                    <?php if (!empty($activities)): ?>
                                        <div class="tsp-activity-list">
                                            <?php foreach ($activities as $act): 
                                                $icon = 'bi-clock-fill';
                                                $bgClass = 'tsp-bg-blue';
                                                $txtClass = 'tsp-color-blue';
                                                
                                                $type = $act['type'] ?? '';
                                                if ($type === 'application') {
                                                    $icon = 'bi-file-earmark-text-fill';
                                                    $bgClass = 'tsp-bg-red';
                                                    $txtClass = 'tsp-color-red';
                                                } elseif ($type === 'student') {
                                                    $icon = 'bi-person-fill';
                                                    $bgClass = 'tsp-bg-blue';
                                                    $txtClass = 'tsp-color-blue';
                                                } elseif ($type === 'announcement') {
                                                    $icon = 'bi-megaphone-fill';
                                                    $bgClass = 'tsp-bg-purple';
                                                    $txtClass = 'tsp-color-purple';
                                                }
                                            ?>
                                                <div class="tsp-activity-item">
                                                    <div class="tsp-activity-icon <?= $bgClass ?> <?= $txtClass ?>">
                                                        <i class="bi <?= $icon ?>"></i>
                                                    </div>
                                                    <div class="tsp-activity-content">
                                                        <p class="tsp-activity-text text-secondary mb-1" style="font-size: 1.2rem;"><?= htmlspecialchars($act['title']) ?></p>
                                                        <span class="tsp-activity-time" style="font-size: 1.15rem; color: #94a3b8;"><?= htmlspecialchars($act['time']) ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-5 my-auto fw-bold" style="font-size: 1.25rem;">कोई हालिया गतिविधि नहीं।</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>

</div>

<!-- Sidebar toggle (shared partial) -->
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

<!-- Dynamic date/time updater for admin dashboard -->
<script>
(function () {
    'use strict';
    const dateEl = document.getElementById('hindi-date');
    const timeEl = document.getElementById('hindi-time');
    if (!dateEl && !timeEl) return;

    const hindiMonths = ['जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितंबर','अक्टूबर','नवंबर','दिसंबर'];
    const hindiDays   = ['रविवार','सोमवार','मंगलवार','बुधवार','गुरुवार','शुक्रवार','शनिवार'];

    function updateDateTime() {
        const now  = new Date();
        const day  = now.getDate().toString().padStart(2, '0');
        const dateStr = `${day} ${hindiMonths[now.getMonth()]} ${now.getFullYear()}, ${hindiDays[now.getDay()]}`;
        let h = now.getHours();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const timeStr = `${h.toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')} ${ampm}`;
        if (dateEl) dateEl.textContent = dateStr;
        if (timeEl) timeEl.textContent = timeStr;
    }

    updateDateTime();
    setInterval(updateDateTime, 15000);
})();
</script>

</body>
</html>


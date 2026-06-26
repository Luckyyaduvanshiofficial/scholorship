<?php
declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$app = $application ?? [];

// Define status translate and badge helper
$statusBadgeClass = function(string $statusName): string {
    return match($statusName) {
        'Approved' => 'bg-success-subtle text-success border border-success',
        'Rejected' => 'bg-danger-subtle text-danger border border-danger',
        'Disputed' => 'bg-warning-subtle text-warning-emphasis border border-warning',
        default    => 'bg-secondary-subtle text-secondary border border-secondary',
    };
};

$statusTranslate = function(string $statusName): string {
    return match($statusName) {
        'Approved' => 'स्वीकृत (Approved)',
        'Rejected' => 'अस्वीकृत (Rejected)',
        'Disputed' => 'त्रुटि/विवाद (Action Required)',
        default    => 'लंबित (Pending)',
    };
};

// Find Photo and Signature document paths for printing
$photoUrl = '';
$signatureUrl = '';
if (!empty($app['documents'])) {
    foreach ($app['documents'] as $doc) {
        if (($doc['document_type'] ?? '') === 'Photo') {
            $photoUrl = '/uploads/applications/' . $app['id'] . '/' . $doc['stored_name'];
        }
        if (($doc['document_type'] ?? '') === 'Signature') {
            $signatureUrl = '/uploads/applications/' . $app['id'] . '/' . $doc['stored_name'];
        }
    }
}

// Calculate timeline progress width and step classes
$timelineProgress = 0;
$step1Class = 'completed';
$step2Class = '';
$step3Class = '';
$step4Class = '';

$step1Icon = 'bi-check-circle-fill';
$step2Icon = 'bi-circle';
$step3Icon = 'bi-circle';
$step4Icon = 'bi-circle';

switch ($app['status_name'] ?? '') {
    case 'Approved':
        $timelineProgress = 100;
        $step2Class = 'completed';
        $step3Class = 'completed';
        $step4Class = 'completed status-approved';
        $step2Icon = 'bi-check-circle-fill';
        $step3Icon = 'bi-check-circle-fill';
        $step4Icon = 'bi-check-circle-fill';
        break;
    case 'Rejected':
        $timelineProgress = 100;
        $step2Class = 'completed';
        $step3Class = 'completed';
        $step4Class = 'completed status-rejected';
        $step2Icon = 'bi-check-circle-fill';
        $step3Icon = 'bi-check-circle-fill';
        $step4Icon = 'bi-x-circle-fill';
        break;
    case 'Disputed':
        $timelineProgress = 33;
        $step2Class = 'active disputed';
        $step2Icon = 'bi-exclamation-triangle-fill text-warning';
        break;
    case 'Pending':
    default:
        $timelineProgress = 33;
        $step2Class = 'active';
        $step2Icon = 'bi-hourglass-split text-primary';
        break;
}

// Organize documents by type
$documentsByType = [];
if (!empty($app['documents'])) {
    foreach ($app['documents'] as $doc) {
        $documentsByType[$doc['document_type']] = $doc;
    }
}

$requiredDocumentTypes = (($app['type'] ?? '') === 'scholarship')
    ? ['Photo', 'Signature', 'Marksheet', 'Passbook']
    : ['Photo', 'Signature', 'Marksheet', 'Certificate'];

$docLabels = [
    'Photo' => 'पासपोर्ट फोटो / Passport Photo',
    'Signature' => 'हस्ताक्षर / Student Signature',
    'Marksheet' => 'अंकतालिका / Marksheet',
    'Passbook' => 'बैंक पासबुक / Bank Passbook',
    'Certificate' => 'योग्यता प्रमाणपत्र / Certificate'
];

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<style>
/* Portal Theme Variables & View Styles */
:root {
    --brand-primary: #8B0000;
    --brand-secondary: #c0271f;
    --bg-light: #f8fafc;
    --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
}

.tsp-dash-container {
    background-color: var(--bg-light);
}

.tsp-view-card {
    border: none;
    border-radius: 1.25rem;
    box-shadow: var(--card-shadow);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background: #fff;
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.tsp-view-card:hover {
    box-shadow: var(--card-shadow-hover);
}

.tsp-card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.tsp-card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    font-family: 'Outfit', 'Inter', sans-serif;
}

.tsp-card-body {
    padding: 1.5rem;
}

/* Info Grid Layout */
.tsp-detail-item {
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    height: 100%;
}

.tsp-detail-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    display: block;
    margin-bottom: 0.25rem;
}

.tsp-detail-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
}

/* Avatar container */
.tsp-avatar-container {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    background: #f1f5f9;
    border: 3px solid var(--brand-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--card-shadow);
}

.tsp-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Timeline Tracker CSS */
.tsp-timeline {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 2rem 0 1rem 0;
    padding: 0 1.5rem;
}

.tsp-timeline-line-container {
    position: absolute;
    top: 22px;
    left: calc(1.5rem + 1.375rem);
    right: calc(1.5rem + 1.375rem);
    height: 4px;
    background: #e2e8f0;
    z-index: 1;
}

.tsp-timeline-progress {
    height: 100%;
    background: var(--brand-secondary);
    width: 0;
    transition: width 0.4s ease;
}

.tsp-timeline-step {
    position: relative;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    padding: 0 0.75rem;
}

.tsp-timeline-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 50%;
    background: #f1f5f9;
    border: 3px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #94a3b8;
    transition: all 0.3s ease;
}

.tsp-timeline-step.active .tsp-timeline-icon {
    background: #fff;
    border-color: #f59e0b;
    color: #f59e0b;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15);
}

.tsp-timeline-step.completed .tsp-timeline-icon {
    background: var(--brand-secondary);
    border-color: var(--brand-secondary);
    color: #fff;
}

.tsp-timeline-step.status-approved .tsp-timeline-icon {
    background: #10b981;
    border-color: #10b981;
    color: #fff;
}

.tsp-timeline-step.status-rejected .tsp-timeline-icon {
    background: #ef4444;
    border-color: #ef4444;
    color: #fff;
}

.tsp-timeline-text {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    text-align: center;
}

.tsp-timeline-step.active .tsp-timeline-text,
.tsp-timeline-step.completed .tsp-timeline-text {
    color: #0f172a;
}

/* Document Cards */
.tsp-doc-slot {
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    background: #fff;
    padding: 1rem;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

.tsp-doc-slot.verified {
    border-color: #10b981;
    background: #f0fdf4;
}

.tsp-doc-slot.rejected {
    border-color: #ef4444;
    background: #fef2f2;
}

.tsp-doc-slot.pending {
    border-color: #f59e0b;
    background: #fffbeb;
}

.tsp-doc-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.tsp-doc-preview {
    height: 120px;
    border-radius: 0.5rem;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin: 0.5rem 0;
}

.tsp-doc-preview img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}

.tsp-doc-status {
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f1f5f9;
}

.hover-scale {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.hover-scale:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <?php
    $activeLink = 'applications';
    require VIEW_PATH . '/layouts/student-sidebar.php';
    ?>

    <!-- Main Content Area -->
    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">
            
            <!-- Breadcrumbs and Action Header -->
            <div class="mb-4">
                <a href="/applications" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>आवेदनों की सूची पर वापस जाएं / Back to Applications</span>
                </a>
            </div>

            <!-- Page Title Card -->
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 1.25rem; border-left: 6px solid var(--brand-primary) !important;">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center bg-danger-subtle rounded-circle shadow-sm" style="width: 55px; height: 55px;">
                            <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                <i class="bi bi-mortarboard-fill fs-3 text-danger"></i>
                            <?php else: ?>
                                <i class="bi bi-trophy-fill fs-3 text-danger"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h2 class="fs-4 mb-1 fw-bold text-dark">
                                <?= Helpers::esc($app['app_type_name'] ?? ucfirst($app['type'] ?? '')) ?>
                            </h2>
                            <div class="d-flex flex-wrap gap-3 align-items-center text-muted small">
                                <span>आवेदन संदर्भ संख्या: <strong class="text-dark">TSVS-<?= date('Y', strtotime($app['created_at'] ?? 'now')) ?>-<?= str_pad((string) ($app['id'] ?? 0), 6, '0', STR_PAD_LEFT) ?></strong></span>
                                <span>&middot;</span>
                                <span>शैक्षणिक सत्र: <strong class="text-dark"><?= Helpers::esc($app['session_name'] ?? 'N/A') ?></strong></span>
                                <?php if (!empty($app['submitted_at'])): ?>
                                    <span>&middot;</span>
                                    <span>जमा तिथि: <strong class="text-dark"><?= date('d M Y, h:i A', strtotime($app['submitted_at'])) ?></strong></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 align-items-center">
                        <?php if (in_array($app['status_name'] ?? '', ['Pending', 'Disputed'], true)): ?>
                            <a href="/applications/<?= (int) $app['id'] ?>/edit" class="btn btn-warning rounded-pill d-inline-flex align-items-center gap-2 shadow-sm px-3.5 py-2 fw-semibold hover-scale">
                                <i class="bi bi-pencil-fill"></i>
                                <span>संशोधन / Edit</span>
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-dark rounded-pill d-inline-flex align-items-center gap-2 shadow-sm px-3.5 py-2 fw-semibold" onclick="window.print();">
                            <i class="bi bi-printer-fill"></i>
                            <span>प्रिंट / Print PDF</span>
                        </button>
                        <span class="badge py-2.5 px-3.5 rounded-pill fw-bold fs-6 <?= $statusBadgeClass($app['status_name'] ?? 'Pending') ?>">
                            <?= $statusTranslate($app['status_name'] ?? 'Pending') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Visual Application Tracker Timeline -->
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 1.25rem;">
                <h5 class="fw-bold text-dark mb-4 fs-6 d-flex align-items-center gap-2">
                    <i class="bi bi-activity text-danger"></i>
                    <span>आवेदन की स्थिति ट्रैकर / Application Status Tracker</span>
                </h5>
                
                <div class="tsp-timeline">
                    <div class="tsp-timeline-line-container">
                        <div class="tsp-timeline-progress" style="width: <?= $timelineProgress ?>%;"></div>
                    </div>
                    
                    <!-- Step 1: Submitted -->
                    <div class="tsp-timeline-step completed">
                        <div class="tsp-timeline-icon">
                            <i class="bi <?= $step1Icon ?>"></i>
                        </div>
                        <span class="tsp-timeline-text">आवेदन जमा किया<br><small class="text-muted">Submitted</small></span>
                    </div>
                    
                    <!-- Step 2: Verification -->
                    <div class="tsp-timeline-step <?= $step2Class ?>">
                        <div class="tsp-timeline-icon">
                            <i class="bi <?= $step2Icon ?>"></i>
                        </div>
                        <span class="tsp-timeline-text">दस्तावेज़ सत्यापन<br><small class="text-muted">Verification</small></span>
                    </div>
                    
                    <!-- Step 3: Recommendation -->
                    <div class="tsp-timeline-step <?= $step3Class ?>">
                        <div class="tsp-timeline-icon">
                            <i class="bi <?= $step3Icon ?>"></i>
                        </div>
                        <span class="tsp-timeline-text">प्रतिनिधि अनुशंसा<br><small class="text-muted">Review</small></span>
                    </div>
                    
                    <!-- Step 4: Final Status -->
                    <div class="tsp-timeline-step <?= $step4Class ?>">
                        <div class="tsp-timeline-icon">
                            <i class="bi <?= $step4Icon ?>"></i>
                        </div>
                        <span class="tsp-timeline-text">अंतिम निर्णय<br><small class="text-muted">Decision</small></span>
                    </div>
                </div>
            </div>

            <!-- Dispute Warning Notice -->
            <?php if (!empty($app['dispute_message'])): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4 p-4" style="border-radius: 1.25rem; border-left: 6px solid #d97706 !important;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1 fs-5">संशोधन की आवश्यकता (Action Required)</h5>
                            <p class="mb-0 text-dark small" style="line-height: 1.6;"><?= Helpers::esc($app['dispute_message']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Dispute Resolution Resubmission Form -->
            <?php if (($app['status_name'] ?? '') === 'Disputed'): ?>
                <div class="card border-0 shadow-sm border-start border-warning border-4 mb-4" style="border-radius: 1.25rem;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis rounded-circle" style="width: 36px; height: 36px;">
                                <i class="bi bi-arrow-counterclockwise fs-5"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0 text-dark">विवाद समाधान एवं दस्तावेज़ पुनः अपलोड / Resolve & Resubmit Documents</h4>
                        </div>
                        <p class="text-muted small mb-4" style="line-height: 1.6;">
                            कृपया त्रुटि निवारण के लिए आवश्यक संशोधित दस्तावेज़ यहाँ अपलोड करें। आप एक या अधिक दस्तावेज़ अपडेट कर सकते हैं।
                            <br><span class="fst-italic text-muted">Please upload the corrected documents below to resolve the dispute and resubmit your application.</span>
                        </p>

                        <form action="/applications/<?= (int) $app['id'] ?>/resubmit" method="POST" enctype="multipart/form-data">
                            <?= Csrf::field() ?>
                            
                            <div class="row g-4 mb-4">
                                <!-- Marksheet -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">संशोधित अंकतालिका अपलोड करें / Upload Corrected Marksheet (JPG/PNG/PDF)</label>
                                    <input type="file" name="marksheet" class="form-control border-2 py-2" style="border-radius: 0.5rem;" accept=".jpg,.jpeg,.png,.pdf">
                                </div>

                                <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                    <!-- Passbook -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">बैंक पासबुक अपलोड करें / Upload Corrected Bank Passbook (JPG/PNG/PDF)</label>
                                        <input type="file" name="passbook" class="form-control border-2 py-2" style="border-radius: 0.5rem;" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                <?php else: ?>
                                    <!-- Certificate -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">योग्यता प्रमाणपत्र अपलोड करें / Upload Corrected Certificate (JPG/PNG/PDF)</label>
                                        <input type="file" name="certificate" class="form-control border-2 py-2" style="border-radius: 0.5rem;" accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2.5 rounded-pill shadow-sm hover-scale">
                                <i class="bi bi-send-fill me-1"></i> दस्तावेज़ सबमिट करें / Submit Documents
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Main Layout Details Grid -->
            <div class="row g-4">
                <!-- Left: Application Details Form Data -->
                <div class="col-lg-8">
                    
                    <!-- 1. Profile / Personal Details Card -->
                    <div class="tsp-view-card">
                        <div class="tsp-card-header">
                            <i class="bi bi-person-fill text-danger fs-5"></i>
                            <h4 class="tsp-card-title">व्यक्तिगत विवरण / Personal Details</h4>
                        </div>
                        <div class="tsp-card-body">
                            
                            <!-- Profile Header (Initials or uploaded photo) -->
                            <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom">
                                <div class="tsp-avatar-container">
                                    <?php if (!empty($app['profile_photo'])): ?>
                                        <img src="<?= Helpers::esc($app['profile_photo']) ?>" alt="Profile Photo" class="tsp-avatar-img">
                                    <?php else: ?>
                                        <div class="tsp-avatar-initials">
                                            <i class="bi bi-person-fill fs-1 text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="fs-5 fw-bold text-dark mb-1"><?= Helpers::esc(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></h3>
                                    <p class="text-muted small mb-0">
                                        विद्यार्थी कोड / Student ID: <strong class="text-dark"><?= Helpers::esc($app['student_code'] ?? '-') ?></strong>
                                    </p>
                                    <span class="badge bg-light text-dark border mt-2 py-1 px-3.5 rounded-pill fs-7">
                                        लिंग / Gender: <?= Helpers::esc($app['gender'] ?? 'N/A') ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Detail Grid -->
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">पिता का नाम / Father's Name</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['father_name'] ?? '-') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">माता का नाम / Mother's Name</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['mother_name'] ?? '-') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">जन्म तिथि / Date of Birth</span>
                                        <span class="tsp-detail-value"><?= !empty($app['dob']) ? date('d M Y', strtotime($app['dob'])) : '-' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">मोबाइल नंबर / Mobile No.</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['mobile'] ?? '-') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">ईमेल आईडी / Email Address</span>
                                        <span class="tsp-detail-value text-break"><?= Helpers::esc($app['email'] ?? '-') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">स्थाई राज्य / State</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['state'] ?? 'Rajasthan') ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">स्थाई पता / Permanent Address</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['address'] ?? '-') ?>, <?= Helpers::esc($app['city'] ?? '') ?>, <?= Helpers::esc($app['district'] ?? '') ?> - <?= Helpers::esc($app['pincode'] ?? '') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Academic Background Card -->
                    <div class="tsp-view-card">
                        <div class="tsp-card-header">
                            <i class="bi bi-mortarboard-fill text-danger fs-5"></i>
                            <h4 class="tsp-card-title">शैक्षणिक योग्यता विवरण / Academic Credentials</h4>
                        </div>
                        <div class="tsp-card-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">उत्तीर्ण कक्षा व वर्ष / Passed Class & Year</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['class_year'] ?? '-') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">परीक्षा परिणाम प्रतिशत / Percentage</span>
                                        <span class="tsp-detail-value text-primary fw-bold"><?= Helpers::esc($app['percentage'] ?? '-') ?>%</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">प्राप्त अंक / Marks Obtained</span>
                                        <span class="tsp-detail-value"><?= !empty($app['marks_obtained']) ? Helpers::esc($app['marks_obtained']) : '-' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">कुल पूर्णांक / Maximum Marks</span>
                                        <span class="tsp-detail-value"><?= !empty($app['max_marks']) ? Helpers::esc($app['max_marks']) : '-' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">विद्यालय/महाविद्यालय / School/College</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['college_name'] ?? '-') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">बोर्ड / विश्वविद्यालय / Board or University</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['board_university'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Current Academic details & Career Goals -->
                    <div class="tsp-view-card">
                        <div class="tsp-card-header">
                            <i class="bi bi-compass-fill text-danger fs-5"></i>
                            <h4 class="tsp-card-title">वर्तमान अध्ययन एवं करियर लक्ष्य / Current Details</h4>
                        </div>
                        <div class="tsp-card-body">
                            <div class="row g-3">
                                <?php if (!empty($app['current_class'])): ?>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">वर्तमान अध्ययनरत कक्षा / Current Class</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['current_class']) ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($app['current_college'])): ?>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">वर्तमान संस्थान / Current Institution</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['current_college']) ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">भविष्य का लक्ष्य / Career Goal</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['career_goal'] ?? '-') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">परिवार का व्यवसाय / Family Occupation</span>
                                        <span class="tsp-detail-value"><?= Helpers::esc($app['family_occupation'] ?? '-') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">परिवार में कुल सदस्य / Family Members</span>
                                        <span class="tsp-detail-value"><?= isset($app['family_members_count']) ? (int) $app['family_members_count'] : '-' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tsp-detail-item">
                                        <span class="tsp-detail-label">कमाने वाले सदस्यों की संख्या / Earning Members</span>
                                        <span class="tsp-detail-value"><?= isset($app['earning_members_count']) ? (int) $app['earning_members_count'] : '-' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Condition card: Scholarship or Pratibha details -->
                    <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                        
                        <!-- Bank Details Card -->
                        <div class="tsp-view-card">
                            <div class="tsp-card-header">
                                <i class="bi bi-bank text-danger fs-5"></i>
                                <h4 class="tsp-card-title">बैंक खाता विवरण / Bank Details</h4>
                            </div>
                            <div class="tsp-card-body">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">खाता धारक का नाम / Account Holder Name</span>
                                            <span class="tsp-detail-value"><?= Helpers::esc($app['account_holder_name'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">बैंक का नाम / Bank Name</span>
                                            <span class="tsp-detail-value"><?= Helpers::esc($app['bank_name'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">खाता संख्या / Account Number</span>
                                            <span class="tsp-detail-value"><?= Helpers::esc($app['account_number'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">IFSC कोड / IFSC Code</span>
                                            <span class="tsp-detail-value"><?= Helpers::esc($app['ifsc_code'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">वार्षिक पारिवारिक आय / Annual Family Income</span>
                                            <span class="tsp-detail-value text-danger fw-bold"><?= !empty($app['family_income']) ? '₹ ' . number_format((float) $app['family_income'], 2) : '-' ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">पूर्व छात्रवृत्ति प्राप्त हुई है? / Prev Scholarship Received</span>
                                            <span class="tsp-detail-value"><?= Helpers::esc($app['prev_scholarship_received'] ?? 'नहीं') ?></span>
                                        </div>
                                    </div>

                                    <?php if (($app['prev_scholarship_received'] ?? '') === 'हाँ'): ?>
                                    <div class="col-12 mt-2">
                                        <div class="p-3 border rounded-3 bg-light">
                                            <span class="small text-muted d-block mb-2.5 fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">पिछले वर्षों में प्राप्त छात्रवृत्ति राशि / Previous Received Amounts:</span>
                                            <div class="row g-2">
                                                <div class="col-sm-4">
                                                    <div class="bg-white p-2.5 border rounded-2 text-center">
                                                        <span class="small text-muted d-block text-xs font-semibold mb-1">सत्र / Session 2023-24</span>
                                                        <span class="fw-bold text-dark"><?= !empty($app['scholarship_amt_2023_24']) ? '₹ ' . number_format((float)$app['scholarship_amt_2023_24'], 2) : '-' ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="bg-white p-2.5 border rounded-2 text-center">
                                                        <span class="small text-muted d-block text-xs font-semibold mb-1">सत्र / Session 2024-25</span>
                                                        <span class="fw-bold text-dark"><?= !empty($app['scholarship_amt_2024_25']) ? '₹ ' . number_format((float)$app['scholarship_amt_2024_25'], 2) : '-' ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="bg-white p-2.5 border rounded-2 text-center">
                                                        <span class="small text-muted d-block text-xs font-semibold mb-1">सत्र / Session 2025-26</span>
                                                        <span class="fw-bold text-dark"><?= !empty($app['scholarship_amt_2025_26']) ? '₹ ' . number_format((float)$app['scholarship_amt_2025_26'], 2) : '-' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        
                        <!-- Achievement Details Card -->
                        <div class="tsp-view-card">
                            <div class="tsp-card-header">
                                <i class="bi bi-trophy-fill text-danger fs-5"></i>
                                <h4 class="tsp-card-title">उपलब्धि विवरण / Achievement Details</h4>
                            </div>
                            <div class="tsp-card-body">
                                <div class="row g-3">
                                    <div class="col-sm-12">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">उपलब्धि का नाम / Achievement Title</span>
                                            <span class="tsp-detail-value"><?= Helpers::esc($app['achievement_title'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">श्रेणी / Category</span>
                                            <span class="tsp-detail-value"><?= Helpers::esc($app['achievement_category'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">स्तर / Level</span>
                                            <span class="tsp-detail-value"><?= Helpers::esc($app['achievement_level'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="tsp-detail-item">
                                            <span class="tsp-detail-label">रैंक / स्थान / Rank or Position</span>
                                            <span class="tsp-detail-value text-success fw-bold"><?= Helpers::esc($app['rank_position'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>

                </div>

                <!-- Right: Submitted Documents Checklist -->
                <div class="col-lg-4">
                    <div class="tsp-view-card">
                        <div class="tsp-card-header">
                            <i class="bi bi-file-earmark-check-fill text-danger fs-5"></i>
                            <h4 class="tsp-card-title">संलग्न दस्तावेज़ सूची / Attached Documents</h4>
                        </div>
                        <div class="tsp-card-body">
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($requiredDocumentTypes as $docType): ?>
                                    <?php 
                                    $hasDoc = isset($documentsByType[$docType]);
                                    $doc = $hasDoc ? $documentsByType[$docType] : null;
                                    $status = $doc['verification_status'] ?? 'pending';
                                    
                                    $statusClass = $hasDoc ? $status : 'missing';
                                    $statusLabel = match($status) {
                                        'verified' => 'सत्यापित / Verified',
                                        'rejected' => 'अस्वीकृत / Rejected',
                                        default    => 'लंबित / Pending Verification'
                                    };
                                    if (!$hasDoc) {
                                        $statusLabel = 'अपलोड नहीं है / Missing';
                                    }
                                    
                                    $filePath = $hasDoc ? '/uploads/applications/' . $app['id'] . '/' . $doc['stored_name'] : '#';
                                    $ext = $hasDoc ? strtolower(pathinfo($doc['stored_name'], PATHINFO_EXTENSION)) : '';
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                                    ?>
                                    
                                    <div class="tsp-doc-slot <?= $statusClass ?>">
                                        <div class="tsp-doc-title">
                                            <?php if ($hasDoc && $status === 'verified'): ?>
                                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <?php elseif ($hasDoc && $status === 'rejected'): ?>
                                                <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                            <?php elseif ($hasDoc): ?>
                                                <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-square-fill text-muted fs-5"></i>
                                            <?php endif; ?>
                                            <span><?= $docLabels[$docType] ?? $docType ?></span>
                                        </div>
                                        
                                        <div class="tsp-doc-preview">
                                            <?php if ($hasDoc): ?>
                                                <?php if ($isImage): ?>
                                                    <a href="<?= $filePath ?>" target="_blank" title="बड़ा देखने के लिए क्लिक करें / Click to view full image">
                                                        <img src="<?= $filePath ?>" alt="<?= Helpers::esc($docType) ?>">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= $filePath ?>" target="_blank" class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-danger py-3">
                                                        <i class="bi bi-file-earmark-pdf-fill fs-1"></i>
                                                        <span class="small mt-1 text-primary text-decoration-underline text-truncate" style="max-width: 180px;">
                                                            <?= Helpers::esc($doc['original_name'] ?? 'View PDF') ?>
                                                        </span>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small italic">दस्तावेज़ उपलब्ध नहीं है</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="tsp-doc-status">
                                            <span class="text-muted">स्थिति / Status:</span>
                                            <span class="fw-bold <?= $hasDoc ? ($status === 'verified' ? 'text-success' : ($status === 'rejected' ? 'text-danger' : 'text-warning')) : 'text-muted' ?>">
                                                <?= $statusLabel ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden Print Container: Only rendered during browser printing (triggered by window.print()) -->
            <div id="printableForm" class="d-none-screen">
                <!-- Print Header -->
                <div class="print-header">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj" class="print-logo" width="60" height="60">
                        <div class="text-center">
                            <h2 class="print-org-title" style="font-size: 2.1rem; font-weight: 800; text-decoration: underline; margin: 0;">तम्बोली समाज विकास संस्था, राजस्थान</h2>
                            <div class="print-reg-no" style="font-size: 1.25rem; font-weight: bold; text-align: center; margin-top: 2px;">रजि.नं. 411/2016-17</div>
                            <div class="print-office-address" style="font-size: 1.15rem; text-align: center; margin-top: 1px;">कार्यालय: 132, जनकपुरी-2, इमलीफाटक, जयपुर (राज.)-302005</div>
                            <div class="print-contact" style="font-size: 1.15rem; text-align: center; margin-top: 1px;">मो. 982971477, 9414728866 ई मेल : tambolisamaj@gmail.com</div>
                        </div>
                    </div>
                    <div class="print-form-title-underlined text-center font-weight-bold" style="font-size: 1.6rem; text-decoration: underline; margin-top: 12px;">
                        <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                            सामाजिक छात्रवृत्ति हेतु आवेदन - <?= date('Y', strtotime($app['created_at'] ?? 'now')) ?>
                        <?php else: ?>
                            प्रतिभा सम्मान हेतु आवेदन - <?= date('Y', strtotime($app['created_at'] ?? 'now')) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Profile Photo at top-right -->
                <div class="print-photo-box-top-right">
                    <?php if ($photoUrl): ?>
                        <img src="<?= Helpers::esc($photoUrl) ?>" alt="Photo">
                    <?php else: ?>
                        <div class="print-photo-placeholder">विद्यार्थी का<br>फोटो</div>
                    <?php endif; ?>
                </div>

                <!-- Form Fields styled like offline paper form with dotted lines -->
                <div class="print-form-fields">
                    <div class="print-field-row">
                        <div class="print-field-label">विद्यार्थी का नाम (Name) :</div>
                        <div class="print-field-value"><?= Helpers::esc(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></div>
                    </div>

                    <div class="print-field-row">
                        <div class="print-field-label">पिता / संरक्षक का नाम (Father's Name) :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['father_name'] ?? '-') ?></div>
                    </div>

                    <div class="print-field-row">
                        <div class="print-field-label">माता का नाम (Mother's Name) :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['mother_name'] ?? '-') ?></div>
                    </div>

                    <div class="print-field-row">
                        <div class="print-field-label">वर्तमान स्थायी पता (Address) :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['address'] ?? '-') ?>, <?= Helpers::esc($app['city'] ?? '') ?>, <?= Helpers::esc($app['district'] ?? '') ?> - <?= Helpers::esc($app['pincode'] ?? '') ?></div>
                    </div>

                    <div class="print-field-row">
                        <div class="print-field-label">मोबाइल नंबर (Mobile) :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['mobile'] ?? '-') ?></div>
                        <div class="print-field-label" style="padding-left: 2rem;">लिंग (Gender) :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['gender'] ?? '-') ?></div>
                        <div class="print-field-label" style="padding-left: 2rem;">जन्म तिथि (DOB) :</div>
                        <div class="print-field-value"><?= !empty($app['dob']) ? date('d/m/Y', strtotime($app['dob'])) : '-' ?></div>
                    </div>

                    <!-- Only for Scholarship: Family income / details -->
                    <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                        <div class="print-field-row">
                            <div class="print-field-label">परिवार का व्यवसाय/आजीविका का साधन :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['family_occupation'] ?? '-') ?></div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">परिवार में कुल सदस्य :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['family_members_count'] ?? '-') ?></div>
                            <div class="print-field-label" style="padding-left: 2rem;">कमाने वाले सदस्यों की संख्या :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['earning_members_count'] ?? '-') ?></div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">वर्तमान में अध्ययनरत कक्षा (Current Class) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['current_class'] ?? '-') ?></div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">वर्तमान में अध्ययनरत विद्यालय/महाविद्यालय :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['current_college'] ?? '-') ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="print-field-row">
                        <div class="print-field-label">उत्तीर्ण कक्षा व वर्ष (Class & Year Passed) :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['class_year'] ?? '-') ?></div>
                        <div class="print-field-label" style="padding-left: 2rem;">परीक्षा परिणाम प्रतिशत (Percentage) :</div>
                        <div class="print-field-value" style="font-weight: bold;"><?= Helpers::esc($app['percentage'] ?? '-') ?>%</div>
                    </div>

                    <div class="print-field-row">
                        <div class="print-field-label">प्राप्त / कुल अंक (Marks Obtained/Max) :</div>
                        <div class="print-field-value"><?= (!empty($app['marks_obtained']) && !empty($app['max_marks'])) ? $app['marks_obtained'] . ' / ' . $app['max_marks'] : '-' ?></div>
                    </div>

                    <div class="print-field-row">
                        <div class="print-field-label">अध्ययनरत विद्यालय/महाविद्यालय का नाम (Institution) :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['college_name'] ?? '-') ?></div>
                    </div>

                    <div class="print-field-row">
                        <div class="print-field-label">बोर्ड / विश्वविद्यालय (Board/University) :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['board_university'] ?? '-') ?></div>
                    </div>

                    <!-- Specific details for scholarship -->
                    <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                        <div class="print-field-row">
                            <div class="print-field-label">संस्था से पिछले वर्षों में छात्रवृत्ति प्राप्त हुई है :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['prev_scholarship_received'] ?? '-') ?></div>
                        </div>
                        <?php if (($app['prev_scholarship_received'] ?? '') === 'हाँ'): ?>
                        <div class="print-field-row">
                            <div class="print-field-label">यदि हाँ तो वर्ष 2023-24 में राशि:</div>
                            <div class="print-field-value"><?= !empty($app['scholarship_amt_2023_24']) ? '₹ ' . Helpers::esc($app['scholarship_amt_2023_24']) : '-' ?></div>
                            <div class="print-field-label" style="padding-left: 1rem;">2024-25 में राशि:</div>
                            <div class="print-field-value"><?= !empty($app['scholarship_amt_2024_25']) ? '₹ ' . Helpers::esc($app['scholarship_amt_2024_25']) : '-' ?></div>
                            <div class="print-field-label" style="padding-left: 1rem;">2025-26 में राशि:</div>
                            <div class="print-field-value"><?= !empty($app['scholarship_amt_2025_26']) ? '₹ ' . Helpers::esc($app['scholarship_amt_2025_26']) : '-' ?></div>
                        </div>
                        <?php endif; ?>

                        <div class="print-field-row">
                            <div class="print-field-label">बैंक खाता संख्या (Account No.) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['account_number'] ?? '-') ?></div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">खाता धारक का नाम (Holder Name) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['account_holder_name'] ?? '-') ?></div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">बैंक का नाम (Bank Name) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['bank_name'] ?? '-') ?></div>
                            <div class="print-field-label" style="padding-left: 2rem;">IFSC कोड :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['ifsc_code'] ?? '-') ?></div>
                        </div>
                    <?php else: ?>
                        <!-- Specific details for Pratibha -->
                        <div class="print-field-row">
                            <div class="print-field-label">उपलब्धि का नाम (Achievement Title) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['achievement_title'] ?? '-') ?></div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">रैंक / स्थान (Rank/Position) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['rank_position'] ?? '-') ?></div>
                            <div class="print-field-label" style="padding-left: 2rem;">श्रेणी (Category) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['achievement_category'] ?? '-') ?></div>
                            <div class="print-field-label" style="padding-left: 2rem;">स्तर (Level) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['achievement_level'] ?? '-') ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="print-field-row">
                        <div class="print-field-label">भविष्य में आप क्या बनना चाहते हैं :</div>
                        <div class="print-field-value"><?= Helpers::esc($app['career_goal'] ?? '-') ?></div>
                    </div>
                </div>

                <div class="mt-4 mb-4">
                    <span class="print-note" style="font-size: 1.2rem; font-weight: bold; border: 1px solid #000; padding: 6px; display: inline-block;">
                        <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                            नोट - मार्कशीट एवं बैंक पासबुक की छायाप्रति संलग्न करना आवश्यक है।
                        <?php else: ?>
                            नोट - मार्कशीट एवं योग्यता प्रमाणपत्र की छायाप्रति संलग्न करना आवश्यक है।
                        <?php endif; ?>
                    </span>
                </div>

                <!-- Signature box and declarations -->
                <div class="print-footer-declaration border-top pt-3" style="margin-top: 30px;">
                    <div class="d-flex justify-content-between align-items-end mt-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="print-signature-box mb-2" style="border: none;"></div>
                            <div style="width: 200px; border-top: 1px solid #000; text-align: center;" class="pt-2 small fw-bold">विद्यार्थी के हस्ताक्षर</div>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <div class="print-signature-box mb-2">
                                <?php if ($signatureUrl): ?>
                                    <img src="<?= Helpers::esc($signatureUrl) ?>" alt="Signature" style="max-height: 50px; object-fit: contain;">
                                <?php endif; ?>
                            </div>
                            <div style="width: 200px; border-top: 1px solid #000; text-align: center;" class="pt-2 small fw-bold">पिता / संरक्षक के हस्ताक्षर</div>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 2px dashed #000; margin: 40px 0 20px 0;">

                <!-- Representatives Recommendation Section -->
                <div class="print-recommendation-box mt-3" style="font-size: 1.25rem; line-height: 1.6;">
                    <p class="mb-4">
                        विद्यार्थी की पढ़ाई अविरल चलती रहे इसके लिये संस्था द्वारा <?= (($app['type'] ?? '') === 'scholarship') ? 'सामाजिक छात्रवृत्ति' : 'प्रतिभा सम्मान' ?> आवश्यक है।
                    </p>
                    <p class="mb-5">
                        <?= (($app['type'] ?? '') === 'scholarship') ? 'छात्रवृत्ति' : 'सम्मान' ?> की अनुशंसा की जाती है।
                    </p>
                    <div class="d-flex justify-content-end mt-5">
                        <div style="width: 350px; border-top: 1px solid #000; text-align: center;" class="pt-2 small fw-bold">संस्था के छात्रवृत्ति प्रतिनिधियों के हस्ताक्षर मय नाम</div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Responsive Sidebar toggle control -->
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

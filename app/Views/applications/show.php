<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$app = $application ?? [];
$statusBadgeClass = function(string $statusName): string {
    return match($statusName) {
        'Approved' => 'bg-success-subtle text-success border border-success-subtle',
        'Rejected' => 'bg-danger-subtle text-danger border border-danger-subtle',
        'Disputed' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        default    => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
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

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

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

            <!-- Page Title and Status -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="tsp-dash-welcome-title fs-3 mb-1">
                        <?= Helpers::esc($app['app_type_name'] ?? ucfirst($app['type'] ?? '')) ?>
                    </h2>
                    <p class="text-muted small mb-0">
                        आवेदन आईडी: <strong class="text-dark">TSVS-<?= date('Y') ?>-<?= str_pad((string) ($app['id'] ?? 0), 6, '0', STR_PAD_LEFT) ?></strong>
                        &middot; सत्र: <strong class="text-dark"><?= Helpers::esc($app['session_name'] ?? 'N/A') ?></strong>
                        &middot; जमा तिथि: <strong class="text-dark"><?= !empty($app['submitted_at']) ? date('d M Y, h:i A', strtotime($app['submitted_at'])) : 'N/A' ?></strong>
                    </p>
                </div>
                
                <div class="d-flex gap-2 align-items-center">
                    <?php if (in_array($app['status_name'] ?? '', ['Pending', 'Disputed'], true)): ?>
                        <a href="/applications/<?= (int) $app['id'] ?>/edit" class="btn btn-warning btn-sm rounded-pill d-inline-flex align-items-center gap-1 shadow-sm px-3 py-2 fw-semibold">
                            <i class="bi bi-pencil-fill"></i>
                            <span>संशोधन / Edit Application</span>
                        </a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-dark btn-sm rounded-pill d-inline-flex align-items-center gap-1 shadow-sm px-3 py-2 fw-semibold" onclick="window.print();">
                        <i class="bi bi-printer-fill"></i>
                        <span>प्रिंट / PDF सेव करें</span>
                    </button>
                    <span class="badge py-2.5 px-3 rounded-pill fw-semibold fs-6 <?= $statusBadgeClass($app['status_name'] ?? 'Pending') ?>">
                        <?= $statusTranslate($app['status_name'] ?? 'Pending') ?>
                    </span>
                </div>
            </div>

            <!-- Dispute Warning Notice -->
            <?php if (!empty($app['dispute_message'])): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4 p-4" style="border-radius: 1rem; border-left: 5px solid #d97706 !important;">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">संशोधन की आवश्यकता (Action Required)</h5>
                            <p class="mb-0 text-muted-800 small"><?= Helpers::esc($app['dispute_message']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Dispute Resolution Resubmission Form -->
            <?php if (($app['status_name'] ?? '') === 'Disputed'): ?>
                <div class="card border-0 shadow-sm border-start border-warning border-4 mb-4" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis rounded-circle" style="width: 36px; height: 36px;">
                                <i class="bi bi-arrow-counterclockwise fs-5"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0 text-dark">विवाद समाधान एवं पुनः प्रस्तुति / Resolve & Resubmit</h4>
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

                            <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2.5 rounded-pill shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> दस्तावेज़ सबमिट करें / Submit Documents
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Application Details Grid -->
            <div class="row g-4">
                <!-- Left: Application Details Form Data -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                        <div class="card-body p-4 p-md-5">
                            
                            <!-- Student Section -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 30px; height: 30px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <h4 class="h6 fw-bold mb-0 text-dark">विद्यार्थी का विवरण / Student Details</h4>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <span class="small text-muted d-block mb-1">विद्यार्थी का नाम (Name)</span>
                                    <span class="fw-semibold text-dark"><?= Helpers::esc(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="small text-muted d-block mb-1">विद्यार्थी कोड (Student Code)</span>
                                    <span class="fw-semibold text-dark"><?= Helpers::esc($app['student_code'] ?? '-') ?></span>
                                </div>
                                <?php if (!empty($app['father_name'])): ?>
                                <div class="col-sm-6">
                                    <span class="small text-muted d-block mb-1">पिता का नाम (Father's Name)</span>
                                    <span class="fw-semibold text-dark"><?= Helpers::esc($app['father_name']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($app['mother_name'])): ?>
                                <div class="col-sm-6">
                                    <span class="small text-muted d-block mb-1">माता का नाम (Mother's Name)</span>
                                    <span class="fw-semibold text-dark"><?= Helpers::esc($app['mother_name']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($app['address'])): ?>
                                <div class="col-12">
                                    <span class="small text-muted d-block mb-1">स्थाई पता (Address)</span>
                                    <span class="fw-semibold text-dark"><?= Helpers::esc($app['address']) ?>, <?= Helpers::esc($app['city'] ?? '') ?>, <?= Helpers::esc($app['district'] ?? '') ?> - <?= Helpers::esc($app['pincode'] ?? '') ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <hr class="my-4" style="border-color: #e2e8f0;">

                            <!-- Academic Section (for Scholarship) -->
                            <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 30px; height: 30px;">
                                        <i class="bi bi-mortarboard"></i>
                                    </div>
                                    <h4 class="h6 fw-bold mb-0 text-dark">शैक्षणिक योग्यता विवरण / Academic Details</h4>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">कक्षा व वर्ष (Class & Year)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['class_year'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">प्रतिशत (Percentage)</span>
                                        <span class="fw-bold text-primary"><?= Helpers::esc($app['percentage'] ?? '-') ?>%</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">विद्यालय/महाविद्यालय (College/School)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['college_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">बोर्ड / विश्वविद्यालय (Board/University)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['board_university'] ?? '-') ?></span>
                                    </div>
                                </div>

                                <hr class="my-4" style="border-color: #e2e8f0;">

                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 30px; height: 30px;">
                                        <i class="bi bi-bank"></i>
                                    </div>
                                    <h4 class="h6 fw-bold mb-0 text-dark">बैंक खाता विवरण / Bank Details</h4>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">बैंक का नाम (Bank Name)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['bank_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">खाता संख्या (Account Number)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['account_number'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">IFSC कोड (IFSC Code)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['ifsc_code'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">वार्षिक पारिवारिक आय (Annual Family Income)</span>
                                        <span class="fw-semibold text-dark"><?= !empty($app['family_income']) ? '₹ ' . number_format((float) $app['family_income'], 2) : '-' ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 30px; height: 30px;">
                                        <i class="bi bi-trophy"></i>
                                    </div>
                                    <h4 class="h6 fw-bold mb-0 text-dark">उपलब्धि विवरण / Achievement Details</h4>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-8">
                                        <span class="small text-muted d-block mb-1">उपलब्धि का नाम (Achievement Title)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['achievement_title'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="small text-muted d-block mb-1">रैंक / स्थान (Rank / Position)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['rank_position'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">श्रेणी (Category)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['achievement_category'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-muted d-block mb-1">स्तर (Level)</span>
                                        <span class="fw-semibold text-dark"><?= Helpers::esc($app['achievement_level'] ?? '-') ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <!-- Right: Submitted Documents Checklist -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                        <div class="card-body p-4">
                            <h4 class="h6 fw-bold text-dark mb-3">अपलोड किए गए दस्तावेज़ / Uploaded Documents</h4>
                            
                            <?php if (empty($app['documents'])): ?>
                                <p class="text-muted small mb-0">कोई दस्तावेज़ संलग्न नहीं है।</p>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($app['documents'] as $document): ?>
                                        <?php 
                                        $filePath = '/uploads/applications/' . $app['id'] . '/' . $document['stored_name'];
                                        $ext = strtolower(pathinfo($document['stored_name'], PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                                        ?>
                                        <div class="p-3 border rounded shadow-sm" style="background: #f8fafc; border-radius: 0.75rem !important;">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="bi bi-file-earmark-check fs-5 text-success"></i>
                                                <span class="fw-bold text-dark small">
                                                    <?= Helpers::esc($document['document_type'] ?? 'Document') ?>
                                                </span>
                                            </div>
                                            
                                            <!-- Image/PDF Preview -->
                                            <div class="mb-3 text-center bg-light border rounded overflow-hidden d-flex align-items-center justify-content-center" style="height: 140px;">
                                                <?php if ($isImage): ?>
                                                    <a href="<?= $filePath ?>" target="_blank" title="Click to view full image">
                                                        <img src="<?= $filePath ?>" alt="<?= Helpers::esc($document['document_type']) ?>" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= $filePath ?>" target="_blank" class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-danger py-3">
                                                        <i class="bi bi-file-earmark-pdf fs-1"></i>
                                                        <span class="small mt-1 text-primary text-decoration-underline text-truncate" style="max-width: 180px;">
                                                            <?= Helpers::esc($document['original_name'] ?? 'View PDF') ?>
                                                        </span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                                <span class="small text-muted">स्थिति / Status:</span>
                                                <span class="badge rounded-pill fw-semibold bg-light text-dark border small">
                                                    <?= Helpers::esc($document['verification_status'] ?? 'pending') ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

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
                            <div class="print-field-value">-</div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">परिवार में कुल सदस्य :</div>
                            <div class="print-field-value">-</div>
                            <div class="print-field-label" style="padding-left: 2rem;">कमाने वाले सदस्यों की संख्या :</div>
                            <div class="print-field-value">-</div>
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
                            <div class="print-field-value">हाँ / नहीं</div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">यदि हाँ तो वर्ष 2023-24 में राशि:</div>
                            <div class="print-field-value">-</div>
                            <div class="print-field-label" style="padding-left: 1rem;">2024-25 में राशि:</div>
                            <div class="print-field-value">-</div>
                            <div class="print-field-label" style="padding-left: 1rem;">2025-26 में राशि:</div>
                            <div class="print-field-value">-</div>
                        </div>

                        <div class="print-field-row">
                            <div class="print-field-label">बैंक खाता संख्या (Account No.) :</div>
                            <div class="print-field-value"><?= Helpers::esc($app['account_number'] ?? '-') ?></div>
                        </div>
                        <div class="print-field-row">
                            <div class="print-field-label">खाता धारक का नाम (Holder Name) :</div>
                            <div class="print-field-value"><?= Helpers::esc(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></div>
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
                        <div class="print-field-value">-</div>
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

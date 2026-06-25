<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$app = $application ?? [];

// Load standard HTML head & custom CSS
require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';

// Fetch Admin Details
$db = \App\Core\Database::getInstance();
$adminEmail = 'admin@tsvs.org';
if (Auth::check()) {
    $stmt = $db->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([Auth::id()]);
    $adminEmail = $stmt->fetchColumn() ?: 'admin@tsvs.org';
}
$adminName = Auth::userName() ?: 'Super Admin';

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

// Helper to determine status badges in Hindi
function getHindiStatusInfo($statusName) {
    $statusName = strtolower($statusName ?? '');
    if ($statusName === 'pending') {
        return ['text' => 'जांचधीन', 'class' => 'tsp-bg-gold'];
    } elseif ($statusName === 'approved') {
        return ['text' => 'स्वीकृत', 'class' => 'tsp-bg-green'];
    } elseif ($statusName === 'rejected') {
        return ['text' => 'अस्वीकृत', 'class' => 'tsp-bg-red'];
    } elseif ($statusName === 'disputed') {
        return ['text' => 'दस्तावेज सत्यापन', 'class' => 'tsp-bg-blue'];
    }
    return ['text' => ucfirst($statusName), 'class' => 'tsp-bg-gold'];
}

$statusInfo = getHindiStatusInfo($app['status_name'] ?? 'Pending');
?>

<!-- Outer full-viewport shell -->
<div class="d-flex flex-column min-vh-100 bg-light" style="font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;">

    <?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

    <!-- Sidebar and Main Panel Workspace Container -->
    <div class="d-flex flex-grow-1 position-relative">

        <?php
        $activeSidebarLink = '/admin/applications';
        require VIEW_PATH . '/layouts/admin-sidebar.php';
        ?>

        <!-- ── MAIN WORKSPACE CONTENT AREA ── -->
        <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
            <div class="container-fluid p-0">

                <a href="/admin/applications" class="btn btn-sm btn-light border px-3 rounded-pill text-secondary fw-semibold mb-3">
                    <i class="bi bi-arrow-left me-1"></i> वापस जाएं (Back)
                </a>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h2 class="h3 fw-bold text-dark mb-1 font-heading">आवेदन की समीक्षा (Review Application)</h2>
                        <p class="text-secondary mb-0 small fw-semibold" style="font-size: 1.25rem;">
                            ID: <strong>TSVS-<?= date('Y') ?>-<?= str_pad((string) ($app['id'] ?? 0), 6, '0', STR_PAD_LEFT) ?></strong>
                            &middot; सबमिट किया गया: <?= !empty($app['submitted_at']) ? date('d M Y, h:i A', strtotime($app['submitted_at'])) : 'N/A' ?>
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill d-inline-flex align-items-center gap-1 shadow-sm px-3 py-2 fw-semibold" onclick="window.print();">
                            <i class="bi bi-printer-fill"></i>
                            <span>प्रिंट / PDF सेव करें</span>
                        </button>
                        <span class="badge rounded-pill px-4 py-2 fs-5 fw-bold <?= $statusInfo['class'] ?>" style="font-size: 1.25rem; display: inline-block; margin-bottom: 0;">
                            <?= htmlspecialchars($statusInfo['text']) ?>
                        </span>
                    </div>
                </div>

                <!-- Action Form Buttons Card -->
                <?php if (($app['status_name'] ?? '') === 'Pending' || ($app['status_name'] ?? '') === 'Disputed'): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <h4 class="h5 fw-bold text-dark mb-3 font-heading">निर्णय प्रक्रिया (Action Menu)</h4>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <form action="/admin/applications/<?= (int) $app['id'] ?>/approve" method="post" class="m-0">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold rounded-pill" style="font-size: 1.35rem;">
                                        <i class="bi bi-check-circle-fill me-1"></i> स्वीकृत करें (Approve)
                                    </button>
                                </form>
                            </div>
                            <div class="col-sm-4">
                                <form action="/admin/applications/<?= (int) $app['id'] ?>/reject" method="post" class="m-0">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn-danger w-100 py-2.5 fw-bold rounded-pill" style="font-size: 1.35rem;">
                                        <i class="bi bi-x-circle-fill me-1"></i> अस्वीकृत करें (Reject)
                                    </button>
                                </form>
                            </div>
                            <div class="col-sm-4">
                                <button type="button" class="btn btn-warning text-dark w-100 py-2.5 fw-bold rounded-pill" style="font-size: 1.35rem;" data-bs-toggle="collapse" data-bs-target="#disputeForm">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> विवादित मार्क करें (Dispute)
                                </button>
                            </div>
                        </div>

                        <!-- Dispute collapse block -->
                        <div class="collapse mt-3" id="disputeForm">
                            <div class="card card-body bg-light border-0" style="border-radius: 12px;">
                                <form action="/admin/applications/<?= (int) $app['id'] ?>/dispute" method="post" class="m-0">
                                    <?= Csrf::field() ?>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">विवाद टिप्पणी (Dispute Remarks / Reason)</label>
                                        <textarea name="dispute_message" class="form-control" rows="3" style="font-size: 1.3rem; border-radius: 8px;"
                                                  placeholder="कृपया टिप्पणी दर्ज करें कि यह आवेदन विवादित क्यों घोषित किया जा रहा है..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4 rounded-pill" style="font-size: 1.25rem;">
                                        <i class="bi bi-send-fill me-1"></i> विवाद सबमिट करें (Submit)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Main Application Detail Fields Card -->
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h4 class="h5 fw-bold text-dark mb-4 border-bottom pb-2 font-heading">
                                    <i class="bi bi-person-fill text-danger me-2"></i> छात्र विवरण (Student Details)
                                </h4>
                                <div class="row g-3 mb-4" style="font-size: 1.35rem;">
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">आवेदक का नाम (Name)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">स्टूडेंट कोड (Reference)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['student_code'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">पिता का नाम (Father's Name)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['father_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">माता का नाम (Mother's Name)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['mother_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small text-muted d-block fw-semibold mb-1">मोबाइल नंबर (Mobile)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['mobile'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small text-muted d-block fw-semibold mb-1">लिंग (Gender)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['gender'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small text-muted d-block fw-semibold mb-1">जन्म तिथि (DOB)</label>
                                        <span class="fw-bold text-dark"><?= !empty($app['dob']) ? date('d/m/Y', strtotime($app['dob'])) : '-' ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-muted d-block fw-semibold mb-1">वर्तमान स्थायी पता (Address)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['address'] ?? '-') ?>, <?= Helpers::esc($app['city'] ?? '') ?>, <?= Helpers::esc($app['district'] ?? '') ?> - <?= Helpers::esc($app['pincode'] ?? '') ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-muted d-block fw-semibold mb-1">भविष्य में आप क्या बनना चाहते हैं (Career Goal)</label>
                                        <span class="fw-bold text-dark text-success"><?= Helpers::esc($app['career_goal'] ?? '-') ?></span>
                                    </div>
                                </div>

                                <h4 class="h5 fw-bold text-dark mb-4 border-bottom pb-2 font-heading">
                                    <i class="bi bi-book text-danger me-2"></i> शैक्षणिक विवरण (Academic Details)
                                </h4>
                                <div class="row g-3 mb-4" style="font-size: 1.35rem;">
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">उत्तीर्ण कक्षा व वर्ष (Passed Class)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['class_year'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">परीक्षा परिणाम प्रतिशत (Percentage)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['percentage'] ?? '-') ?>%</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">प्राप्त / कुल अंक (Marks Obtained/Max)</label>
                                        <span class="fw-bold text-dark"><?= (!empty($app['marks_obtained']) && !empty($app['max_marks'])) ? $app['marks_obtained'] . ' / ' . $app['max_marks'] : '-' ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">बोर्ड / विश्वविद्यालय (Board/Univ)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['board_university'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-muted d-block fw-semibold mb-1">अध्ययनरत विद्यालय/महाविद्यालय का नाम (Institution)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['college_name'] ?? '-') ?></span>
                                    </div>
                                    <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">वर्तमान में अध्ययनरत कक्षा (Current Class)</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['current_class'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">वर्तमान में अध्ययनरत विद्यालय/महाविद्यालय (Current Inst.)</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['current_college'] ?? '-') ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                    <h4 class="h5 fw-bold text-dark mb-4 border-bottom pb-2 font-heading">
                                        <i class="bi bi-people-fill text-danger me-2"></i> पारिवारिक विवरण (Family Details)
                                    </h4>
                                    <div class="row g-3 mb-4" style="font-size: 1.35rem;">
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">परिवार का व्यवसाय/आजीविका का साधन</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['family_occupation'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">वार्षिक पारिवारिक आय</label>
                                            <span class="fw-bold text-dark text-danger"><?= !empty($app['family_income']) ? '₹ ' . number_format((float) $app['family_income'], 2) : '-' ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">परिवार में कुल सदस्य</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['family_members_count'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">परिवार में कमाने वाले सदस्यों की संख्या</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['earning_members_count'] ?? '-') ?></span>
                                        </div>
                                    </div>

                                    <h4 class="h5 fw-bold text-dark mb-4 border-bottom pb-2 font-heading">
                                        <i class="bi bi-gift-fill text-danger me-2"></i> संस्था से पिछले वर्षों में छात्रवृत्ति (Prev Scholarship Details)
                                    </h4>
                                    <div class="row g-3 mb-4" style="font-size: 1.35rem;">
                                        <div class="col-12">
                                            <label class="small text-muted d-block fw-semibold mb-1">क्या संस्था से पिछले वर्षों में छात्रवृत्ति प्राप्त हुई है?</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['prev_scholarship_received'] ?? 'नहीं') ?></span>
                                        </div>
                                        <?php if (($app['prev_scholarship_received'] ?? '') === 'हाँ'): ?>
                                            <div class="col-sm-4">
                                                <label class="small text-muted d-block fw-semibold mb-1">वर्ष 2023-24 में प्राप्त राशि</label>
                                                <span class="fw-bold text-dark"><?= !empty($app['scholarship_amt_2023_24']) ? '₹ ' . number_format((float)$app['scholarship_amt_2023_24'], 2) : '₹ 0.00' ?></span>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="small text-muted d-block fw-semibold mb-1">वर्ष 2024-25 में प्राप्त राशि</label>
                                                <span class="fw-bold text-dark"><?= !empty($app['scholarship_amt_2024_25']) ? '₹ ' . number_format((float)$app['scholarship_amt_2024_25'], 2) : '₹ 0.00' ?></span>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="small text-muted d-block fw-semibold mb-1">वर्ष 2025-26 में प्राप्त राशि</label>
                                                <span class="fw-bold text-dark"><?= !empty($app['scholarship_amt_2025_26']) ? '₹ ' . number_format((float)$app['scholarship_amt_2025_26'], 2) : '₹ 0.00' ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <h4 class="h5 fw-bold text-dark mb-4 border-bottom pb-2 font-heading">
                                        <i class="bi bi-bank text-danger me-2"></i> बैंक विवरण (Bank Account Details)
                                    </h4>
                                    <div class="row g-3" style="font-size: 1.35rem;">
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">बैंक का नाम</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['bank_name'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">खाता धारक का नाम</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['account_holder_name'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6 mt-3">
                                            <label class="small text-muted d-block fw-semibold mb-1">खाता संख्या</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['account_number'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6 mt-3">
                                            <label class="small text-muted d-block fw-semibold mb-1">IFSC कोड</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['ifsc_code'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <h4 class="h5 fw-bold text-dark mb-4 border-bottom pb-2 font-heading">
                                        <i class="bi bi-trophy-fill text-danger me-2"></i> उपलब्धि विवरण (Achievement Details)
                                    </h4>
                                    <div class="row g-3" style="font-size: 1.35rem;">
                                        <div class="col-sm-8">
                                            <label class="small text-muted d-block fw-semibold mb-1">समारोह / उपलब्धि का नाम</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['achievement_title'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="small text-muted d-block fw-semibold mb-1">रैंक / स्थान</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['rank_position'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6 mt-3">
                                            <label class="small text-muted d-block fw-semibold mb-1">श्रेणी</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['achievement_category'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6 mt-3">
                                            <label class="small text-muted d-block fw-semibold mb-1">स्तर</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['achievement_level'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($app['dispute_message'])): ?>
                                    <div class="alert alert-warning mt-4 mb-0 border-0" style="border-radius: 12px;">
                                        <h6 class="fw-bold text-warning-dark mb-1"><i class="bi bi-exclamation-triangle-fill"></i> वर्तमान विवाद संदेश:</h6>
                                        <p class="mb-0 text-dark" style="font-size: 1.3rem; font-weight: 500;"><?= Helpers::esc($app['dispute_message']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Document Review Checklist -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h4 class="h5 fw-bold text-dark mb-4 border-bottom pb-2 font-heading">
                                    <i class="bi bi-file-earmark-check-fill text-danger me-2"></i> दस्तावेज़ सूची (Documents)
                                </h4>
                                <?php if (empty($app['documents'])): ?>
                                    <p class="text-muted small mb-0 fw-semibold">इस आवेदन के साथ कोई दस्तावेज़ संलग्न नहीं है।</p>
                                <?php else: ?>
                                    <div class="d-flex flex-column gap-3">
                                        <?php foreach ($app['documents'] as $document): 
                                            $docType = Helpers::esc($document['document_type'] ?? 'Document');
                                            $docTypeHindi = match ($docType) {
                                                'Photo' => 'पासपोर्ट साइज फोटो',
                                                'Marksheet' => 'मार्कशीट / अंकतालिका',
                                                'Passbook' => 'बैंक पासबुक / निरस्त चेक',
                                                'Signature' => 'आवेदक के हस्ताक्षर',
                                                'Certificate' => 'प्रमाण पत्र',
                                                'Aadhaar' => 'आधार कार्ड',
                                                default => $docType
                                            };
                                        ?>
                                            <div class="border rounded p-3 bg-white hover-shadow-sm transition-all" style="border-radius: 12px !important;">
                                                <div class="fw-bold text-dark mb-1" style="font-size: 1.3rem;">
                                                    <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                                    <?= $docTypeHindi ?>
                                                </div>
                                                <div class="mb-2 text-truncate" style="font-size: 1.15rem;">
                                                    <a href="/uploads/applications/<?= $app['id'] ?>/<?= $document['stored_name'] ?>" target="_blank" class="text-decoration-underline text-primary fw-bold">
                                                        <?= Helpers::esc($document['original_name'] ?? 'दस्तावेज़ देखें') ?>
                                                    </a>
                                                </div>
                                                <div class="small fw-semibold d-flex justify-content-between align-items-center">
                                                    <span class="text-secondary">सत्यापन स्थिति:</span>
                                                    <span class="badge px-2 py-1 rounded <?= strtolower($document['verification_status'] ?? '') === 'verified' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?>">
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

    <?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>
</div>

<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

</body>
</html>

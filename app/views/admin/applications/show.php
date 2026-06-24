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

                    <div>
                        <span class="badge rounded-pill px-4 py-2 fs-5 fw-bold <?= $statusInfo['class'] ?>" style="font-size: 1.25rem; display: inline-block;">
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
                                        <label class="small text-muted d-block fw-semibold mb-1">आवेदक का नाम</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block fw-semibold mb-1">स्टूडेंट कोड (Reference)</label>
                                        <span class="fw-bold text-dark"><?= Helpers::esc($app['student_code'] ?? '-') ?></span>
                                    </div>
                                </div>

                                <?php if (($app['type'] ?? '') === 'scholarship'): ?>
                                    <h4 class="h5 fw-bold text-dark mb-4 border-bottom pb-2 font-heading">
                                        <i class="bi bi-bank text-danger me-2"></i> बैंक एवं पारिवारिक विवरण (Bank & Income Details)
                                    </h4>
                                    <div class="row g-3" style="font-size: 1.35rem;">
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">बैंक का नाम</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['bank_name'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block fw-semibold mb-1">खाता संख्या</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['account_number'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6 mt-3">
                                            <label class="small text-muted d-block fw-semibold mb-1">IFSC कोड</label>
                                            <span class="fw-bold text-dark"><?= Helpers::esc($app['ifsc_code'] ?? '-') ?></span>
                                        </div>
                                        <div class="col-sm-6 mt-3">
                                            <label class="small text-muted d-block fw-semibold mb-1">वार्षिक पारिवारिक आय</label>
                                            <span class="fw-bold text-dark text-danger"><?= !empty($app['family_income']) ? '₹ ' . number_format((float) $app['family_income'], 2) : '-' ?></span>
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

            </div>
        </main>
    </div>

    <?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>
</div>

<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

</body>
</html>

<?php
declare(strict_types=1);

use App\Core\Helpers;

$application = $application ?? [];
$isScholarship = (($application['type'] ?? '') === 'scholarship');
$statusId = (int) ($application['status_id'] ?? 2);
$statusName = $application['status_name'] ?? 'Submitted';

$submittedAt = $application['submitted_at'] ?? null;
if ($statusId === 7 && !empty($application['resubmitted_at'])) {
    $submittedAt = $application['resubmitted_at'];
}

$steps = [
    ['key' => 'draft',       'label' => 'ड्राफ्ट (Draft)',              'minStatus' => 1],
    ['key' => 'submitted',   'label' => 'जमा किया गया (Submitted)',     'minStatus' => 2],
    ['key' => 'review',      'label' => 'समीक्षाधीन (Under Review)',    'minStatus' => 3],
    ['key' => 'decision',    'label' => 'निर्णय (Decision)',            'minStatus' => 4],
];

function ackStepState(int $statusId, int $minStatus, string $statusName): string {
    if ($statusId >= $minStatus) {
        if ($minStatus === 4 && $statusName === 'Rejected') {
            return 'rejected';
        }
        if ($minStatus === 4 && in_array($statusName, ['Approved', 'Rejected'], true)) {
            return 'done';
        }
        if ($minStatus === 4) {
            return 'pending';
        }
        return 'done';
    }
    if ($statusId + 1 === $minStatus || ($statusId === 7 && $minStatus === 3)) {
        return 'active';
    }
    return 'pending';
}
?>

<div class="mb-4 no-print d-flex justify-content-between align-items-center">
    <a href="/dashboard/applications" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 back-link">
        <i class="bi bi-arrow-left"></i>
        <span>आवेदन सूची पर वापस जाएं / Back to Applications</span>
    </a>
    <button type="button" class="btn btn-primary rounded-pill d-inline-flex align-items-center gap-2 shadow-sm px-4 no-print" onclick="window.print();">
        <i class="bi bi-printer-fill"></i>
        <span>प्रिंट करें / Print Application</span>
    </button>
</div>

<div class="card border-0 shadow-sm p-4 p-md-5 bg-white form-card" style="border-radius: 1.25rem;">
    <div class="text-center mb-4 border-bottom pb-4">
        <div class="text-center mb-2">
            <img src="<?= \App\Core\Url::asset('images/logo/logo-placeholder.svg') ?>" alt="Tamboli Samaj" width="64" height="64">
        </div>
        <h3 class="fw-bold text-dark mb-1">तम्बोली समाज विकास संस्था, राजस्थान</h3>
        <div class="text-muted small fw-semibold mb-1">पंजीकृत संख्या: 411 / 2016-17</div>
        <div class="badge bg-success-subtle text-success py-2 px-3 rounded-pill mt-2 border border-success-subtle fw-bold fs-6">
            <i class="bi bi-patch-check-fill me-1"></i> आवेदन सफलतापूर्वक जमा हुआ / Application Submitted Successfully
        </div>
    </div>

    <div class="text-center mb-5 py-4 bg-light rounded-3 border">
        <div class="text-muted small text-uppercase fw-semibold mb-1">आवेदन संख्या / Application Number</div>
        <div class="display-5 fw-bold text-dark font-monospace"><?= Helpers::esc($application['application_no'] ?? 'N/A') ?></div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">आवेदन का विवरण / Application Details</h5>
            <table class="table table-borderless align-middle">
                <tbody>
                    <tr>
                        <td class="text-muted ps-0 py-2" style="width: 40%;">योजना का नाम:</td>
                        <td class="fw-semibold text-dark py-2">
                            <?= $isScholarship ? 'शिक्षा प्रोत्साहन छात्रवृत्ति (Scholarship)' : 'प्रतिभा सम्मान (Pratibha Samman)' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 py-2">शैक्षणिक सत्र:</td>
                        <td class="text-dark py-2"><?= Helpers::esc($application['session_name'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 py-2">आवेदन तिथि:</td>
                        <td class="text-dark py-2">
                            <?= $submittedAt ? date('d M Y, h:i A', strtotime((string) $submittedAt)) : 'N/A' ?>
                            <?php if ($statusId === 7): ?>
                                <span class="badge bg-purple ms-1">पुनः जमा / Resubmitted</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 py-2">वर्तमान स्थिति:</td>
                        <td class="py-2">
                            <?php
                            $badgeClass = match ($statusName) {
                                'Approved'           => 'bg-success',
                                'Rejected'           => 'bg-danger',
                                'Under Review'       => 'bg-warning text-dark',
                                'Pending Correction' => 'bg-orange',
                                'Resubmitted'        => 'bg-purple',
                                default              => 'bg-primary',
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?> text-white px-3 py-2 rounded-pill">
                                <?= Helpers::esc($statusName) ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">आवेदक का विवरण / Applicant Profile</h5>
            <table class="table table-borderless align-middle">
                <tbody>
                    <tr>
                        <td class="text-muted ps-0 py-2" style="width: 40%;">विद्यार्थी का नाम:</td>
                        <td class="fw-semibold text-dark py-2">
                            <?= Helpers::esc(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? '')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 py-2">पिता का नाम:</td>
                        <td class="text-dark py-2"><?= Helpers::esc($application['father_name'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 py-2">उत्तीर्ण कक्षा:</td>
                        <td class="text-dark py-2"><?= Helpers::esc($application['class_year'] ?? 'N/A') ?> (<?= Helpers::esc($application['percentage'] ?? '0.00') ?>%)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Timeline -->
    <div class="mt-5 border-top pt-4 no-print timeline">
        <h5 class="fw-bold text-dark mb-4">आवेदन की प्रगति / Application Progress Tracker</h5>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center position-relative my-4 gap-4 gap-md-2">
            <div class="position-absolute top-50 start-0 end-0 bg-light d-none d-md-block" style="height: 4px; z-index: 1; transform: translateY(-50%);"></div>

            <?php foreach ($steps as $idx => $step):
                $state = ackStepState($statusId, $step['minStatus'], $statusName);
                $circleClass = match ($state) {
                    'done'     => ($step['key'] === 'decision' && $statusName === 'Rejected') ? 'bg-danger' : 'bg-success',
                    'active'   => 'bg-primary',
                    'rejected' => 'bg-danger',
                    default    => 'bg-light text-muted border',
                };
                $textClass = in_array($state, ['done', 'active', 'rejected'], true) ? 'fw-bold' : 'text-muted';
            ?>
            <div class="d-flex align-items-center gap-3 d-md-block text-md-center position-relative" style="z-index: 2;">
                <div class="rounded-circle <?= $circleClass ?> text-white d-inline-flex align-items-center justify-content-center mx-md-auto mb-md-2" style="width: 40px; height: 40px;">
                    <i class="bi <?= $state === 'done' ? 'bi-check-lg' : ($state === 'rejected' ? 'bi-x-lg' : 'bi-circle') ?> fs-5"></i>
                </div>
                <div>
                    <div class="<?= $textClass ?> small"><?= $step['label'] ?></div>
                    <?php if ($step['key'] === 'submitted' && $submittedAt): ?>
                        <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y', strtotime((string) $submittedAt)) ?></div>
                    <?php elseif ($step['key'] === 'decision' && $statusName === 'Approved'): ?>
                        <div class="text-success small">स्वीकृत / Approved</div>
                    <?php elseif ($step['key'] === 'decision' && $statusName === 'Rejected'): ?>
                        <div class="text-danger small">अस्वीकृत / Rejected</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mt-4 no-print d-flex flex-wrap gap-2 justify-content-center">
        <a href="/dashboard" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-house me-1"></i> डैशबोर्ड / Back to Dashboard
        </a>
        <button type="button" class="btn btn-outline-primary rounded-pill px-4" disabled title="Coming soon">
            <i class="bi bi-download me-1"></i> डाउनलोड / Download
        </button>
    </div>

    <div class="mt-5 border-top pt-4 text-center">
        <p class="text-muted small mb-0">यह एक कंप्यूटर जनित पावती पत्र है, इस पर हस्ताक्षर की आवश्यकता नहीं है।</p>
        <p class="text-muted small mt-1">तम्बोली समाज विकास संस्था, राजस्थान द्वारा संचालित छात्रवृत्ति एवं प्रतिभा सम्मान पोर्टल।</p>
    </div>
</div>
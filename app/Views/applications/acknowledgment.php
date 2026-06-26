<?php
declare(strict_types=1);

use App\Core\Helpers;
use App\Core\Flash;

$application = $application ?? [];
$isScholarship = (($application['type'] ?? '') === 'scholarship');
$title = "आवेदन पावती पत्र / Acknowledgment — Tamboli Samaj Portal";

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
            
            <!-- Breadcrumbs and Header (Hidden in Print) -->
            <div class="mb-4 no-print d-flex justify-content-between align-items-center">
                <a href="/dashboard/applications" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>आवेदन सूची पर वापस जाएं / Back to Applications</span>
                </a>
                <button type="button" class="btn btn-primary rounded-pill d-inline-flex align-items-center gap-2 shadow-sm px-4" onclick="window.print();">
                    <i class="bi bi-printer-fill"></i>
                    <span>प्रिंट करें / Print Acknowledgment</span>
                </button>
            </div>

            <!-- Printable Acknowledgment Card -->
            <div class="card border-0 shadow-sm p-4 p-md-5 bg-white" style="border-radius: 1.25rem;">
                <!-- Header (Visible in print too) -->
                <div class="text-center mb-4 border-bottom pb-4">
                    <div class="print-logo-wrapper text-center mb-2">
                        <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj" class="print-logo" width="64" height="64">
                    </div>
                    <h3 class="fw-bold text-dark mb-1">तम्बोली समाज विकास संस्था, राजस्थान</h3>
                    <div class="text-muted small fw-semibold mb-1">पंजीकृत संख्या: 411 / 2016-17</div>
                    <div class="badge bg-success-subtle text-success py-2 px-3 rounded-pill mt-2 border border-success-subtle fw-bold fs-6">
                        <i class="bi bi-patch-check-fill me-1"></i> आवेदन सफलतापूर्वक जमा हुआ / Application Submitted Successfully
                    </div>
                </div>

                <!-- Detail Table -->
                <div class="row g-4 mt-2">
                    <div class="col-md-6">
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">आवेदन का विवरण / Application Details</h5>
                        <table class="table table-borderless align-middle">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0 py-2" style="width: 40%;">आवेदन संख्या (Application No.):</td>
                                    <td class="fw-bold text-dark py-2"><?= Helpers::esc($application['application_no'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">योजना का नाम (Scheme Name):</td>
                                    <td class="fw-semibold text-dark py-2">
                                        <?= $isScholarship ? 'शिक्षा प्रोत्साहन छात्रवृत्ति योजना (Scholarship)' : 'प्रतिभा सम्मान समारोह (Pratibha Samman)' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">शैक्षणिक सत्र (Academic Session):</td>
                                    <td class="text-dark py-2"><?= Helpers::esc($application['session_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">आवेदन तिथि (Submission Date):</td>
                                    <td class="text-dark py-2">
                                        <?= $application['submitted_at'] ? date('d M Y, h:i A', strtotime($application['submitted_at'])) : 'N/A' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">वर्तमान स्थिति (Current Status):</td>
                                    <td class="py-2">
                                        <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-monospace small">
                                            <?= Helpers::esc($application['status_name'] ?? 'Submitted') ?>
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
                                    <td class="text-muted ps-0 py-2" style="width: 40%;">विद्यार्थी का नाम (Student Name):</td>
                                    <td class="fw-semibold text-dark py-2">
                                        <?= Helpers::esc(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? '')) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">पिता का नाम (Father's Name):</td>
                                    <td class="text-dark py-2"><?= Helpers::esc($application['father_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">उत्तीर्ण कक्षा (Passed Class):</td>
                                    <td class="text-dark py-2"><?= Helpers::esc($application['class_year'] ?? 'N/A') ?> (<?= Helpers::esc($application['percentage'] ?? '0.00') ?>%)</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">स्थायी पता (Address):</td>
                                    <td class="text-dark py-2" style="font-size: 0.95rem;">
                                        <?= Helpers::esc(($application['address'] ?? '') . ', ' . ($application['city'] ?? '') . ', ' . ($application['district'] ?? '') . ' - ' . ($application['pincode'] ?? '')) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Status Timeline Tracker (Hidden in Print) -->
                <div class="mt-5 border-top pt-4 no-print">
                    <h5 class="fw-bold text-dark mb-4">आवेदन की प्रगति / Application Progress Tracker</h5>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center position-relative my-4 gap-4 gap-md-2">
                        <!-- Horizontal line for timeline in md+ screens -->
                        <div class="position-absolute top-50 start-0 end-0 bg-light d-none d-md-block" style="height: 4px; z-index: 1; transform: translateY(-50%);"></div>
                        
                        <div class="d-flex align-items-center gap-3 d-md-block text-md-center position-relative" style="z-index: 2;">
                            <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mx-md-auto mb-md-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-lg fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-success small">आवेदन प्राप्त (Submitted)</div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= $application['submitted_at'] ? date('d M Y', strtotime($application['submitted_at'])) : '' ?></div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 d-md-block text-md-center position-relative" style="z-index: 2;">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mx-md-auto mb-md-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-clock-fill fs-6"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-primary small">समीक्षा अधीन (Under Review)</div>
                                <div class="text-muted" style="font-size: 0.75rem;">प्रक्रिया जारी है</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 d-md-block text-md-center position-relative" style="z-index: 2;">
                            <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mx-md-auto mb-md-2 border" style="width: 40px; height: 40px;">
                                <i class="bi bi-patch-check fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-muted small">स्वीकृत / अस्वीकृत (Approval)</div>
                                <div class="text-muted" style="font-size: 0.75rem;">निर्णय लंबित</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Print Note Footer -->
                <div class="mt-5 border-top pt-4 text-center">
                    <p class="text-muted small mb-0">यह एक कंप्यूटर जनित पावती पत्र है, इस पर हस्ताक्षर की आवश्यकता नहीं है।</p>
                    <p class="text-muted small mt-1">तम्बोली समाज विकास संस्था, राजस्थान द्वारा संचालित छात्रवृत्ति एवं प्रतिभा सम्मान पोर्टल।</p>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.php'; ?>

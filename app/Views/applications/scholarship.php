<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helpers;

$activeSession = $activeSession ?? [];
$student = $student ?? [];
$old = Flash::get('old');
$old = $old[0] ?? [];

$isEdit = $isEdit ?? false;
$application = $application ?? [];
$marksheetDoc = null;
$passbookDoc = null;
$photoDoc = null;
$signatureDoc = null;
if ($isEdit && !empty($application['documents'])) {
    foreach ($application['documents'] as $doc) {
        if ($doc['document_type'] === 'Marksheet') $marksheetDoc = $doc;
        if ($doc['document_type'] === 'Passbook') $passbookDoc = $doc;
        if ($doc['document_type'] === 'Photo') $photoDoc = $doc;
        if ($doc['document_type'] === 'Signature') $signatureDoc = $doc;
    }
}

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <?php
    $activeLink = 'apply';
    require VIEW_PATH . '/layouts/student-sidebar.php';
    ?>

    <!-- Main Content Area -->
    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">
            
            <!-- Back button & session indicator -->
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <a href="/applications/create" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>वापस जाएं / Back</span>
                </a>
                <span class="badge bg-light text-dark py-2 px-3 border">सत्र / Session: <?= Helpers::esc($activeSession['session_name'] ?? 'N/A') ?></span>
            </div>

            <!-- Title -->
            <div class="mb-4 text-start">
                <h2 class="tsp-dash-welcome-title fs-3 mb-1">छात्रवृत्ति आवेदन पत्र / Scholarship Application Form</h2>
                <p class="text-muted small mb-0">कृपया नीचे दिए गए चरणों का पालन करते हुए ऑनलाइन फॉर्म सावधानीपूर्वक भरें।</p>
            </div>

            <!-- Form Stepper Header -->
            <div class="tsp-stepper" id="formStepper">
                <div class="tsp-step-item active" data-step="1">
                    <div class="tsp-step-circle">1</div>
                    <div class="tsp-step-label">व्यक्तिगत विवरण<br><small class="text-muted d-none d-md-inline">Profile</small></div>
                </div>
                <div class="tsp-step-item" data-step="2">
                    <div class="tsp-step-circle">2</div>
                    <div class="tsp-step-label">शैक्षणिक व बैंक<br><small class="text-muted d-none d-md-inline">Academic & Bank</small></div>
                </div>
                <div class="tsp-step-item" data-step="3">
                    <div class="tsp-step-circle">3</div>
                    <div class="tsp-step-label">दस्तावेज़ अपलोड<br><small class="text-muted d-none d-md-inline">Uploads</small></div>
                </div>
                <div class="tsp-step-item" data-step="4">
                    <div class="tsp-step-circle">4</div>
                    <div class="tsp-step-label">पूर्वावलोकन<br><small class="text-muted d-none d-md-inline">Form Preview</small></div>
                </div>
            </div>

            <!-- Interactive Form Wizard Wrapper -->
            <div class="card border-0 shadow-sm" style="border-radius: 1.25rem;">
                <div class="card-body p-4 p-md-5">
                    <form action="<?= $isEdit ? '/applications/' . $application['id'] . '/edit' : '/applications/scholarship' ?>" method="POST" enctype="multipart/form-data" id="scholarshipWizardForm">
                        <?= Csrf::field() ?>

                        <!-- STEP 1: Personal & Family Information -->
                        <div class="tsp-form-step active" id="step1">
                            <h4 class="h5 fw-bold mb-4 text-dark border-bottom pb-2">
                                <i class="bi bi-person-fill text-muted me-2"></i> 1. व्यक्तिगत एवं पारिवारिक विवरण / Personal & Family Details
                            </h4>
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">प्रथम नाम (First Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="field_first_name" class="form-control border-2 py-2" style="border-radius: 0.5rem;" required
                                           value="<?= Helpers::esc($old['first_name'] ?? $student['first_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">अंतिम नाम (Last Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="field_last_name" class="form-control border-2 py-2" style="border-radius: 0.5rem;" required
                                           value="<?= Helpers::esc($old['last_name'] ?? $student['last_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">पिता का नाम (Father Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="father_name" id="field_father_name" class="form-control border-2 py-2" style="border-radius: 0.5rem;" required
                                           value="<?= Helpers::esc($old['father_name'] ?? $student['father_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">माता का नाम (Mother Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="mother_name" id="field_mother_name" class="form-control border-2 py-2" style="border-radius: 0.5rem;" required
                                           value="<?= Helpers::esc($old['mother_name'] ?? $student['mother_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">जन्म तिथि (Date of Birth) <span class="text-danger">*</span></label>
                                    <input type="date" name="dob" id="field_dob" class="form-control border-2 py-2" style="border-radius: 0.5rem;" required
                                           value="<?= Helpers::esc($old['dob'] ?? $student['dob'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">लिंग (Gender) <span class="text-danger">*</span></label>
                                    <select name="gender" id="field_gender" class="form-select border-2 py-2" style="border-radius: 0.5rem;" required>
                                        <option value="">Select</option>
                                        <option value="Male" <?= ($old['gender'] ?? $student['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>पुरुष (Male)</option>
                                        <option value="Female" <?= ($old['gender'] ?? $student['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>महिला (Female)</option>
                                        <option value="Other" <?= ($old['gender'] ?? $student['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>अन्य (Other)</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">मोबाइल नंबर (Mobile) <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile" id="field_mobile" class="form-control border-2 py-2" style="border-radius: 0.5rem;" readonly
                                           value="<?= Helpers::esc($student['mobile'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">ईमेल (Email) <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="field_email" class="form-control border-2 py-2" style="border-radius: 0.5rem;" readonly
                                           value="<?= Helpers::esc($student['email'] ?? '') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-muted">स्थाई पता (Permanent Address) <span class="text-danger">*</span></label>
                                    <textarea name="address" id="field_address" class="form-control border-2 py-2" style="border-radius: 0.5rem;" rows="3" required><?= Helpers::esc($old['address'] ?? $student['address'] ?? '') ?></textarea>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small fw-semibold text-muted">शहर/कस्बा (City) <span class="text-danger">*</span></label>
                                    <input type="text" name="city" id="field_city" class="form-control border-2 py-2" style="border-radius: 0.5rem;" required
                                           value="<?= Helpers::esc($old['city'] ?? $student['city'] ?? '') ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small fw-semibold text-muted">जिला (District) <span class="text-danger">*</span></label>
                                    <input type="text" name="district" id="field_district" class="form-control border-2 py-2" style="border-radius: 0.5rem;" required
                                           value="<?= Helpers::esc($old['district'] ?? $student['district'] ?? '') ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small fw-semibold text-muted">पिनकोड (PIN Code) <span class="text-danger">*</span></label>
                                    <input type="text" name="pincode" id="field_pincode" class="form-control border-2 py-2" style="border-radius: 0.5rem;" required
                                           value="<?= Helpers::esc($old['pincode'] ?? $student['pincode'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: Academic & Bank Details -->
                        <div class="tsp-form-step" id="step2">
                            <h4 class="h5 fw-bold mb-4 text-dark border-bottom pb-2">
                                <i class="bi bi-book-half text-muted me-2"></i> 2. शैक्षणिक एवं बैंक खाता विवरण / Academic & Bank Details
                            </h4>
                            
                            <h5 class="h6 fw-bold mb-3 text-secondary">शैक्षणिक जानकारी / Academic Records</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">कक्षा / वर्ष (Class/Year) <span class="text-danger">*</span></label>
                                    <select name="class_year" id="field_class_year" class="form-select border-2 py-2" style="border-radius: 0.5rem;" required>
                                        <option value="">कक्षा चुनें / Select</option>
                                        <?php foreach (['10th', '12th', 'Graduation', 'Post Graduation'] as $cy): ?>
                                            <option value="<?= $cy ?>" <?= ($old['class_year'] ?? $application['class_year'] ?? '') === $cy ? 'selected' : '' ?>><?= $cy ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">प्राप्त प्रतिशत (Percentage) <span class="text-danger">*</span></label>
                                    <input type="number" name="percentage" id="field_percentage" class="form-control border-2 py-2" style="border-radius: 0.5rem;" step="0.01" min="0" max="100" placeholder="उदा. 75.00" required
                                           value="<?= Helpers::esc($old['percentage'] ?? $application['percentage'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">प्राप्त अंक (Marks Obtained)</label>
                                    <input type="number" name="marks_obtained" id="field_marks_obtained" class="form-control border-2 py-2" style="border-radius: 0.5rem;" placeholder="प्राप्त अंक"
                                           value="<?= Helpers::esc($old['marks_obtained'] ?? $application['marks_obtained'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">कुल पूर्णांक (Max Marks)</label>
                                    <input type="number" name="max_marks" id="field_max_marks" class="form-control border-2 py-2" style="border-radius: 0.5rem;" placeholder="कुल पूर्णांक"
                                           value="<?= Helpers::esc($old['max_marks'] ?? $application['max_marks'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">विद्यालय / महाविद्यालय (College/School Name)</label>
                                    <input type="text" name="college_name" id="field_college_name" class="form-control border-2 py-2" style="border-radius: 0.5rem;" placeholder="विद्यालय/महाविद्यालय"
                                           value="<?= Helpers::esc($old['college_name'] ?? $application['college_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">बोर्ड / विश्वविद्यालय (Board/University)</label>
                                    <input type="text" name="board_university" id="field_board_university" class="form-control border-2 py-2" style="border-radius: 0.5rem;" placeholder="उदा. RBSE, CBSE"
                                           value="<?= Helpers::esc($old['board_university'] ?? $application['board_university'] ?? '') ?>">
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: #cbd5e1;">

                            <h5 class="h6 fw-bold mb-3 text-secondary">बैंक खाता विवरण / Bank Account</h5>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">बैंक का नाम (Bank Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" id="field_bank_name" class="form-control border-2 py-2" style="border-radius: 0.5rem;" placeholder="बैंक का नाम" required
                                           value="<?= Helpers::esc($old['bank_name'] ?? $application['bank_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">खाता संख्या (Account Number) <span class="text-danger">*</span></label>
                                    <input type="text" name="account_number" id="field_account_number" class="form-control border-2 py-2" style="border-radius: 0.5rem;" placeholder="खाता नंबर" required
                                           value="<?= Helpers::esc($old['account_number'] ?? $application['account_number'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">IFSC कोड (IFSC Code) <span class="text-danger">*</span></label>
                                    <input type="text" name="ifsc_code" id="field_ifsc_code" class="form-control border-2 py-2" style="border-radius: 0.5rem;" placeholder="IFSC कोड" required
                                           value="<?= Helpers::esc($old['ifsc_code'] ?? $application['ifsc_code'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">वार्षिक पारिवारिक आय (Annual Family Income)</label>
                                    <input type="number" name="family_income" id="field_family_income" class="form-control border-2 py-2" style="border-radius: 0.5rem;" placeholder="उदा. 180000"
                                           value="<?= Helpers::esc($old['family_income'] ?? $application['family_income'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: Required Upload Documents -->
                        <div class="tsp-form-step" id="step3">
                            <h4 class="h5 fw-bold mb-4 text-dark border-bottom pb-2">
                                <i class="bi bi-file-earmark-arrow-up text-muted me-2"></i> 3. आवश्यक दस्तावेज़ अपलोड / Upload Documents
                            </h4>
                            
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">अंकतालिका अपलोड करें (Marksheet) <span class="text-danger">*</span></label>
                                        <input type="file" name="marksheet" id="file_marksheet" class="form-control" accept=".jpg,.jpeg,.png,.pdf" <?= $isEdit ? '' : 'required' ?>>
                                        <div class="form-text text-muted small mt-1">पिछले वर्ष की अंकतालिका (JPG, PNG, PDF | अधिकतम: 2MB)</div>
                                        <?php if ($marksheetDoc): ?>
                                            <div class="mt-2 text-success small">
                                                <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                <a href="/uploads/applications/<?= $application['id'] ?>/<?= $marksheetDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline fw-semibold"><?= Helpers::esc($marksheetDoc['original_name']) ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">बैंक पासबुक अपलोड करें (Bank Passbook) <span class="text-danger">*</span></label>
                                        <input type="file" name="passbook" id="file_passbook" class="form-control" accept=".jpg,.jpeg,.png,.pdf" <?= $isEdit ? '' : 'required' ?>>
                                        <div class="form-text text-muted small mt-1">खाता संख्या एवं IFSC दर्शाने वाला पृष्ठ (JPG, PNG, PDF | अधिकतम: 2MB)</div>
                                        <?php if ($passbookDoc): ?>
                                            <div class="mt-2 text-success small">
                                                <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                <a href="/uploads/applications/<?= $application['id'] ?>/<?= $passbookDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline fw-semibold"><?= Helpers::esc($passbookDoc['original_name']) ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">पासपोर्ट साइज फोटो (Student Photo) <span class="text-danger">*</span></label>
                                        <input type="file" name="photo" id="file_photo" class="form-control" accept=".jpg,.jpeg,.png" <?= $isEdit ? '' : 'required' ?>>
                                        <div class="form-text text-muted small mt-1">हाल ही की रंगीन पासपोर्ट फोटो (केवल JPG, PNG | अधिकतम: 1MB)</div>
                                        <?php if ($photoDoc): ?>
                                            <div class="mt-2 text-success small">
                                                <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                <a href="/uploads/applications/<?= $application['id'] ?>/<?= $photoDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline fw-semibold"><?= Helpers::esc($photoDoc['original_name']) ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">विद्यार्थी के हस्ताक्षर (Student Signature) <span class="text-danger">*</span></label>
                                        <input type="file" name="signature" id="file_signature" class="form-control" accept=".jpg,.jpeg,.png" <?= $isEdit ? '' : 'required' ?>>
                                        <div class="form-text text-muted small mt-1">सफ़ेद कागज पर काले/नीले पेन से हस्ताक्षर (केवल JPG, PNG | अधिकतम: 500KB)</div>
                                        <?php if ($signatureDoc): ?>
                                            <div class="mt-2 text-success small">
                                                <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                <a href="/uploads/applications/<?= $application['id'] ?>/<?= $signatureDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline fw-semibold"><?= Helpers::esc($signatureDoc['original_name']) ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 4: Offline-Style Formal Preview Sheet -->
                        <div class="tsp-form-step" id="step4">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                                <h4 class="h5 fw-bold text-dark mb-0">
                                    <i class="bi bi-eye text-muted me-2"></i> 4. आवेदन पूर्वावलोकन / Form Preview & Verification
                                </h4>
                                <button type="button" class="btn btn-outline-dark btn-sm rounded-pill d-inline-flex align-items-center gap-1 shadow-sm px-3" onclick="window.print();">
                                    <i class="bi bi-printer-fill"></i>
                                    <span>प्रिंट / PDF सेव करें</span>
                                </button>
                            </div>

                            <p class="text-muted small mb-4">
                                कृपया सबमिट करने से पहले अपने सभी विवरणों की जांच कर लें। आप प्रिंट बटन पर क्लिक करके इसे सहेज भी सकते हैं।
                            </p>

                            <!-- Formal Paper Form Container -->
                            <div id="printableScholarshipForm">
                                <!-- Print Header -->
                                <div class="print-header">
                                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                                        <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj" width="50" height="50">
                                        <div>
                                            <h2 class="print-org-title">तम्बोली समाज विकास संस्था, राजस्थान</h2>
                                            <span class="print-org-subtitle">TAMBOLI SAMAJ VIKAS SANSTHA, RAJASTHAN</span>
                                        </div>
                                    </div>
                                    <div class="print-form-title">
                                        शिक्षा प्रोत्साहन छात्रवृत्ति आवेदन पत्र (सत्र: <?= Helpers::esc($activeSession['session_name'] ?? 'N/A') ?>)
                                    </div>
                                </div>

                                <!-- Photo and Profile Info block -->
                                <div class="d-flex justify-content-between align-items-start gap-4 mb-4">
                                    <div class="flex-grow-1">
                                        <div class="print-section-heading">1. व्यक्तिगत जानकारी (Personal Details)</div>
                                        <table class="print-table">
                                            <tr>
                                                <th>आवेदक का नाम / Name</th>
                                                <td><span id="preview_name">-</span></td>
                                            </tr>
                                            <tr>
                                                <th>पिता का नाम / Father's Name</th>
                                                <td><span id="preview_father_name">-</span></td>
                                            </tr>
                                            <tr>
                                                <th>माता का नाम / Mother's Name</th>
                                                <td><span id="preview_mother_name">-</span></td>
                                            </tr>
                                            <tr>
                                                <th>लिंग / Gender</th>
                                                <td><span id="preview_gender">-</span></td>
                                            </tr>
                                            <tr>
                                                <th>जन्म तिथि / DOB</th>
                                                <td><span id="preview_dob">-</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- Photo Display Box -->
                                    <div class="flex-shrink-0 d-flex flex-column align-items-center">
                                        <div class="print-photo-box" id="preview_photo_box">
                                            फोटो<br>Photo
                                        </div>
                                        <span class="small text-muted mt-1 font-monospace">1.5" x 2.0"</span>
                                    </div>
                                </div>

                                <!-- Contact Details -->
                                <div class="print-section-heading">2. संपर्क विवरण (Contact Details)</div>
                                <table class="print-table">
                                    <tr>
                                        <th>मोबाइल / Mobile Number</th>
                                        <td><span id="preview_mobile">-</span></td>
                                        <th>ईमेल / Email ID</th>
                                        <td><span id="preview_email">-</span></td>
                                    </tr>
                                    <tr>
                                        <th>स्थाई पता / Address</th>
                                        <td colspan="3"><span id="preview_address">-</span></td>
                                    </tr>
                                    <tr>
                                        <th>शहर / City</th>
                                        <td><span id="preview_city">-</span></td>
                                        <th>जिला व पिनकोड / Dist & PIN</th>
                                        <td><span id="preview_district_pincode">-</span></td>
                                    </tr>
                                </table>

                                <!-- Academic Details -->
                                <div class="print-section-heading">3. शैक्षणिक योग्यता (Academic Records)</div>
                                <table class="print-table">
                                    <tr>
                                        <th>कक्षा व वर्ष / Class & Year</th>
                                        <td><span id="preview_class_year">-</span></td>
                                        <th>प्राप्त प्रतिशत / Percentage</th>
                                        <td><strong id="preview_percentage">-</strong></td>
                                    </tr>
                                    <tr>
                                        <th>प्राप्त / कुल अंक / Marks</th>
                                        <td><span id="preview_marks">-</span></td>
                                        <th>विद्यालय/महाविद्यालय / Institution</th>
                                        <td><span id="preview_college">-</span></td>
                                    </tr>
                                    <tr>
                                        <th>बोर्ड/विश्वविद्यालय / Board</th>
                                        <td colspan="3"><span id="preview_board">-</span></td>
                                    </tr>
                                </table>

                                <!-- Bank Details -->
                                <div class="print-section-heading">4. बैंक खाता विवरण (Bank Details)</div>
                                <table class="print-table">
                                    <tr>
                                        <th>बैंक का नाम / Bank Name</th>
                                        <td><span id="preview_bank_name">-</span></td>
                                        <th>खाता संख्या / Account Number</th>
                                        <td><span id="preview_account_number">-</span></td>
                                    </tr>
                                    <tr>
                                        <th>IFSC कोड / IFSC Code</th>
                                        <td><span id="preview_ifsc_code">-</span></td>
                                        <th>पारिवारिक आय / Family Income</th>
                                        <td><span id="preview_family_income">-</span></td>
                                    </tr>
                                </table>

                                <!-- Upload Checklist info -->
                                <div class="print-section-heading">5. संलग्न दस्तावेज़ (Attached Documents Checklist)</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-sm-6">
                                        <div class="fw-semibold small text-muted mb-2">गत वर्ष की अंकतालिका / Last Year Marksheet:</div>
                                        <div id="preview_marksheet_box" class="tsp-thumbnail-preview d-flex align-items-center justify-content-center bg-light text-muted py-3" style="min-height: 120px;">
                                            अंकतालिका / Marksheet
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="fw-semibold small text-muted mb-2">बैंक पासबुक / Bank Passbook Photo:</div>
                                        <div id="preview_passbook_box" class="tsp-thumbnail-preview d-flex align-items-center justify-content-center bg-light text-muted py-3" style="min-height: 120px;">
                                            पासबुक / Passbook
                                        </div>
                                    </div>
                                </div>

                                <!-- Signature box and declarations -->
                                <div class="print-footer-declaration border-top pt-4">
                                    <p class="mb-5 small text-dark">
                                        <strong>घोषणा (Declaration):</strong> मैं प्रमाणित करता हूँ कि इस आवेदन में दी गई सभी जानकारियाँ सत्य एवं सही हैं। यदि कोई भी जानकारी असत्य पाई जाती है, तो संस्था को मेरा आवेदन निरस्त करने का पूर्ण अधिकार है।
                                    </p>
                                    <div class="d-flex justify-content-between align-items-end mt-5 pt-3">
                                        <div>
                                            <div style="width: 150px; border-top: 1px solid #000; text-align: center;" class="pt-2 small fw-bold">दिनांक (Date)</div>
                                        </div>
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="print-signature-box mb-2" id="preview_signature_box">
                                                हस्ताक्षर<br>Signature
                                            </div>
                                            <div style="width: 200px; border-top: 1px solid #000; text-align: center;" class="pt-2 small fw-bold">आवेदक के हस्ताक्षर / Signature</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stepper Actions Navigation Footer -->
                        <div class="d-flex gap-2 justify-content-between mt-5 pt-3 border-top" id="wizardActions">
                            <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-semibold d-none" id="btnPrev" onclick="moveStep(-1);">
                                <i class="bi bi-chevron-left"></i> पिछला चरण / Previous
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" id="btnCancel" onclick="location.href='/applications/create';">
                                रद्द करें / Cancel
                            </button>
                            <button type="button" class="btn tsp-dash-welcome-btn shadow-sm rounded-pill px-4 py-2 fw-semibold ms-auto" id="btnNext" onclick="moveStep(1);">
                                अगला चरण / Next <i class="bi bi-chevron-right"></i>
                            </button>
                            <button type="submit" class="btn btn-success shadow-sm rounded-pill px-4 py-2 fw-semibold d-none" id="btnSubmit">
                                <i class="bi bi-check-circle-fill me-1"></i> आवेदन जमा करें / Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Stepper Navigation & Preview Script -->
<script>
let currentStep = 1;
const totalSteps = 4;

function moveStep(direction) {
    if (direction === 1 && !validateCurrentStep()) {
        return; // Validation failed, do not progress
    }
    
    // Deactivate current view & stepper label
    document.getElementById(`step${currentStep}`).classList.remove('active');
    document.querySelector(`.tsp-step-item[data-step="${currentStep}"]`).classList.remove('active');
    if (direction === 1) {
        document.querySelector(`.tsp-step-item[data-step="${currentStep}"]`).classList.add('completed');
    }
    
    currentStep += direction;
    
    // Activate new view & stepper label
    document.getElementById(`step${currentStep}`).classList.add('active');
    document.querySelector(`.tsp-step-item[data-step="${currentStep}"]`).classList.add('active');
    
    // If going backwards, remove completed status
    if (direction === -1) {
        document.querySelector(`.tsp-step-item[data-step="${currentStep}"]`).classList.remove('completed');
    }

    // Toggle button visibilities
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnCancel = document.getElementById('btnCancel');
    
    // Prev button display state
    if (currentStep > 1) {
        btnPrev.classList.remove('d-none');
        btnCancel.classList.add('d-none');
    } else {
        btnPrev.classList.add('d-none');
        btnCancel.classList.remove('d-none');
    }

    // Next vs Submit display state
    if (currentStep === totalSteps) {
        btnNext.classList.add('d-none');
        btnSubmit.classList.remove('d-none');
        compileFormPreview(); // Compile inputs to formal layout on preview step
    } else {
        btnNext.classList.remove('d-none');
        btnSubmit.classList.add('d-none');
    }

    // Scroll back to stepper top for good usability
    document.getElementById('formStepper').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function validateCurrentStep() {
    const activeContainer = document.getElementById(`step${currentStep}`);
    const requiredInputs = activeContainer.querySelectorAll('[required]');
    let isValid = true;
    
    // Remove previous validation alert if any
    const existingAlert = activeContainer.querySelector('.wizard-validation-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    for (let input of requiredInputs) {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    }
    
    // Extra percentage range validation on Step 2
    if (currentStep === 2) {
        const pctInput = document.getElementById('field_percentage');
        if (pctInput) {
            const pct = parseFloat(pctInput.value);
            if (isNaN(pct) || pct < 0 || pct > 100) {
                pctInput.classList.add('is-invalid');
                isValid = false;
            }
        }
    }

    if (!isValid) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger wizard-validation-alert border-0 shadow-sm mt-3 d-flex align-items-center gap-2 small';
        alertDiv.style.borderRadius = '0.5rem';
        alertDiv.innerHTML = `<i class="bi bi-exclamation-triangle-fill fs-5"></i> <span>कृपया सभी आवश्यक जानकारी (*) दर्ज करें।</span>`;
        activeContainer.appendChild(alertDiv);
    }
    
    return isValid;
}

function compileFormPreview() {
    // Collect values from form fields
    const firstName = document.getElementById('field_first_name').value.trim();
    const lastName = document.getElementById('field_last_name').value.trim();
    const fatherName = document.getElementById('field_father_name').value.trim();
    const motherName = document.getElementById('field_mother_name').value.trim();
    const dob = document.getElementById('field_dob').value.trim();
    const gender = document.getElementById('field_gender').value;
    const mobile = document.getElementById('field_mobile').value;
    const email = document.getElementById('field_email').value;
    const address = document.getElementById('field_address').value.trim();
    const city = document.getElementById('field_city').value.trim();
    const district = document.getElementById('field_district').value.trim();
    const pincode = document.getElementById('field_pincode').value.trim();
    
    const classYear = document.getElementById('field_class_year').value;
    const percentage = document.getElementById('field_percentage').value.trim();
    const marksObtained = document.getElementById('field_marks_obtained').value.trim();
    const maxMarks = document.getElementById('field_max_marks').value.trim();
    const collegeName = document.getElementById('field_college_name').value.trim();
    const boardUniversity = document.getElementById('field_board_university').value.trim();
    
    const bankName = document.getElementById('field_bank_name').value.trim();
    const accountNumber = document.getElementById('field_account_number').value.trim();
    const ifscCode = document.getElementById('field_ifsc_code').value.trim();
    const familyIncome = document.getElementById('field_family_income').value.trim();

    // Map values into preview sheet
    document.getElementById('preview_name').textContent = `${firstName} ${lastName}`;
    document.getElementById('preview_father_name').textContent = fatherName;
    document.getElementById('preview_mother_name').textContent = motherName;
    document.getElementById('preview_gender').textContent = gender === 'Male' ? 'पुरुष (Male)' : (gender === 'Female' ? 'महिला (Female)' : 'अन्य (Other)');
    
    // Formatting date
    if (dob) {
        const d = new Date(dob);
        document.getElementById('preview_dob').textContent = d.toLocaleDateString('hi-IN', { day: '2-digit', month: 'long', year: 'numeric' });
    } else {
        document.getElementById('preview_dob').textContent = '-';
    }

    document.getElementById('preview_mobile').textContent = mobile;
    document.getElementById('preview_email').textContent = email;
    document.getElementById('preview_address').textContent = address;
    document.getElementById('preview_city').textContent = city;
    document.getElementById('preview_district_pincode').textContent = `${district} - ${pincode}`;
    
    document.getElementById('preview_class_year').textContent = classYear;
    document.getElementById('preview_percentage').textContent = `${percentage}%`;
    document.getElementById('preview_marks').textContent = (marksObtained && maxMarks) ? `${marksObtained} / ${maxMarks}` : '-';
    document.getElementById('preview_college').textContent = collegeName || '-';
    document.getElementById('preview_board').textContent = boardUniversity || '-';
    
    document.getElementById('preview_bank_name').textContent = bankName;
    document.getElementById('preview_account_number').textContent = accountNumber;
    document.getElementById('preview_ifsc_code').textContent = ifscCode;
    document.getElementById('preview_family_income').textContent = familyIncome ? `₹ ${parseFloat(familyIncome).toLocaleString('en-IN')}/-` : '-';

    // File Preview using FileReader for Student Photo
    const filePhoto = document.getElementById('file_photo').files[0];
    const previewPhotoBox = document.getElementById('preview_photo_box');
    if (filePhoto) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewPhotoBox.innerHTML = `<img src="${e.target.result}" alt="Student Photo">`;
        };
        reader.readAsDataURL(filePhoto);
    } else {
        <?php if ($photoDoc): ?>
            previewPhotoBox.innerHTML = `<img src="/uploads/applications/<?= $application['id'] ?>/<?= $photoDoc['stored_name'] ?>" alt="Student Photo">`;
        <?php else: ?>
            previewPhotoBox.innerHTML = 'फोटो<br>Photo';
        <?php endif; ?>
    }

    // File Preview using FileReader for Signature
    const fileSignature = document.getElementById('file_signature').files[0];
    const previewSignatureBox = document.getElementById('preview_signature_box');
    if (fileSignature) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewSignatureBox.innerHTML = `<img src="${e.target.result}" alt="Student Signature">`;
        };
        reader.readAsDataURL(fileSignature);
    } else {
        <?php if ($signatureDoc): ?>
            previewSignatureBox.innerHTML = `<img src="/uploads/applications/<?= $application['id'] ?>/<?= $signatureDoc['stored_name'] ?>" alt="Student Signature">`;
        <?php else: ?>
            previewSignatureBox.innerHTML = 'हस्ताक्षर<br>Signature';
        <?php endif; ?>
    }

    // File Preview for Marksheet
    const fileMarksheet = document.getElementById('file_marksheet').files[0];
    const previewMarksheetBox = document.getElementById('preview_marksheet_box');
    if (fileMarksheet) {
        if (fileMarksheet.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewMarksheetBox.innerHTML = `<img src="${e.target.result}" style="max-height: 120px; max-width: 100%; object-fit: contain;">`;
            };
            reader.readAsDataURL(fileMarksheet);
        } else {
            previewMarksheetBox.innerHTML = `<div class="py-2 text-center"><i class="bi bi-file-earmark-pdf fs-2 text-danger"></i><div class="mt-1 small text-truncate" style="max-width: 150px;">${fileMarksheet.name}</div></div>`;
        }
    } else {
        <?php if ($marksheetDoc): ?>
            <?php $isPdf = strtolower(pathinfo($marksheetDoc['stored_name'], PATHINFO_EXTENSION)) === 'pdf'; ?>
            <?php if ($isPdf): ?>
                previewMarksheetBox.innerHTML = `<div class="py-2 text-center"><i class="bi bi-file-earmark-pdf fs-2 text-danger"></i><div class="mt-1 small"><a href="/uploads/applications/<?= $application['id'] ?>/<?= $marksheetDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline text-primary">PDF View</a></div></div>`;
            <?php else: ?>
                previewMarksheetBox.innerHTML = `<img src="/uploads/applications/<?= $application['id'] ?>/<?= $marksheetDoc['stored_name'] ?>" style="max-height: 120px; max-width: 100%; object-fit: contain;">`;
            <?php endif; ?>
        <?php else: ?>
            previewMarksheetBox.innerHTML = '<span class="text-muted">अंकतालिका उपलब्ध नहीं है / No Marksheet</span>';
        <?php endif; ?>
    }

    // File Preview for Passbook
    const filePassbook = document.getElementById('file_passbook').files[0];
    const previewPassbookBox = document.getElementById('preview_passbook_box');
    if (filePassbook) {
        if (filePassbook.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewPassbookBox.innerHTML = `<img src="${e.target.result}" style="max-height: 120px; max-width: 100%; object-fit: contain;">`;
            };
            reader.readAsDataURL(filePassbook);
        } else {
            previewPassbookBox.innerHTML = `<div class="py-2 text-center"><i class="bi bi-file-earmark-pdf fs-2 text-danger"></i><div class="mt-1 small text-truncate" style="max-width: 150px;">${filePassbook.name}</div></div>`;
        }
    } else {
        <?php if ($passbookDoc): ?>
            <?php $isPdf = strtolower(pathinfo($passbookDoc['stored_name'], PATHINFO_EXTENSION)) === 'pdf'; ?>
            <?php if ($isPdf): ?>
                previewPassbookBox.innerHTML = `<div class="py-2 text-center"><i class="bi bi-file-earmark-pdf fs-2 text-danger"></i><div class="mt-1 small"><a href="/uploads/applications/<?= $application['id'] ?>/<?= $passbookDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline text-primary">PDF View</a></div></div>`;
            <?php else: ?>
                previewPassbookBox.innerHTML = `<img src="/uploads/applications/<?= $application['id'] ?>/<?= $passbookDoc['stored_name'] ?>" style="max-height: 120px; max-width: 100%; object-fit: contain;">`;
            <?php endif; ?>
        <?php else: ?>
            previewPassbookBox.innerHTML = '<span class="text-muted">बैंक पासबुक उपलब्ध नहीं है / No Passbook</span>';
        <?php endif; ?>
    }
}
}
</script>

<!-- Responsive Sidebar toggle control -->
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

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

?>
<!-- Back button & session indicator -->
<div class="mb-4 d-flex justify-content-between align-items-center no-print back-link">
                <a href="/dashboard/applications/create" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
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
<div class="tsp-stepper stepper no-print" id="formStepper">
                <div class="tsp-step-item <?= $step === 1 ? 'active' : ($step > 1 ? 'completed' : '') ?>" data-step="1">
                    <div class="tsp-step-circle">1</div>
                    <div class="tsp-step-label">व्यक्तिगत विवरण<br><small class="text-muted d-none d-md-inline">Profile</small></div>
                </div>
                <div class="tsp-step-item <?= $step === 2 ? 'active' : ($step > 2 ? 'completed' : '') ?>" data-step="2">
                    <div class="tsp-step-circle">2</div>
                    <div class="tsp-step-label">शैक्षणिक व बैंक<br><small class="text-muted d-none d-md-inline">Academic & Bank</small></div>
                </div>
                <div class="tsp-step-item <?= $step === 3 ? 'active' : ($step > 3 ? 'completed' : '') ?>" data-step="3">
                    <div class="tsp-step-circle">3</div>
                    <div class="tsp-step-label">दस्तावेज़ अपलोड<br><small class="text-muted d-none d-md-inline">Uploads</small></div>
                </div>
                <div class="tsp-step-item <?= $step === 4 ? 'active' : ($step > 4 ? 'completed' : '') ?>" data-step="4">
                    <div class="tsp-step-circle">4</div>
                    <div class="tsp-step-label">पूर्वावलोकन<br><small class="text-muted d-none d-md-inline">Form Preview</small></div>
                </div>
            </div>

            <!-- Interactive Form Wizard Wrapper -->
            <div class="card border-0 shadow-sm" style="border-radius: 1.25rem;">
                <div class="card-body p-4 p-md-5">
                    <form action="/dashboard/applications/step/<?= $step ?>" method="POST" enctype="multipart/form-data" id="scholarshipWizardForm">
                        <?= Csrf::field() ?>

                        <!-- STEP 1: Personal & Family Information -->
                        <div class="tsp-form-step <?= $step === 1 ? 'active' : '' ?>" id="step1">
                            <h4 class="h5 fw-bold mb-4 text-dark border-bottom pb-2">
                                <i class="bi bi-person-fill text-muted me-2"></i> 1. व्यक्तिगत एवं पारिवारिक विवरण / Personal & Family Details
                            </h4>
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">प्रथम नाम (First Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="field_first_name" class="form-control border-2 py-2" required
                                           value="<?= Helpers::esc($old['first_name'] ?? ($isEdit ? $application['first_name'] : $student['first_name']) ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">अंतिम नाम (Last Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="field_last_name" class="form-control border-2 py-2" required
                                           value="<?= Helpers::esc($old['last_name'] ?? ($isEdit ? $application['last_name'] : $student['last_name']) ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">पिता का नाम (Father Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="father_name" id="field_father_name" class="form-control border-2 py-2" required
                                           value="<?= Helpers::esc($old['father_name'] ?? ($isEdit ? $application['father_name'] : $student['father_name']) ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">माता का नाम (Mother Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="mother_name" id="field_mother_name" class="form-control border-2 py-2" required
                                           value="<?= Helpers::esc($old['mother_name'] ?? ($isEdit ? $application['mother_name'] : $student['mother_name']) ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">जन्म तिथि (Date of Birth) <span class="text-danger">*</span></label>
                                    <input type="date" name="dob" id="field_dob" class="form-control border-2 py-2" required max="<?= date('Y-m-d') ?>"
                                           value="<?= Helpers::esc($old['dob'] ?? ($isEdit ? $application['dob'] : $student['dob']) ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">लिंग (Gender) <span class="text-danger">*</span></label>
                                    <select name="gender" id="field_gender" class="form-select border-2 py-2" required>
                                        <option value="">Select</option>
                                        <option value="Male" <?= ($old['gender'] ?? ($isEdit ? $application['gender'] : $student['gender']) ?? '') === 'Male' ? 'selected' : '' ?>>पुरुष (Male)</option>
                                        <option value="Female" <?= ($old['gender'] ?? ($isEdit ? $application['gender'] : $student['gender']) ?? '') === 'Female' ? 'selected' : '' ?>>महिला (Female)</option>
                                        <option value="Other" <?= ($old['gender'] ?? ($isEdit ? $application['gender'] : $student['gender']) ?? '') === 'Other' ? 'selected' : '' ?>>अन्य (Other)</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">मोबाइल नंबर (Mobile) <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile" id="field_mobile" class="form-control border-2 py-2 bg-light" readonly
                                           value="<?= Helpers::esc($student['mobile'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">ईमेल (Email) <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="field_email" class="form-control border-2 py-2 bg-light" readonly
                                           value="<?= Helpers::esc($student['email'] ?? '') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-muted">स्थाई पता (Permanent Address) <span class="text-danger">*</span></label>
                                    <textarea name="address" id="field_address" class="form-control border-2 py-2" rows="3" required><?= Helpers::esc($old['address'] ?? ($isEdit ? $application['address'] : $student['address']) ?? '') ?></textarea>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small fw-semibold text-muted">शहर/कस्बा (City) <span class="text-danger">*</span></label>
                                    <input type="text" name="city" id="field_city" class="form-control border-2 py-2" required
                                           value="<?= Helpers::esc($old['city'] ?? ($isEdit ? $application['city'] : $student['city']) ?? '') ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small fw-semibold text-muted">जिला (District) <span class="text-danger">*</span></label>
                                    <input type="text" name="district" id="field_district" class="form-control border-2 py-2" required
                                           value="<?= Helpers::esc($old['district'] ?? ($isEdit ? $application['district'] : $student['district']) ?? '') ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small fw-semibold text-muted">पिनकोड (PIN Code) <span class="text-danger">*</span></label>
                                    <input type="text" name="pincode" id="field_pincode" class="form-control border-2 py-2" required pattern="\d{6}" inputmode="numeric" maxlength="6"
                                           value="<?= Helpers::esc($old['pincode'] ?? ($isEdit ? $application['pincode'] : $student['pincode']) ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">परिवार का व्यवसाय/आजीविका का साधन (Family Occupation) <span class="text-danger">*</span></label>
                                    <input type="text" name="family_occupation" id="field_family_occupation" class="form-control border-2 py-2" required
                                           value="<?= Helpers::esc($old['family_occupation'] ?? $application['family_occupation'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">भविष्य में आप क्या बनना चाहते हैं (Career Goal) <span class="text-danger">*</span></label>
                                    <input type="text" name="career_goal" id="field_career_goal" class="form-control border-2 py-2" required
                                           value="<?= Helpers::esc($old['career_goal'] ?? $application['career_goal'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">परिवार में कुल सदस्य (Total Family Members) <span class="text-danger">*</span></label>
                                    <input type="number" name="family_members_count" id="field_family_members_count" class="form-control border-2 py-2" min="1" max="30" required
                                           value="<?= Helpers::esc($old['family_members_count'] ?? $application['family_members_count'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">परिवार में कमाने वाले सदस्यों की संख्या (Earning Members) <span class="text-danger">*</span></label>
                                    <input type="number" name="earning_members_count" id="field_earning_members_count" class="form-control border-2 py-2" min="0" max="30" required
                                           value="<?= Helpers::esc($old['earning_members_count'] ?? $application['earning_members_count'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: Academic & Bank Details -->
                        <div class="tsp-form-step <?= $step === 2 ? 'active' : '' ?>" id="step2">
                            <h4 class="h5 fw-bold mb-4 text-dark border-bottom pb-2">
                                <i class="bi bi-book-half text-muted me-2"></i> 2. शैक्षणिक एवं बैंक खाता विवरण / Academic & Bank Details
                            </h4>
                            
                            <h5 class="h6 fw-bold mb-3 text-secondary">शैक्षणिक जानकारी / Academic Records</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">कक्षा / वर्ष (Class/Year) <span class="text-danger">*</span></label>
                                    <select name="class_year" id="field_class_year" class="form-select border-2 py-2" required>
                                        <option value="">कक्षा चुनें / Select</option>
                                        <?php foreach (['10th', '12th', 'Graduation', 'Post Graduation'] as $cy): ?>
                                            <option value="<?= $cy ?>" <?= ($old['class_year'] ?? $application['class_year'] ?? '') === $cy ? 'selected' : '' ?>><?= $cy ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">प्राप्त प्रतिशत (Percentage) <span class="text-danger">*</span></label>
                                    <input type="number" name="percentage" id="field_percentage" class="form-control border-2 py-2" step="0.01" min="0" max="100" placeholder="उदा. 75.00" required
                                           value="<?= Helpers::esc($old['percentage'] ?? $application['percentage'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">प्राप्त अंक (Marks Obtained)</label>
                                    <input type="number" name="marks_obtained" id="field_marks_obtained" class="form-control border-2 py-2" min="0" max="10000" placeholder="प्राप्त अंक"
                                           value="<?= Helpers::esc($old['marks_obtained'] ?? $application['marks_obtained'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">कुल पूर्णांक (Max Marks)</label>
                                    <input type="number" name="max_marks" id="field_max_marks" class="form-control border-2 py-2" min="1" max="10000" placeholder="कुल पूर्णांक"
                                           value="<?= Helpers::esc($old['max_marks'] ?? $application['max_marks'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">विद्यालय / महाविद्यालय (College/School Name)</label>
                                    <input type="text" name="college_name" id="field_college_name" class="form-control border-2 py-2" placeholder="विद्यालय/महाविद्यालय"
                                           value="<?= Helpers::esc($old['college_name'] ?? $application['college_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">बोर्ड / विश्वविद्यालय (Board/University)</label>
                                    <input type="text" name="board_university" id="field_board_university" class="form-control border-2 py-2" placeholder="उदा. RBSE, CBSE"
                                           value="<?= Helpers::esc($old['board_university'] ?? $application['board_university'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">वर्तमान में अध्ययनरत कक्षा (Current Class) <span class="text-danger">*</span></label>
                                    <input type="text" name="current_class" id="field_current_class" class="form-control border-2 py-2" placeholder="उदा. 11th, B.Sc. 1st Year" required
                                           value="<?= Helpers::esc($old['current_class'] ?? $application['current_class'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">वर्तमान में अध्ययनरत विद्यालय/महाविद्यालय का नाम (Current Institution) <span class="text-danger">*</span></label>
                                    <input type="text" name="current_college" id="field_current_college" class="form-control border-2 py-2" placeholder="विद्यालय/महाविद्यालय का नाम" required
                                           value="<?= Helpers::esc($old['current_college'] ?? $application['current_college'] ?? '') ?>">
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="h6 fw-bold mb-3 text-secondary">संस्था से छात्रवृत्ति की जानकारी / Previous Scholarship Details</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-muted d-block">संस्था से पिछले वर्षों में छात्रवृत्ति प्राप्त हुई है? (Received scholarship in previous years?) <span class="text-danger">*</span></label>
                                    <?php $prevSch = $old['prev_scholarship_received'] ?? $application['prev_scholarship_received'] ?? 'नहीं'; ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="prev_scholarship_received" id="prev_sch_yes" value="हाँ" <?= $prevSch === 'हाँ' ? 'checked' : '' ?> onchange="togglePrevSchAmt(true);">
                                        <label class="form-check-label" for="prev_sch_yes">हाँ / Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="prev_scholarship_received" id="prev_sch_no" value="नहीं" <?= $prevSch !== 'हाँ' ? 'checked' : '' ?> onchange="togglePrevSchAmt(false);">
                                        <label class="form-check-label" for="prev_sch_no">नहीं / No</label>
                                    </div>
                                </div>
                                <div class="col-12 <?= $prevSch === 'हाँ' ? '' : 'd-none' ?>" id="prev_sch_amounts_div">
                                    <div class="row g-3">
                                        <div class="col-sm-4">
                                            <label class="form-label small fw-semibold text-muted">वर्ष 2023-24 में प्राप्त राशि (Amt in 2023-24)</label>
                                            <input type="number" name="scholarship_amt_2023_24" id="field_scholarship_amt_2023_24" class="form-control border-2 py-2" placeholder="उदा. 1500"
                                                   value="<?= Helpers::esc($old['scholarship_amt_2023_24'] ?? $application['scholarship_amt_2023_24'] ?? '') ?>">
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label small fw-semibold text-muted">वर्ष 2024-25 में प्राप्त राशि (Amt in 2024-25)</label>
                                            <input type="number" name="scholarship_amt_2024_25" id="field_scholarship_amt_2024_25" class="form-control border-2 py-2" placeholder="उदा. 2000"
                                                   value="<?= Helpers::esc($old['scholarship_amt_2024_25'] ?? $application['scholarship_amt_2024_25'] ?? '') ?>">
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label small fw-semibold text-muted">वर्ष 2025-26 में प्राप्त राशि (Amt in 2025-26)</label>
                                            <input type="number" name="scholarship_amt_2025_26" id="field_scholarship_amt_2025_26" class="form-control border-2 py-2" placeholder="उदा. 2500"
                                                   value="<?= Helpers::esc($old['scholarship_amt_2025_26'] ?? $application['scholarship_amt_2025_26'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="h6 fw-bold mb-3 text-secondary">बैंक खाता विवरण / Bank Account</h5>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">बैंक का नाम (Bank Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" id="field_bank_name" class="form-control border-2 py-2" placeholder="बैंक का नाम" required
                                           value="<?= Helpers::esc($old['bank_name'] ?? $application['bank_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">खाता संख्या (Account Number) <span class="text-danger">*</span></label>
                                    <input type="text" name="account_number" id="field_account_number" class="form-control border-2 py-2" placeholder="खाता नंबर" required
                                           value="<?= Helpers::esc($old['account_number'] ?? $application['account_number'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">खाता संख्या पुनः दर्ज करें (Confirm Account Number) <span class="text-danger">*</span></label>
                                    <input type="text" name="confirm_account_number" id="field_confirm_account_number" class="form-control border-2 py-2" placeholder="पुनः खाता नंबर" required
                                           value="<?= Helpers::esc($old['account_number'] ?? $application['account_number'] ?? '') ?>">
                                </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">खाता धारक का नाम (Account Holder Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="account_holder_name" id="field_account_holder_name" class="form-control border-2 py-2" placeholder="खाता धारक का नाम" required
                                           value="<?= Helpers::esc($old['account_holder_name'] ?? $application['account_holder_name'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">IFSC कोड (IFSC Code) <span class="text-danger">*</span></label>
                                    <input type="text" name="ifsc_code" id="field_ifsc_code" class="form-control border-2 py-2" placeholder="IFSC कोड" required
                                           value="<?= Helpers::esc($old['ifsc_code'] ?? $application['ifsc_code'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">वार्षिक पारिवारिक आय (Annual Family Income)</label>
                                    <input type="number" name="family_income" id="field_family_income" class="form-control border-2 py-2" placeholder="उदा. 180000"
                                           value="<?= Helpers::esc($old['family_income'] ?? $application['family_income'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: Required Upload Documents -->
                        <div class="tsp-form-step <?= $step === 3 ? 'active' : '' ?>" id="step3">
                            <h4 class="h5 fw-bold mb-4 text-dark border-bottom pb-2">
                                <i class="bi bi-file-earmark-arrow-up text-muted me-2"></i> 3. आवश्यक दस्तावेज़ अपलोड / Upload Documents
                            </h4>
                            
                            <div class="row g-4">
                                <!-- Marksheet -->
                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm doc-card" id="card_marksheet" data-type="Marksheet" data-field="marksheet" data-uploaded="<?= $marksheetDoc ? 'true' : '' ?>">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">अंकतालिका अपलोड करें (Marksheet) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="file" id="file_marksheet" class="form-control file-input-field" accept=".jpg,.jpeg,.png,.pdf">
                                            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 btn-upload-doc d-none" onclick="uploadDocAjax('Marksheet', 'file_marksheet');">
                                                <i class="bi bi-cloud-arrow-up-fill"></i> अपलोड / Upload
                                            </button>
                                        </div>
                                        <div class="form-text text-muted small mt-1">पिछले वर्ष की अंकतालिका (JPG, PNG, PDF | अधिकतम: 2MB)</div>
                                        <div class="doc-status-container mt-2">
                                            <?php if ($marksheetDoc): ?>
                                                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                                    <span class="text-success small fw-semibold">
                                                        <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                        <a href="/uploads/applications/<?= $application['id'] ?>/<?= $marksheetDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline"><?= Helpers::esc(Helpers::limitString($marksheetDoc['original_name'], 25)) ?></a>
                                                    </span>
                                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="deleteDocAjax('Marksheet', 'file_marksheet');">
                                                        <i class="bi bi-trash-fill"></i> हटाएं / Remove
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark font-monospace small py-1 px-2 mt-1">दस्तावेज़ आवश्यक है / Required</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Passbook -->
                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm doc-card" id="card_passbook" data-type="Passbook" data-field="passbook" data-uploaded="<?= $passbookDoc ? 'true' : '' ?>">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">बैंक पासबुक अपलोड करें (Bank Passbook) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="file" id="file_passbook" class="form-control file-input-field" accept=".jpg,.jpeg,.png,.pdf">
                                            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 btn-upload-doc d-none" onclick="uploadDocAjax('Passbook', 'file_passbook');">
                                                <i class="bi bi-cloud-arrow-up-fill"></i> अपलोड / Upload
                                            </button>
                                        </div>
                                        <div class="form-text text-muted small mt-1">खाता संख्या एवं IFSC दर्शाने वाला पृष्ठ (JPG, PNG, PDF | अधिकतम: 2MB)</div>
                                        <div class="doc-status-container mt-2">
                                            <?php if ($passbookDoc): ?>
                                                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                                    <span class="text-success small fw-semibold">
                                                        <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                        <a href="/uploads/applications/<?= $application['id'] ?>/<?= $passbookDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline"><?= Helpers::esc(Helpers::limitString($passbookDoc['original_name'], 25)) ?></a>
                                                    </span>
                                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="deleteDocAjax('Passbook', 'file_passbook');">
                                                        <i class="bi bi-trash-fill"></i> हटाएं / Remove
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark font-monospace small py-1 px-2 mt-1">दस्तावेज़ आवश्यक है / Required</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo -->
                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm doc-card" id="card_photo" data-type="Photo" data-field="photo" data-uploaded="<?= $photoDoc ? 'true' : '' ?>">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">पासपोर्ट साइज फोटो (Student Photo) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="file" id="file_photo" class="form-control file-input-field" accept=".jpg,.jpeg,.png">
                                            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 btn-upload-doc d-none" onclick="uploadDocAjax('Photo', 'file_photo');">
                                                <i class="bi bi-cloud-arrow-up-fill"></i> अपलोड / Upload
                                            </button>
                                        </div>
                                        <div class="form-text text-muted small mt-1">हाल ही की रंगीन पासपोर्ट फोटो (केवल JPG, PNG | अधिकतम: 1MB)</div>
                                        <div class="doc-status-container mt-2">
                                            <?php if ($photoDoc): ?>
                                                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                                    <span class="text-success small fw-semibold">
                                                        <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                        <a href="/uploads/applications/<?= $application['id'] ?>/<?= $photoDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline"><?= Helpers::esc(Helpers::limitString($photoDoc['original_name'], 25)) ?></a>
                                                    </span>
                                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="deleteDocAjax('Photo', 'file_photo');">
                                                        <i class="bi bi-trash-fill"></i> हटाएं / Remove
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark font-monospace small py-1 px-2 mt-1">दस्तावेज़ आवश्यक है / Required</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Signature -->
                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm doc-card" id="card_signature" data-type="Signature" data-field="signature" data-uploaded="<?= $signatureDoc ? 'true' : '' ?>">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">विद्यार्थी के हस्ताक्षर (Student Signature) <span class="text-danger">*</span></label>
                                 <!-- STEP 4: Offline-Style Formal Preview Sheet -->
                        <div class="tsp-form-step <?= $step === 4 ? 'active' : '' ?>" id="step4">
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
                            <div id="printableForm" class="p-3 border rounded bg-white">
                                <!-- Print Header -->
                                <div class="print-header text-center position-relative mb-4">
                                    <div class="print-logo-wrapper text-center mb-2">
                                        <img src="<?= \App\Core\Url::asset('images/logo/logo-placeholder.svg') ?>" alt="Tamboli Samaj" class="print-logo" width="60" height="60">
                                    </div>
                                    <h2 class="print-org-title text-center mb-1">तम्बोली समाज विकास संस्था, राजस्थान</h2>
                                    <div class="print-reg-no text-center fw-bold small mb-1">रजि.नं. 411 / 2016-17</div>
                                    <div class="print-office-address text-center small mb-1">कार्यालय: 132, जनकपुरी-2, इमलीफाटक, जयपुर (राज.)-302005</div>
                                    <div class="print-contact text-center small mb-2">मो. 9829714778, 9414728866 ई मेल : tambolisamaj@gmail.com</div>
                                    <div class="print-form-title-underlined text-center fw-bold fs-5 border-top border-bottom py-2">
                                        सामाजिक छात्रवृत्ति हेतु आवेदन - <?= Helpers::esc($activeSession['session_name'] ?? '2026') ?>
                                    </div>
                                </div>

                                <div class="print-form-fields mt-4">
                                    <!-- Section 1 Header with Edit Link -->
                                    <div class="d-flex justify-content-between align-items-center bg-light p-2 mb-3 border no-print">
                                        <span class="mb-0 text-dark fw-bold" style="font-size: 14px;"><i class="bi bi-person-circle text-muted me-1"></i> 1. व्यक्तिगत एवं पारिवारिक विवरण / Personal & Family Details</span>
                                        <a href="?step=1" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 12px;"><i class="bi bi-pencil-square"></i> सुधारें / Edit</a>
                                    </div>

                                    <!-- Photo & Profile Row -->
                                    <div class="row mb-3">
                                        <div class="col-8 col-sm-9">
                                            <div class="print-field-row mb-3">
                                                <span class="print-field-label">विद्यार्थी का नाम (Student Name):</span>
                                                <span class="print-field-value" id="preview_name"></span>
                                            </div>
                                            <div class="print-field-row mb-3">
                                                <span class="print-field-label">पिता / संरक्षक का नाम (Father/Guardian Name):</span>
                                                <span class="print-field-value" id="preview_father_name"></span>
                                            </div>
                                            <div class="print-field-row mb-3">
                                                <span class="print-field-label">माता का नाम (Mother Name):</span>
                                                <span class="print-field-value" id="preview_mother_name"></span>
                                            </div>
                                            <div class="print-field-row mb-3">
                                                <span class="print-field-label">वर्तमान स्थायी पता (Permanent Address):</span>
                                                <span class="print-field-value" id="preview_address"></span>
                                            </div>
                                        </div>
                                        <div class="col-4 col-sm-3 d-flex justify-content-end align-items-start">
                                            <div class="print-photo-box-top-right border border-dark border-2" id="preview_photo_box" style="width: 120px; height: 150px; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 13px; font-weight: bold; overflow: hidden; position: static;">
                                                विद्यार्थी का<br>फोटो
                                            </div>
                                        </div>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">लिंग (Gender):</span>
                                        <span class="print-field-value" id="preview_gender"></span>
                                        <span class="print-field-label ms-3">जन्म तिथि (Date of Birth):</span>
                                        <span class="print-field-value" id="preview_dob"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">मोबाइल नंबर (Mobile Number):</span>
                                        <span class="print-field-value" id="preview_mobile"></span>
                                        <span class="print-field-label ms-3">ईमेल (Email):</span>
                                        <span class="print-field-value" id="preview_email"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">परिवार का व्यवसाय/आजीविका का साधन (Family Occupation):</span>
                                        <span class="print-field-value" id="preview_family_occupation"></span>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">परिवार में कुल सदस्य (Total Family Members):</span>
                                                <span class="print-field-value" id="preview_family_members_count"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">परिवार में कमाने वाले सदस्यों की संख्या (Earning Members):</span>
                                                <span class="print-field-value" id="preview_earning_members_count"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section 2 Header with Edit Link -->
                                    <div class="d-flex justify-content-between align-items-center bg-light p-2 mb-3 border mt-4 no-print">
                                        <span class="mb-0 text-dark fw-bold" style="font-size: 14px;"><i class="bi bi-book-half text-muted me-1"></i> 2. शैक्षणिक एवं बैंक खाता विवरण / Academic & Bank Details</span>
                                        <a href="?step=2" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 12px;"><i class="bi bi-pencil-square"></i> सुधारें / Edit</a>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">वर्ष <?= Helpers::esc($activeSession['session_name'] ?? '2026') ?> में उत्तीर्ण कक्षा (Passed Class):</span>
                                                <span class="print-field-value" id="preview_class_year"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">परीक्षा परिणाम प्रतिशत (Percentage):</span>
                                                <span class="print-field-value" id="preview_percentage"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">प्राप्त अंक / कुल पूर्णांक (Marks Obtained/Max Marks):</span>
                                        <span class="print-field-value" id="preview_marks"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">विद्यालय / महाविद्यालय का नाम (Previous School/College Name):</span>
                                        <span class="print-field-value" id="preview_college"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">बोर्ड / विश्वविद्यालय (Board/University):</span>
                                        <span class="print-field-value" id="preview_board"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">वर्तमान में अध्ययनरत कक्षा (Current Class):</span>
                                        <span class="print-field-value" id="preview_current_class"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">वर्तमान में अध्ययनरत विद्यालय/महाविद्यालय का नाम (Current Institution):</span>
                                        <span class="print-field-value" id="preview_current_college"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">संस्था से पिछले वर्षों में छात्रवृत्ति प्राप्त हुई है (Received scholarship in previous years?):</span>
                                        <span class="print-field-value" id="preview_prev_scholarship_received"></span>
                                    </div>

                                    <div class="print-prev-sch-amt-container mb-3 d-none" id="preview_prev_sch_amounts_row">
                                        <div class="row g-2">
                                            <div class="col-sm-4">
                                                <div class="print-field-row">
                                                    <span class="print-field-label">वर्ष 2023-24 में प्राप्त राशि:</span>
                                                    <span class="print-field-value" id="preview_sch_amt_2023_24"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="print-field-row">
                                                    <span class="print-field-label">2024-25 में प्राप्त राशि:</span>
                                                    <span class="print-field-value" id="preview_sch_amt_2024_25"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="print-field-row">
                                                    <span class="print-field-label">2025-26 में प्राप्त राशि:</span>
                                                    <span class="print-field-value" id="preview_sch_amt_2025_26"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">बैंक का नाम (Bank Name):</span>
                                                <span class="print-field-value" id="preview_bank_name"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">IFSC कोड (IFSC Code):</span>
                                                <span class="print-field-value" id="preview_ifsc_code"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">खाता संख्या (Account Number):</span>
                                        <span class="print-field-value" id="preview_account_number"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">खाता धारक का नाम (Account Holder Name):</span>
                                        <span class="print-field-value" id="preview_account_holder_name"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">वार्षिक पारिवारिक आय (Annual Family Income):</span>
                                        <span class="print-field-value" id="preview_family_income"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">भविष्य में आप क्या बनना चाहते हैं (Career Goal):</span>
                                        <span class="print-field-value" id="preview_career_goal"></span>
                                    </div>
                                </div>

                                <!-- Section 3 Header with Edit Link -->
                                <div class="d-flex justify-content-between align-items-center bg-light p-2 mb-3 border mt-4 no-print">
                                    <span class="mb-0 text-dark fw-bold" style="font-size: 14px;"><i class="bi bi-file-earmark-arrow-up text-muted me-1"></i> 3. संलग्न दस्तावेज़ (Attached Documents Checklist)</span>
                                    <a href="?step=3" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 12px;"><i class="bi bi-pencil-square"></i> सुधारें / Edit</a>
                                </div>

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
                                    <div class="d-flex justify-content-between align-items-end mt-4 mb-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="print-signature-box mb-2" id="preview_signature_box" style="width: 120px; height: 50px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                हस्ताक्षर / Signature
                                            </div>
                                            <div style="width: 180px; border-top: 1px solid #000; text-align: center;" class="pt-2 small fw-bold">विद्यार्थी के हस्ताक्षर</div>
                                        </div>
                                        <div class="d-flex flex-column align-items-center">
                                            <div style="height: 50px;"></div>
                                            <div style="width: 180px; border-top: 1px solid #000; text-align: center;" class="pt-2 small fw-bold">पिता / संरक्षक के हस्ताक्षर</div>
                                        </div>
                                    </div>
                                    
                                    <div style="border-top: 1px dashed #000; margin: 20px 0;"></div>
                                    
                                    <p class="mb-4 small text-dark text-center" style="font-style: italic; font-size: 14px;">
                                        विद्यार्थी की पढ़ाई अविरल चलती रहे इसके लिये संस्था द्वारा सामाजिक छात्रवृत्ति आवश्यक है। छात्रवृत्ति की अनुशंसा की जाती है।
                                    </p>
                                    
                                    <div class="d-flex justify-content-end mt-5 pt-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div style="height: 60px;"></div>
                                            <div style="width: 320px; border-top: 1px solid #000; text-align: center;" class="pt-2 small fw-bold">संस्था के छात्रवृत्ति प्रतिनिधियों के हस्ताक्षर मय नाम</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($step === 4): ?>
                        <!-- Declaration Checkbox -->
                        <div class="form-check mt-4 p-3 border rounded bg-light no-print" id="declarationBox">
                            <input class="form-check-input" type="checkbox" name="self_declared" id="declarationCheckbox" value="1" onchange="toggleSubmitBtn();">
                            <label class="form-check-label fw-semibold small text-danger" for="declarationCheckbox">
                                मैं घोषणा करता/करती हूं कि दी गई सभी जानकारी सही है। / I hereby declare that all information provided is true and correct to the best of my knowledge. I understand that any false information may result in rejection. <span class="text-danger">*</span>
                            </label>
                        </div>
                        <?php endif; ?>

                        <!-- Stepper Actions Navigation Footer -->
                        <div class="d-flex gap-2 justify-content-between mt-5 pt-3 border-top sticky-bar form-actions" id="wizardActions">
                            <input type="hidden" name="action" id="wizardAction" value="next">
                            <input type="hidden" name="application_id" value="<?= (int) ($application['id'] ?? 0) ?>">
                            <?php if ($step === 4): $submitToken = bin2hex(random_bytes(16)); \App\Core\Session::set('submit_token', $submitToken); ?>
                            <input type="hidden" name="submit_token" value="<?= $submitToken ?>">
                            <?php endif; ?>
                            
                            <?php if ($step > 1): ?>
                                <a href="?step=<?= $step - 1 ?>" class="btn btn-light rounded-pill px-4 py-2 fw-semibold" id="btnPrev">
                                    <i class="bi bi-chevron-left"></i> पिछला चरण / Previous
                                </a>
                            <?php else: ?>
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" id="btnCancel" onclick="localStorage.removeItem('scholarship_form_draft_new'); localStorage.removeItem('scholarship_form_draft_<?= (int) ($application['id'] ?? 0) ?>'); location.href='/dashboard/applications/create';">
                                    रद्द करें / Cancel
                                </button>
                            <?php endif; ?>

                            <div class="d-flex gap-2 ms-auto">
                                <?php if ($step < 4): ?>
                                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold" id="btnSaveDraft" onclick="saveDraftAction();">
                                        प्रारूप सहेजें / Save Draft
                                    </button>
                                    <button type="submit" class="btn tsp-dash-welcome-btn shadow-sm rounded-pill px-4 py-2 fw-semibold" id="btnNext">
                                        अगला चरण / Next <i class="bi bi-chevron-right"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-success shadow-sm rounded-pill px-4 py-2 fw-semibold" id="btnSubmit" onclick="confirmFinalSubmit();" disabled>
                                        <i class="bi bi-check-circle-fill me-1"></i> अंतिम सबमिट / Final Submit
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

<!-- Final Submit Confirmation Modal -->
<div class="modal fade no-print" id="finalSubmitModal" tabindex="-1" aria-labelledby="finalSubmitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="finalSubmitModalLabel">अंतिम सबमिशन की पुष्टि / Confirm Final Submit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">क्या आप सुनिश्चित हैं? सबमिशन के बाद आप इस आवेदन को संपादित नहीं कर सकते।</p>
                <p class="text-muted small mb-0">Are you sure? You will not be able to edit this application after submission.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">रद्द करें / Cancel</button>
                <button type="button" class="btn btn-success rounded-pill px-4" id="confirmSubmitBtn">
                    <i class="bi bi-check-circle-fill me-1"></i> हाँ, सबमिट करें / Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stepper Navigation & Preview Script -->
<script>
let currentStep = <?= (int) $step ?>;
const totalSteps = 4;
const applicationId = <?= (int) ($application['id'] ?? 0) ?>;

// Global map to hold uploaded document details
const uploadedDocs = {
    Marksheet: <?= json_encode($marksheetDoc) ?>,
    Passbook: <?= json_encode($passbookDoc) ?>,
    Photo: <?= json_encode($photoDoc) ?>,
    Signature: <?= json_encode($signatureDoc) ?>
};

// Monitor file inputs to show upload button when a file is selected
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.file-input-field').forEach(input => {
        input.addEventListener('change', function() {
            const btn = this.parentElement.querySelector('.btn-upload-doc');
            if (this.files && this.files.length > 0) {
                btn.classList.remove('d-none');
            } else {
                btn.classList.add('d-none');
            }
        });
    });
});

async function uploadDocAjax(docType, inputId) {
    if (!applicationId) {
        alert('कृपया पहले फॉर्म सेव करें। / Please save the form first.');
        return;
    }
    const input = document.getElementById(inputId);
    const file = input.files[0];
    if (!file) {
        alert('कृपया अपलोड करने के लिए एक फ़ाइल चुनें। / Please select a file to upload.');
        return;
    }

    const formData = new FormData();
    formData.append('document_type', docType);
    formData.append('file', file);
    
    // Add CSRF token
    const csrfToken = document.querySelector('input[name="csrf_token"]');
    if (csrfToken) {
        formData.append('csrf_token', csrfToken.value);
    }

    const card = document.getElementById(`card_${inputId.split('_')[1]}`);
    const statusContainer = card.querySelector('.doc-status-container');
    const uploadBtn = card.querySelector('.btn-upload-doc');
    
    // Show loading state
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> अपलोड हो रहा है...`;

    try {
        const response = await fetch(`/dashboard/applications/${applicationId}/upload-document`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        let result;
        const responseText = await response.text();
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Non-JSON response received:', responseText);
            let errMsg = 'Invalid response format from server.';
            if (responseText.includes('<title>')) {
                const match = responseText.match(/<title>(.*?)<\/title>/i);
                if (match && match[1]) {
                    errMsg = match[1];
                }
            } else if (responseText.trim().length > 0 && responseText.trim().length < 150) {
                errMsg = responseText.trim();
            }
            throw new Error(`${errMsg} (HTTP ${response.status})`);
        }
        
        if (result.success) {
            uploadedDocs[docType] = {
                stored_name: result.stored_name,
                original_name: result.original_name,
                url: result.url
            };
            
            card.setAttribute('data-uploaded', 'true');
            
            // Limit original name display length
            const limitName = result.original_name.length > 25 ? result.original_name.substring(0, 22) + '...' : result.original_name;

            statusContainer.innerHTML = `
                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded mt-2">
                    <span class="text-success small fw-semibold">
                        <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                        <a href="${result.url}" target="_blank" class="text-decoration-underline">${limitName}</a>
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="deleteDocAjax('${docType}', '${inputId}');">
                        <i class="bi bi-trash-fill"></i> हटाएं / Remove
                    </button>
                </div>
            `;
            
            // Clear the file input and hide upload button
            input.value = '';
            uploadBtn.classList.add('d-none');
            
            alert(`${docType} सफलतापूर्वक अपलोड हो गया है। / ${docType} uploaded successfully.`);
        } else {
            alert('त्रुटि: ' + (result.error || 'अपलोड करने में विफल।'));
        }
    } catch (error) {
        console.error('Error uploading document:', error);
        alert('नेटवर्क त्रुटि: फ़ाइल अपलोड करने में असमर्थ।\nविवरण (Detail): ' + error.message);
    } finally {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = `<i class="bi bi-cloud-arrow-up-fill"></i> अपलोड / Upload`;
    }
}

async function deleteDocAjax(docType, inputId) {
    if (!applicationId) return;
    if (!confirm('क्या आप वाकई इस दस्तावेज़ को हटाना चाहते हैं? / Are you sure you want to delete this document?')) {
        return;
    }

    const input = document.getElementById(inputId);
    const formData = new FormData();
    formData.append('document_type', docType);
    
    // Add CSRF token
    const csrfToken = document.querySelector('input[name="csrf_token"]');
    if (csrfToken) {
        formData.append('csrf_token', csrfToken.value);
    }

    const card = document.getElementById(`card_${inputId.split('_')[1]}`);
    const statusContainer = card.querySelector('.doc-status-container');
    const uploadBtn = card.querySelector('.btn-upload-doc');

    try {
        const response = await fetch(`/dashboard/applications/${applicationId}/delete-document`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        let result;
        const responseText = await response.text();
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Non-JSON response received:', responseText);
            let errMsg = 'Invalid response format from server.';
            if (responseText.includes('<title>')) {
                const match = responseText.match(/<title>(.*?)<\/title>/i);
                if (match && match[1]) {
                    errMsg = match[1];
                }
            } else if (responseText.trim().length > 0 && responseText.trim().length < 150) {
                errMsg = responseText.trim();
            }
            throw new Error(`${errMsg} (HTTP ${response.status})`);
        }
        
        if (result.success) {
            delete uploadedDocs[docType];
            card.removeAttribute('data-uploaded');
            
            statusContainer.innerHTML = `
                <span class="badge bg-warning text-dark font-monospace small py-1 px-2 mt-1">दस्तावेज़ आवश्यक है / Required</span>
            `;
            
            input.value = '';
            uploadBtn.classList.add('d-none');
            
            alert(`${docType} हटा दिया गया है। / ${docType} removed successfully.`);
        } else {
            alert('त्रुटि: ' + (result.error || 'दस्तावेज़ हटाने में विफल।'));
        }
    } catch (error) {
        console.error('Error deleting document:', error);
        alert('नेटवर्क त्रुटि: दस्तावेज़ हटाने में असमर्थ।\nविवरण (Detail): ' + error.message);
    }
}

function togglePrevSchAmt(show) {
    const div = document.getElementById('prev_sch_amounts_div');
    if (show) {
        div.classList.remove('d-none');
    } else {
        div.classList.add('d-none');
        document.getElementById('field_scholarship_amt_2023_24').value = '';
        document.getElementById('field_scholarship_amt_2024_25').value = '';
        document.getElementById('field_scholarship_amt_2025_26').value = '';
    }
}

function toggleSubmitBtn() {
    const cb = document.getElementById('declarationCheckbox');
    const btn = document.getElementById('btnSubmit');
    if (cb && btn) {
        btn.disabled = !cb.checked;
    }
}

// Restore togglePrevSchAmt state from draft on page load
document.addEventListener('DOMContentLoaded', function() {
    const prevSchYes = document.getElementById('prev_sch_yes');
    const prevSchNo = document.getElementById('prev_sch_no');
    if (prevSchYes && prevSchYes.checked) {
        togglePrevSchAmt(true);
    } else if (prevSchNo && prevSchNo.checked) {
        togglePrevSchAmt(false);
    }
});

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
    
    const familyOccupation = document.getElementById('field_family_occupation').value.trim();
    const careerGoal = document.getElementById('field_career_goal').value.trim();
    const familyMembersCount = document.getElementById('field_family_members_count').value.trim();
    const earningMembersCount = document.getElementById('field_earning_members_count').value.trim();
    
    const classYear = document.getElementById('field_class_year').value;
    const percentage = document.getElementById('field_percentage').value.trim();
    const marksObtained = document.getElementById('field_marks_obtained').value.trim();
    const maxMarks = document.getElementById('field_max_marks').value.trim();
    const collegeName = document.getElementById('field_college_name').value.trim();
    const boardUniversity = document.getElementById('field_board_university').value.trim();
    const currentClass = document.getElementById('field_current_class').value.trim();
    const currentCollege = document.getElementById('field_current_college').value.trim();
    
    const prevSchRadio = document.querySelector('input[name="prev_scholarship_received"]:checked');
    const prevSchReceived = prevSchRadio ? prevSchRadio.value : 'नहीं';
    
    const schAmt23_24 = document.getElementById('field_scholarship_amt_2023_24').value.trim();
    const schAmt24_25 = document.getElementById('field_scholarship_amt_2024_25').value.trim();
    const schAmt25_26 = document.getElementById('field_scholarship_amt_2025_26').value.trim();
    
    const bankName = document.getElementById('field_bank_name').value.trim();
    const accountNumber = document.getElementById('field_account_number').value.trim();
    const accountHolderName = document.getElementById('field_account_holder_name').value.trim();
    const ifscCode = document.getElementById('field_ifsc_code').value.trim();
    const familyIncome = document.getElementById('field_family_income').value.trim();

    // Map values into preview sheet
    document.getElementById('preview_name').textContent = `${firstName} ${lastName}`;
    document.getElementById('preview_father_name').textContent = fatherName;
    document.getElementById('preview_mother_name').textContent = motherName;
    document.getElementById('preview_gender').textContent = gender === 'Male' ? 'पुरुष (Male)' : (gender === 'Female' ? 'महिला (Female)' : 'अन्य (Other)');
    
    // Formatting date
    if (dob) {
        const parts = dob.split('-');
        const d = new Date(parts[0], parts[1] - 1, parts[2]);
        document.getElementById('preview_dob').textContent = d.toLocaleDateString('hi-IN', { day: '2-digit', month: 'long', year: 'numeric' });
    } else {
        document.getElementById('preview_dob').textContent = '-';
    }

    document.getElementById('preview_mobile').textContent = mobile;
    document.getElementById('preview_email').textContent = email;
    document.getElementById('preview_address').textContent = `${address}, ${city}, ${district} - ${pincode}`;
    
    document.getElementById('preview_family_occupation').textContent = familyOccupation;
    document.getElementById('preview_career_goal').textContent = careerGoal;
    document.getElementById('preview_family_members_count').textContent = familyMembersCount;
    document.getElementById('preview_earning_members_count').textContent = earningMembersCount;
    
    document.getElementById('preview_class_year').textContent = classYear;
    document.getElementById('preview_percentage').textContent = `${percentage}%`;
    document.getElementById('preview_marks').textContent = (marksObtained && maxMarks) ? `${marksObtained} / ${maxMarks}` : '-';
    document.getElementById('preview_college').textContent = collegeName || '-';
    document.getElementById('preview_board').textContent = boardUniversity || '-';
    document.getElementById('preview_current_class').textContent = currentClass;
    document.getElementById('preview_current_college').textContent = currentCollege;
    
    document.getElementById('preview_prev_scholarship_received').textContent = prevSchReceived;
    
    if (prevSchReceived === 'हाँ') {
        document.getElementById('preview_prev_sch_amounts_row').classList.remove('d-none');
        document.getElementById('preview_sch_amt_2023_24').textContent = schAmt23_24 ? `₹ ${schAmt23_24}/-` : '-';
        document.getElementById('preview_sch_amt_2024_25').textContent = schAmt24_25 ? `₹ ${schAmt24_25}/-` : '-';
        document.getElementById('preview_sch_amt_2025_26').textContent = schAmt25_26 ? `₹ ${schAmt25_26}/-` : '-';
    } else {
        document.getElementById('preview_prev_sch_amounts_row').classList.add('d-none');
    }
    
    document.getElementById('preview_bank_name').textContent = bankName;
    document.getElementById('preview_account_number').textContent = accountNumber;
    document.getElementById('preview_account_holder_name').textContent = accountHolderName;
    document.getElementById('preview_ifsc_code').textContent = ifscCode;
    document.getElementById('preview_family_income').textContent = familyIncome ? `₹ ${parseFloat(familyIncome).toLocaleString('en-IN')}/-` : '-';

    // File Preview for Student Photo
    const filePhoto = document.getElementById('file_photo').files[0];
    const previewPhotoBox = document.getElementById('preview_photo_box');
    if (filePhoto) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewPhotoBox.innerHTML = `<img src="${e.target.result}" alt="Student Photo" style="width: 100%; height: 100%; object-fit: cover;">`;
        };
        reader.readAsDataURL(filePhoto);
    } else if (uploadedDocs['Photo']) {
        previewPhotoBox.innerHTML = `<img src="${uploadedDocs['Photo'].url || '/uploads/applications/' + applicationId + '/' + uploadedDocs['Photo'].stored_name}" alt="Student Photo" style="width: 100%; height: 100%; object-fit: cover;">`;
    } else {
        previewPhotoBox.innerHTML = 'विद्यार्थी का<br>फोटो';
    }

    // File Preview for Signature
    const fileSignature = document.getElementById('file_signature').files[0];
    const previewSignatureBox = document.getElementById('preview_signature_box');
    if (fileSignature) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewSignatureBox.innerHTML = `<img src="${e.target.result}" alt="Student Signature" style="width: 100%; height: 100%; object-fit: contain;">`;
        };
        reader.readAsDataURL(fileSignature);
    } else if (uploadedDocs['Signature']) {
        previewSignatureBox.innerHTML = `<img src="${uploadedDocs['Signature'].url || '/uploads/applications/' + applicationId + '/' + uploadedDocs['Signature'].stored_name}" alt="Student Signature" style="width: 100%; height: 100%; object-fit: contain;">`;
    } else {
        previewSignatureBox.innerHTML = 'हस्ताक्षर<br>Signature';
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
    } else if (uploadedDocs['Marksheet']) {
        const doc = uploadedDocs['Marksheet'];
        const isPdf = doc.stored_name.toLowerCase().endsWith('.pdf');
        const url = doc.url || `/uploads/applications/${applicationId}/${doc.stored_name}`;
        if (isPdf) {
            previewMarksheetBox.innerHTML = `<div class="py-2 text-center"><i class="bi bi-file-earmark-pdf fs-2 text-danger"></i><div class="mt-1 small"><a href="${url}" target="_blank" class="text-decoration-underline text-primary">PDF View</a></div></div>`;
        } else {
            previewMarksheetBox.innerHTML = `<img src="${url}" style="max-height: 120px; max-width: 100%; object-fit: contain;">`;
        }
    } else {
        previewMarksheetBox.innerHTML = '<span class="text-muted">अंकतालिका उपलब्ध नहीं है / No Marksheet</span>';
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
    } else if (uploadedDocs['Passbook']) {
        const doc = uploadedDocs['Passbook'];
        const isPdf = doc.stored_name.toLowerCase().endsWith('.pdf');
        const url = doc.url || `/uploads/applications/${applicationId}/${doc.stored_name}`;
        if (isPdf) {
            previewPassbookBox.innerHTML = `<div class="py-2 text-center"><i class="bi bi-file-earmark-pdf fs-2 text-danger"></i><div class="mt-1 small"><a href="${url}" target="_blank" class="text-decoration-underline text-primary">PDF View</a></div></div>`;
        } else {
            previewPassbookBox.innerHTML = `<img src="${url}" style="max-height: 120px; max-width: 100%; object-fit: contain;">`;
        }
    } else {
        previewPassbookBox.innerHTML = '<span class="text-muted">बैंक पासबुक उपलब्ध नहीं है / No Passbook</span>';
    }
}

function saveDraftAction() {
    var btn = document.querySelector('#btnSaveDraft, button[onclick="saveDraftAction()"]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> सेव... / Saving...'; }
    document.getElementById('wizardAction').value = 'save_draft';
    const form = document.getElementById('scholarshipWizardForm');
    form.submit();
}

function confirmFinalSubmit() {
    const checkbox = document.getElementById('declarationCheckbox');
    if (!checkbox || !checkbox.checked) {
        alert('कृपया स्व-घोषणा बॉक्स को चेक करें / Please check the self-declaration box.');
        return;
    }
    const modalEl = document.getElementById('finalSubmitModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

function executeFinalSubmit() {
    var btn = document.getElementById('btnSubmit');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> सबमिट... / Submitting...'; }
    document.getElementById('wizardAction').value = 'final_submit';
    const form = document.getElementById('scholarshipWizardForm');
    if (form) form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmSubmitBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            const modalEl = document.getElementById('finalSubmitModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            }
            executeFinalSubmit();
        });
    }
});

// ─── Disable Required Fields for Inactive Steps & Initialize ───
document.addEventListener('DOMContentLoaded', function() {
    for (let s = 1; s <= 4; s++) {
        if (s !== currentStep) {
            const stepDiv = document.getElementById(`step${s}`);
            if (stepDiv) {
                stepDiv.querySelectorAll('[required]').forEach(input => {
                    input.removeAttribute('required');
                });
            }
        }
    }
    
    if (currentStep === 4) {
        compileFormPreview();
        toggleSubmitBtn();
    }
});

// ─── Unsaved Progress Auto-Save and Navigation Warn ───
(function () {
    const isEditMode = <?= $isEdit ? 'true' : 'false' ?>;
    const FORM_DRAFT_KEY = 'scholarship_form_draft_' + (isEditMode ? String(<?= (int) ($application['id'] ?? 0) ?>) : 'new');
    const wizardForm = document.getElementById('scholarshipWizardForm');
    if (!wizardForm) return;

    // Save form data to localStorage (skip sensitive bank fields)
    function saveFormDraft() {
        const sensitiveFields = ['account_number', 'confirm_account_number', 'ifsc_code'];
        const formData = {};
        wizardForm.querySelectorAll('input:not([type="file"]):not([type="hidden"]):not([name="csrf_token"]), select, textarea').forEach(input => {
            if (input.name && !input.readOnly && !input.disabled) {
                // Skip sensitive bank fields for security
                if (sensitiveFields.includes(input.name)) return;
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.checked) {
                        formData[input.name] = input.value;
                    }
                } else {
                    formData[input.name] = input.value;
                }
            }
        });
        localStorage.setItem(FORM_DRAFT_KEY, JSON.stringify(formData));
    }

    // Restore form data from localStorage
    function restoreFormDraft() {
        const savedData = localStorage.getItem(FORM_DRAFT_KEY);
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                Object.keys(formData).forEach(name => {
                    const value = formData[name];
                    const inputs = wizardForm.querySelectorAll(`[name="${name}"]`);
                    inputs.forEach(input => {
                        if (input.readOnly || input.disabled) return;
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            if (input.value === value) {
                                input.checked = true;
                            }
                        } else {
                            input.value = value;
                            input.dispatchEvent(new Event('input'));
                        }
                    });
                });
            } catch (e) {
                console.error('Error restoring form draft:', e);
            }
        }
    }

    // Setup listeners on inputs to auto-save as user types/modifies
    wizardForm.querySelectorAll('input:not([type="file"]), select, textarea').forEach(input => {
        input.addEventListener('input', saveFormDraft);
        input.addEventListener('change', saveFormDraft);
    });

    // Clear localStorage on successful form submit
    wizardForm.addEventListener('submit', () => {
        localStorage.removeItem(FORM_DRAFT_KEY);
        window.removeEventListener('beforeunload', beforeUnloadHandler);
    });

    // Unsaved changes navigation warning
    let isFormModified = false;
    wizardForm.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('change', () => { isFormModified = true; });
        input.addEventListener('input', () => { isFormModified = true; });
    });

    function beforeUnloadHandler(e) {
        if (isFormModified && document.getElementById('wizardAction').value !== 'save_draft') {
            e.preventDefault();
            e.returnValue = '';
        }
    }
    window.addEventListener('beforeunload', beforeUnloadHandler);

    // Initial restore draft on page load
    restoreFormDraft();
})();
</script>

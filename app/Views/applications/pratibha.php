<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Core\Flash;

$activeSession = $activeSession ?? [];
$student = $student ?? [];
$old = Flash::get('old');
$old = $old[0] ?? [];

$isEdit = $isEdit ?? false;
$application = $application ?? [];
$marksheetDoc = null;
$certificateDoc = null;
$photoDoc = null;
$signatureDoc = null;
if ($isEdit && !empty($application['documents'])) {
    foreach ($application['documents'] as $doc) {
        if ($doc['document_type'] === 'Marksheet') $marksheetDoc = $doc;
        if ($doc['document_type'] === 'Certificate') $certificateDoc = $doc;
        if ($doc['document_type'] === 'Photo') $photoDoc = $doc;
        if ($doc['document_type'] === 'Signature') $signatureDoc = $doc;
    }
}

ob_start();
?>
            
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
                <h2 class="tsp-dash-welcome-title fs-3 mb-1">प्रतिभा सम्मान रजिस्ट्रेशन फॉर्म / Pratibha Samman Registration Form</h2>
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
                    <div class="tsp-step-label">शैक्षणिक व उपलब्धि<br><small class="text-muted d-none d-md-inline">Academic & Trophy</small></div>
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
                    <form action="<?= $isEdit ? '/applications/' . (int) $application['id'] . '/edit' : '/applications/pratibha' ?>" method="POST" enctype="multipart/form-data" id="pratibhaWizardForm">
                        <?= Csrf::field() ?>

                        <!-- STEP 1: Personal & Family Information -->
                        <div class="tsp-form-step active" id="step1">
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
                            </div>
                        </div>

                        <!-- STEP 2: Academic & Achievement Details -->
                        <div class="tsp-form-step" id="step2">
                            <h4 class="h5 fw-bold mb-4 text-dark border-bottom pb-2">
                                <i class="bi bi-book-half text-muted me-2"></i> 2. शैक्षणिक एवं उपलब्धि विवरण / Academic & Achievement Details
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
                            </div>

                            <hr class="my-4">

                            <h5 class="h6 fw-bold mb-3 text-secondary">उपलब्धि विवरण / Achievement Details</h5>
                            <div class="row g-3">
                                <div class="col-sm-8">
                                    <label class="form-label small fw-semibold text-muted">उपलब्धि का नाम (Achievement Title) <span class="text-danger">*</span></label>
                                    <input type="text" name="achievement_title" id="field_achievement_title" class="form-control border-2 py-2" placeholder="उदा. जिला स्तरीय विज्ञान प्रदर्शनी, खेल प्रतियोगिता" required
                                           value="<?= Helpers::esc($old['achievement_title'] ?? $application['achievement_title'] ?? '') ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small fw-semibold text-muted">रैंक / स्थान (Rank/Position)</label>
                                    <input type="text" name="rank_position" id="field_rank_position" class="form-control border-2 py-2" placeholder="उदा. प्रथम (1st), द्वितीय"
                                           value="<?= Helpers::esc($old['rank_position'] ?? $application['rank_position'] ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">श्रेणी (Category)</label>
                                    <select name="achievement_category" id="field_achievement_category" class="form-select border-2 py-2">
                                        <option value="">श्रेणी चुनें / Select</option>
                                        <?php foreach (['Academic', 'Sports', 'Cultural', 'Science', 'Arts', 'Other'] as $cat): ?>
                                            <option value="<?= $cat ?>" <?= ($old['achievement_category'] ?? $application['achievement_category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">स्तर (Level)</label>
                                    <select name="achievement_level" id="field_achievement_level" class="form-select border-2 py-2">
                                        <option value="">स्तर चुनें / Select</option>
                                        <?php foreach (['School', 'District', 'State', 'National', 'International'] as $lvl): ?>
                                            <option value="<?= $lvl ?>" <?= ($old['achievement_level'] ?? $application['achievement_level'] ?? '') === $lvl ? 'selected' : '' ?>><?= $lvl ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: Required Upload Documents -->
                        <div class="tsp-form-step" id="step3">
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

                                <!-- Certificate -->
                                <div class="col-sm-6">
                                    <div class="p-3 border rounded shadow-sm doc-card" id="card_certificate" data-type="Certificate" data-field="certificate" data-uploaded="<?= $certificateDoc ? 'true' : '' ?>">
                                        <label class="form-label small fw-semibold text-muted d-block mb-2">योग्यता प्रमाणपत्र अपलोड करें (Certificate) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="file" id="file_certificate" class="form-control file-input-field" accept=".jpg,.jpeg,.png,.pdf">
                                            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 btn-upload-doc d-none" onclick="uploadDocAjax('Certificate', 'file_certificate');">
                                                <i class="bi bi-cloud-arrow-up-fill"></i> अपलोड / Upload
                                            </button>
                                        </div>
                                        <div class="form-text text-muted small mt-1">पुरस्कार प्रमाणपत्र या आधिकारिक पुरस्कार दस्तावेज़ (JPG, PNG, PDF | अधिकतम: 2MB)</div>
                                        <div class="doc-status-container mt-2">
                                            <?php if ($certificateDoc): ?>
                                                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                                    <span class="text-success small fw-semibold">
                                                        <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                        <a href="/uploads/applications/<?= $application['id'] ?>/<?= $certificateDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline"><?= Helpers::esc(Helpers::limitString($certificateDoc['original_name'], 25)) ?></a>
                                                    </span>
                                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="deleteDocAjax('Certificate', 'file_certificate');">
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
                                        <div class="input-group">
                                            <input type="file" id="file_signature" class="form-control file-input-field" accept=".jpg,.jpeg,.png">
                                            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 btn-upload-doc d-none" onclick="uploadDocAjax('Signature', 'file_signature');">
                                                <i class="bi bi-cloud-arrow-up-fill"></i> अपलोड / Upload
                                            </button>
                                        </div>
                                        <div class="form-text text-muted small mt-1">सफ़ेद कागज पर काले/नीले पेन से हस्ताक्षर (केवल JPG, PNG | अधिकतम: 500KB)</div>
                                        <div class="doc-status-container mt-2">
                                            <?php if ($signatureDoc): ?>
                                                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                                    <span class="text-success small fw-semibold">
                                                        <i class="bi bi-check-circle-fill"></i> वर्तमान फ़ाइल: 
                                                        <a href="/uploads/applications/<?= $application['id'] ?>/<?= $signatureDoc['stored_name'] ?>" target="_blank" class="text-decoration-underline"><?= Helpers::esc(Helpers::limitString($signatureDoc['original_name'], 25)) ?></a>
                                                    </span>
                                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="deleteDocAjax('Signature', 'file_signature');">
                                                        <i class="bi bi-trash-fill"></i> हटाएं / Remove
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark font-monospace small py-1 px-2 mt-1">दस्तावेज़ आवश्यक है / Required</span>
                                            <?php endif; ?>
                                        </div>
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
                            <div id="printableForm" class="p-3 border rounded bg-white">
                                <!-- Print Header -->
                                <div class="print-header text-center position-relative mb-4">
                                    <div class="print-logo-wrapper text-center mb-2">
                                        <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj" class="print-logo" width="60" height="60">
                                    </div>
                                    <h2 class="print-org-title text-center mb-1">तम्बोली समाज विकास संस्था, राजस्थान</h2>
                                    <div class="print-reg-no text-center fw-bold small mb-1">रजि.नं. 411 / 2016-17</div>
                                    <div class="print-office-address text-center small mb-1">कार्यालय: 132, जनकपुरी-2, इमलीफाटक, जयपुर (राज.)-302005</div>
                                    <div class="print-contact text-center small mb-2">मो. 9829714778, 9414728866 ई मेल : tambolisamaj@gmail.com</div>
                                    <div class="print-form-title-underlined text-center fw-bold fs-5 border-top border-bottom py-2">
                                        प्रतिभा सम्मान रजिस्ट्रेशन आवेदन पत्र - <?= Helpers::esc($activeSession['session_name'] ?? '2026') ?>
                                    </div>
                                </div>

                                <div class="print-form-fields mt-4">
                                    <!-- Photo & Profile Row -->
                                    <div class="row mb-3">
                                        <div class="col-8 col-sm-9">
                                            <div class="print-field-row mb-3">
                                                <span class="print-field-label">विद्यार्थी का नाम (Student Name):</span>
                                                <span class="print-field-value" id="preview_name"></span>
                                            </div>
                                            <div class="print-field-row mb-3">
                                                <span class="print-field-label">पिता का नाम (Father's Name):</span>
                                                <span class="print-field-value" id="preview_father_name"></span>
                                            </div>
                                            <div class="print-field-row mb-3">
                                                <span class="print-field-label">माता का नाम (Mother's Name):</span>
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

                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">उत्तीर्ण कक्षा (Passed Class):</span>
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
                                        <span class="print-field-label">विद्यालय / महाविद्यालय का नाम (School/College Name):</span>
                                        <span class="print-field-value" id="preview_college"></span>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">बोर्ड / विश्वविद्यालय (Board/University):</span>
                                        <span class="print-field-value" id="preview_board"></span>
                                    </div>

                                    <div class="print-section-heading mt-4">उपलब्धि विवरण (Achievement Details)</div>
                                    
                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">उपलब्धि का नाम (Achievement Title):</span>
                                        <span class="print-field-value" id="preview_achievement_title"></span>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">श्रेणी (Category):</span>
                                                <span class="print-field-value" id="preview_achievement_category"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="print-field-row">
                                                <span class="print-field-label">स्तर (Level):</span>
                                                <span class="print-field-value" id="preview_achievement_level"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="print-field-row mb-3">
                                        <span class="print-field-label">स्थान / रैंक (Rank/Position):</span>
                                        <span class="print-field-value" id="preview_rank_position"></span>
                                    </div>
                                </div>

                                <div class="print-section-heading">संलग्न दस्तावेज़ (Attached Documents Checklist)</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-sm-6">
                                        <div class="fw-semibold small text-muted mb-2">गत वर्ष की अंकतालिका / Last Year Marksheet:</div>
                                        <div id="preview_marksheet_box" class="tsp-thumbnail-preview d-flex align-items-center justify-content-center bg-light text-muted py-3" style="min-height: 120px;">
                                            अंकतालिका / Marksheet
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="fw-semibold small text-muted mb-2">योग्यता प्रमाणपत्र / Achievement Certificate:</div>
                                        <div id="preview_certificate_box" class="tsp-thumbnail-preview d-flex align-items-center justify-content-center bg-light text-muted py-3" style="min-height: 120px;">
                                            प्रमाणपत्र / Certificate
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
                                </div>
                            </div>
                        </div>

                        <!-- Stepper Actions Navigation Footer -->
                        <div class="d-flex gap-2 justify-content-between mt-5 pt-3 border-top" id="wizardActions">
                            <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-semibold d-none" id="btnPrev" onclick="moveStep(-1);">
                                <i class="bi bi-chevron-left"></i> पिछला चरण / Previous
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" id="btnCancel" onclick="localStorage.removeItem('pratibha_form_draft_new'); localStorage.removeItem('pratibha_form_draft_<?= (int) ($application['id'] ?? 0) ?>'); location.href='/applications/create';">
                                रद्द करें / Cancel
                            </button>
                            <button type="button" class="btn tsp-dash-welcome-btn shadow-sm rounded-pill px-4 py-2 fw-semibold ms-auto" id="btnNext" onclick="moveStep(1);">
                                अगला चरण / Next <i class="bi bi-chevron-right"></i>
                            </button>
                            <button type="submit" class="btn btn-success shadow-sm rounded-pill px-4 py-2 fw-semibold d-none" id="btnSubmit">
                                <i class="bi bi-check-circle-fill me-1"></i> <?= $isEdit ? 'बदलाव सुरक्षित करें / Save Changes' : 'आवेदन जमा करें / Submit' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

<!-- Stepper Navigation & Preview Script -->
<script>
let currentStep = 1;
const totalSteps = 4;
const isEditMode = <?= $isEdit ? 'true' : 'false' ?>;
const applicationId = <?= (int) ($application['id'] ?? 0) ?>;

// Global map to hold uploaded document details
const uploadedDocs = {
    Marksheet: <?= json_encode($marksheetDoc) ?>,
    Certificate: <?= json_encode($certificateDoc) ?>,
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
        const response = await fetch(`/applications/${applicationId}/upload-document`, {
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
        const response = await fetch(`/applications/${applicationId}/delete-document`, {
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
    
    // Remove previous validation alert if any
    const existingAlert = activeContainer.querySelector('.wizard-validation-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    let isValid = true;
    
    if (currentStep === 3) {
        // Step 3 holds the document cards
        const docCards = activeContainer.querySelectorAll('.doc-card');
        for (let card of docCards) {
            const isUploaded = card.getAttribute('data-uploaded') === 'true';
            const fileInput = card.querySelector('.file-input-field');
            const hasLocalSelectedFile = fileInput && fileInput.files && fileInput.files.length > 0;
            
            // If the document is not uploaded to DB and no local file is selected
            if (!isUploaded && !hasLocalSelectedFile) {
                card.classList.add('border-danger');
                isValid = false;
            } else {
                card.classList.remove('border-danger');
            }
        }
    } else {
        const requiredInputs = activeContainer.querySelectorAll('[required]');
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
    }

    if (!isValid) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger wizard-validation-alert border-0 shadow-sm mt-3 d-flex align-items-center gap-2 small';
        alertDiv.style.borderRadius = '0.5rem';
        alertDiv.innerHTML = `<i class="bi bi-exclamation-triangle-fill fs-5"></i> <span>कृपया सभी आवश्यक जानकारी दर्ज करें या आवश्यक दस्तावेज़ अपलोड करें।</span>`;
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

    const achievementTitle = document.getElementById('field_achievement_title').value.trim();
    const rankPosition = document.getElementById('field_rank_position').value.trim();
    const achievementCategory = document.getElementById('field_achievement_category').value;
    const achievementLevel = document.getElementById('field_achievement_level').value;

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
    
    document.getElementById('preview_class_year').textContent = classYear;
    document.getElementById('preview_percentage').textContent = `${percentage}%`;
    document.getElementById('preview_marks').textContent = (marksObtained && maxMarks) ? `${marksObtained} / ${maxMarks}` : '-';
    document.getElementById('preview_college').textContent = collegeName || '-';
    document.getElementById('preview_board').textContent = boardUniversity || '-';

    document.getElementById('preview_achievement_title').textContent = achievementTitle;
    document.getElementById('preview_rank_position').textContent = rankPosition || '-';
    document.getElementById('preview_achievement_category').textContent = achievementCategory || '-';
    document.getElementById('preview_achievement_level').textContent = achievementLevel || '-';

    // File Preview using FileReader or global uploadedDocs object for Student Photo
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

    // File Preview using FileReader or global uploadedDocs object for Signature
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

    // File Preview for Certificate
    const fileCertificate = document.getElementById('file_certificate').files[0];
    const previewCertificateBox = document.getElementById('preview_certificate_box');
    if (fileCertificate) {
        if (fileCertificate.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewCertificateBox.innerHTML = `<img src="${e.target.result}" style="max-height: 120px; max-width: 100%; object-fit: contain;">`;
            };
            reader.readAsDataURL(fileCertificate);
        } else {
            previewCertificateBox.innerHTML = `<div class="py-2 text-center"><i class="bi bi-file-earmark-pdf fs-2 text-danger"></i><div class="mt-1 small text-truncate" style="max-width: 150px;">${fileCertificate.name}</div></div>`;
        }
    } else if (uploadedDocs['Certificate']) {
        const doc = uploadedDocs['Certificate'];
        const isPdf = doc.stored_name.toLowerCase().endsWith('.pdf');
        const url = doc.url || `/uploads/applications/${applicationId}/${doc.stored_name}`;
        if (isPdf) {
            previewCertificateBox.innerHTML = `<div class="py-2 text-center"><i class="bi bi-file-earmark-pdf fs-2 text-danger"></i><div class="mt-1 small"><a href="${url}" target="_blank" class="text-decoration-underline text-primary">PDF View</a></div></div>`;
        } else {
            previewCertificateBox.innerHTML = `<img src="${url}" style="max-height: 120px; max-width: 100%; object-fit: contain;">`;
        }
    } else {
        previewCertificateBox.innerHTML = '<span class="text-muted">प्रमाणपत्र उपलब्ध नहीं है / No Certificate</span>';
    }
}

// ─── Unsaved Progress Auto-Save and Navigation Warn ───
(function () {
    const isEditMode = <?= $isEdit ? 'true' : 'false' ?>;
    const FORM_DRAFT_KEY = 'pratibha_form_draft_' + (isEditMode ? String(<?= (int) ($application['id'] ?? 0) ?>) : 'new');
    const wizardForm = document.getElementById('pratibhaWizardForm');
    if (!wizardForm) return;

    // Save form data to localStorage
    function saveFormDraft() {
        const formData = {};
        wizardForm.querySelectorAll('input:not([type="file"]):not([type="hidden"]):not([name="csrf_token"]), select, textarea').forEach(input => {
            if (input.name && !input.readOnly && !input.disabled) {
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
        if (isFormModified) {
            e.preventDefault();
            e.returnValue = '';
        }
    }
    window.addEventListener('beforeunload', beforeUnloadHandler);

    // Initial restore draft on page load
    restoreFormDraft();
})();
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/FormLayout.php';
?>

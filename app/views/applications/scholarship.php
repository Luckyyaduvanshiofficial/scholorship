<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helpers;

$activeSession = $activeSession ?? [];
$old = Flash::get('old');
$old = $old[0] ?? [];

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<!-- Dashboard Top Header -->
<header class="tsp-dash-header">
    <!-- Left: Menu Toggle Button -->
    <button class="tsp-dash-menu-toggle" id="tspSidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Center: Logo & Bilingual Title -->
    <div class="tsp-dash-logo-title-group d-flex flex-column align-items-center">
        <div class="d-flex align-items-center gap-2 mb-1">
            <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj Logo" width="36" height="36">
            <h1 class="tsp-dash-title-hi">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</h1>
        </div>
        <span class="tsp-dash-title-en">TAMBOLI SAMAJ VIKAS SANSTHA, RAJASTHAN</span>
    </div>

    <!-- Right: Student Profile Block & Logout -->
    <div class="tsp-dash-profile-block">
        <div class="tsp-dash-profile-info d-none d-md-flex align-items-end me-1">
            <span class="tsp-dash-profile-name"><?= Helpers::esc(Auth::userName()) ?></span>
            <span class="tsp-dash-profile-code"><?= Helpers::esc(Auth::studentCode()) ?></span>
        </div>
        <div class="tsp-dash-avatar me-2">
            <i class="bi bi-person-fill fs-5"></i>
        </div>
        <form action="/logout" method="post" class="m-0">
            <?= Csrf::field() ?>
            <button type="submit" class="tsp-dash-logout-btn shadow-sm">
                <i class="bi bi-box-arrow-right"></i>
                <span>लॉगआउट</span>
            </button>
        </form>
    </div>
</header>

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <!-- Sidebar -->
    <aside class="tsp-dash-sidebar" id="tspSidebar">
        <a href="/dashboard" class="tsp-dash-sidebar-link">
            <i class="bi bi-house-door-fill"></i>
            <span>डैशबोर्ड</span>
        </a>
        <a href="/applications/create" class="tsp-dash-sidebar-link active">
            <i class="bi bi-pencil-square"></i>
            <span>आवेदन फॉर्म भरें</span>
        </a>
        <a href="/applications" class="tsp-dash-sidebar-link">
            <i class="bi bi-file-earmark-text"></i>
            <span>मेरे आवेदन</span>
        </a>
        <a href="/applications" class="tsp-dash-sidebar-link">
            <i class="bi bi-clock-history"></i>
            <span>आवेदन की स्थिति</span>
        </a>
        <a href="/dashboard#help" class="tsp-dash-sidebar-link">
            <i class="bi bi-question-circle"></i>
            <span>सहायता</span>
        </a>
    </aside>

    <!-- Main Content Area -->
    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">
            
            <!-- Breadcrumbs and Header -->
            <div class="mb-4">
                <a href="/applications/create" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>वापस जाएं / Back to Options</span>
                </a>
            </div>

            <div class="mb-4">
                <h2 class="tsp-dash-welcome-title fs-3 mb-1">छात्रवृत्ति आवेदन फॉर्म / Scholarship Application Form</h2>
                <p class="text-muted small mb-0">
                    सक्रिय सत्र (Active Session): <strong class="text-dark"><?= Helpers::esc($activeSession['session_name'] ?? 'N/A') ?></strong>
                </p>
            </div>

            <!-- Application Card Form -->
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-body p-4 p-md-5">
                    <form action="/applications/scholarship" method="post" enctype="multipart/form-data">
                        <?= Csrf::field() ?>

                        <!-- Section 1: Academic Details -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center bg-light text-primary rounded-circle" style="width: 32px; height: 32px;">
                                <i class="bi bi-book-half"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0 text-dark">शैक्षणिक विवरण / Academic Details</h4>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label for="class_year" class="form-label small fw-semibold text-muted">कक्षा / वर्ष (Class/Year) <span class="text-danger">*</span></label>
                                <select name="class_year" id="class_year" class="form-select border-2 py-2" required style="border-radius: 0.5rem;">
                                    <option value="">कक्षा चुनें / Select</option>
                                    <?php foreach (['10th', '12th', 'Graduation', 'Post Graduation'] as $cy): ?>
                                        <option value="<?= $cy ?>" <?= ($old['class_year'] ?? '') === $cy ? 'selected' : '' ?>>
                                            <?= $cy ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="percentage" class="form-label small fw-semibold text-muted">प्रतिशत (Percentage) <span class="text-danger">*</span></label>
                                <input type="number" name="percentage" id="percentage"
                                       class="form-control border-2 py-2" step="0.01" min="0" max="100"
                                       placeholder="उदा. 75.00" style="border-radius: 0.5rem;"
                                       value="<?= Helpers::esc($old['percentage'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="college_name" class="form-label small fw-semibold text-muted">विद्यालय / महाविद्यालय का नाम (School/College Name)</label>
                                <input type="text" name="college_name" id="college_name"
                                       class="form-control border-2 py-2" style="border-radius: 0.5rem;"
                                       placeholder="विद्यालय/महाविद्यालय दर्ज करें"
                                       value="<?= Helpers::esc($old['college_name'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <label for="board_university" class="form-label small fw-semibold text-muted">बोर्ड / विश्वविद्यालय (Board/University)</label>
                                <input type="text" name="board_university" id="board_university"
                                       class="form-control border-2 py-2" style="border-radius: 0.5rem;"
                                       placeholder="उदा. RBSE, CBSE, RTU"
                                       value="<?= Helpers::esc($old['board_university'] ?? '') ?>">
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: #e2e8f0;">

                        <!-- Section 2: Required Documents -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center bg-light text-primary rounded-circle" style="width: 32px; height: 32px;">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0 text-dark">आवश्यक दस्तावेज़ / Required Documents</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label for="marksheet" class="form-label small fw-semibold text-muted">अंकतालिका अपलोड करें (Marksheet) <span class="text-danger">*</span></label>
                                <input type="file" name="marksheet" id="marksheet" class="form-control border-2 py-2" accept=".jpg,.jpeg,.png,.pdf" required style="border-radius: 0.5rem;">
                                <div class="form-text text-muted small">JPG, PNG, या PDF स्वीकार्य। (अधिकतम: PDF 5MB, इमेज 2MB)</div>
                            </div>
                            <div class="col-sm-6">
                                <label for="passbook" class="form-label small fw-semibold text-muted">बैंक पासबुक अपलोड करें (Bank Passbook) <span class="text-danger">*</span></label>
                                <input type="file" name="passbook" id="passbook" class="form-control border-2 py-2" accept=".jpg,.jpeg,.png,.pdf" required style="border-radius: 0.5rem;">
                                <div class="form-text text-muted small">खाताधारक का नाम, खाता संख्या एवं IFSC कोड साफ दिखने वाला पृष्ठ।</div>
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: #e2e8f0;">

                        <!-- Section 3: Bank Details -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center bg-light text-primary rounded-circle" style="width: 32px; height: 32px;">
                                <i class="bi bi-bank"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0 text-dark">बैंक खाता विवरण / Bank Details</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label for="bank_name" class="form-label small fw-semibold text-muted">बैंक का नाम (Bank Name) <span class="text-danger">*</span></label>
                                <input type="text" name="bank_name" id="bank_name"
                                       class="form-control border-2 py-2" style="border-radius: 0.5rem;"
                                       placeholder="उदा. State Bank of India"
                                       value="<?= Helpers::esc($old['bank_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="account_number" class="form-label small fw-semibold text-muted">खाता संख्या (Account Number) <span class="text-danger">*</span></label>
                                <input type="text" name="account_number" id="account_number"
                                       class="form-control border-2 py-2" style="border-radius: 0.5rem;"
                                       placeholder="खाता नंबर दर्ज करें"
                                       value="<?= Helpers::esc($old['account_number'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="ifsc_code" class="form-label small fw-semibold text-muted">IFSC कोड (IFSC Code) <span class="text-danger">*</span></label>
                                <input type="text" name="ifsc_code" id="ifsc_code"
                                       class="form-control border-2 py-2" style="border-radius: 0.5rem;"
                                       placeholder="उदा. SBIN0001234"
                                       value="<?= Helpers::esc($old['ifsc_code'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="family_income" class="form-label small fw-semibold text-muted">वार्षिक पारिवारिक आय (Annual Family Income)</label>
                                <input type="number" name="family_income" id="family_income"
                                       class="form-control border-2 py-2" step="0.01" style="border-radius: 0.5rem;"
                                       placeholder="आय दर्ज करें (उदा. 150000)"
                                       value="<?= Helpers::esc($old['family_income'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end mt-5 pt-3 border-top">
                            <a href="/applications/create" class="btn btn-light rounded-pill px-4 py-2 fw-semibold">रद्द करें / Cancel</a>
                            <button type="submit" class="btn tsp-dash-welcome-btn shadow-sm rounded-pill px-4 py-2 fw-semibold">
                                <i class="bi bi-send-fill me-1"></i> आवेदन जमा करें / Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Inline Sidebar Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('tspSidebarToggle');
    const sidebar = document.getElementById('tspSidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('collapsed');
        });
    }
    
    // Auto collapse sidebar on small screens when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991.98) {
            if (sidebar && !sidebar.classList.contains('collapsed') && !sidebar.contains(e.target) && e.target !== toggleBtn) {
                sidebar.classList.add('collapsed');
            }
        }
    });
});
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

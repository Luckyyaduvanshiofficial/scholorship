<?php
use App\Core\Auth;
use App\Core\Csrf;

// Load the standard HTML head & custom CSS
require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<!-- Outer full-viewport shell -->
<div class="d-flex flex-column min-vh-100 bg-light" style="font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;">

    <?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

    <!-- Sidebar and Main Panel Workspace Container -->
    <div class="d-flex flex-grow-1 position-relative">

        <?php
        $activeSidebarLink = admin_path('students');
        require VIEW_PATH . '/layouts/admin-sidebar.php';
        ?>

        <!-- ── MAIN WORKSPACE CONTENT AREA ── -->
        <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
            <div class="container-fluid p-0">

                <!-- Heading Strip -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <h2 class="h3 fw-bold text-dark mb-1" style="font-family: 'Manrope', sans-serif;">उपयोगकर्ता (छात्र) प्रबंधन</h2>
                        <p class="text-secondary mb-0 small fw-semibold" style="font-size: 1.3rem;">पंजीकृत छात्रों के खातों और उनकी स्थिति का प्रबंधन करें।</p>
                    </div>
                </div>

                <!-- Filters & Search Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <form method="get" action="<?= admin_path('students') ?>" class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="search" class="form-label fw-bold text-secondary" style="font-size: 1.2rem;">खोजें (नाम, ईमेल, मोबाइल, या कोड)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control border-start-0" id="search" name="search" 
                                           placeholder="उदा. अमित, 98765..." value="<?= htmlspecialchars($search ?? '') ?>" style="border-color: #cbd5e1; font-size: 1.25rem;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label fw-bold text-secondary" style="font-size: 1.2rem;">खाता स्थिति</label>
                                <select class="form-select" id="status" name="status" style="border-color: #cbd5e1; font-size: 1.25rem;">
                                    <option value="all" <?= ($status ?? 'all') === 'all' ? 'selected' : '' ?>>सभी</option>
                                    <option value="0" <?= ($status ?? '') === '0' ? 'selected' : '' ?>>सक्रिय</option>
                                    <option value="2" <?= ($status ?? '') === '2' ? 'selected' : '' ?>>निलंबित</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-success fw-bold px-4 py-2 flex-grow-1" style="font-size: 1.25rem; background-color: #10b981; border-color: #10b981;">
                                    फिल्टर लागू करें
                                </button>
                                <a href="<?= admin_path('students') ?>" class="btn btn-outline-secondary fw-bold px-3 py-2" style="font-size: 1.25rem;">
                                    रीसेट
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Students Table Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="table-responsive table-responsive-card">
                            <table class="table align-middle admin-table" style="font-size: 1.3rem;">
                                <thead>
                                    <tr>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">छात्र कोड</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">नाम</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">ईमेल</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">मोबाइल</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">पंजीकरण तिथि</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">स्थिति</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3 text-end">कार्रवाई</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php if (!empty($students)): ?>
                                        <?php foreach ($students as $student): 
                                            $isSuspended = (int) $student['user_status'] === 2;
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark py-3" data-label="छात्र कोड">
                                                    <?= htmlspecialchars($student['student_code']) ?>
                                                </td>
                                                <td class="fw-semibold text-secondary py-3" data-label="नाम">
                                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                                </td>
                                                <td class="text-secondary py-3" data-label="ईमेल">
                                                    <?= htmlspecialchars($student['email']) ?>
                                                </td>
                                                <td class="text-secondary py-3" data-label="मोबाइल">
                                                    <?= htmlspecialchars($student['mobile']) ?>
                                                </td>
                                                <td class="text-muted py-3" data-label="पंजीकरण तिथि">
                                                    <?= date('d M Y', strtotime($student['created_at'])) ?>
                                                </td>
                                                <td class="py-3" data-label="स्थिति">
                                                    <?php if ($isSuspended): ?>
                                                        <span class="badge rounded-pill px-3 py-2 fw-bold bg-danger text-white" style="font-size: 1.15rem;">
                                                            निलंबित
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge rounded-pill px-3 py-2 fw-bold bg-success text-white" style="font-size: 1.15rem;">
                                                            सक्रिय
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-3 text-end" data-label="कार्रवाई">
                                                    <div class="d-inline-flex gap-2">
                                                        <!-- Toggle Status Button -->
                                                        <form action="/admin/students/<?= $student['id'] ?>/toggle-status" method="post" class="m-0">
                                                            <?= Csrf::field() ?>
                                                            <?php if ($isSuspended): ?>
                                                                <button type="submit" class="btn btn-sm btn-outline-success fw-bold px-3 py-1" style="font-size: 1.15rem;">
                                                                    सक्रिय करें
                                                                </button>
                                                            <?php else: ?>
                                                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-3 py-1" style="font-size: 1.15rem;">
                                                                    निलंबित करें
                                                                </button>
                                                            <?php endif; ?>
                                                        </form>

                                                        <!-- Delete Button -->
                                                        <form action="/admin/students/<?= $student['id'] ?>/delete" method="post" class="m-0" 
                                                              onsubmit="return confirm('क्या आप वाकई इस छात्र को हटाना चाहते हैं? छात्र से जुड़े सभी आवेदन और दस्तावेज भी हमेशा के लिए हट जाएंगे।');">
                                                            <?= Csrf::field() ?>
                                                            <button type="submit" class="btn btn-sm btn-danger fw-bold px-3 py-1 text-white" style="font-size: 1.15rem;">
                                                                <i class="bi bi-trash"></i> हटाएं
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted fw-bold">
                                                कोई छात्र नहीं मिला।
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>

</div>

<!-- Sidebar toggle script -->
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

</body>
</html>

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
        $activeSidebarLink = admin_path('reps');
        require VIEW_PATH . '/layouts/admin-sidebar.php';
        ?>

        <!-- ── MAIN WORKSPACE CONTENT AREA ── -->
        <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
            <div class="container-fluid p-0">

                <!-- Heading & Add Button -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <h2 class="h3 fw-bold text-dark mb-1" style="font-family: 'Manrope', sans-serif;">प्रतिनिधि (Representatives) प्रबंधन</h2>
                        <p class="text-secondary mb-0 small fw-semibold" style="font-size: 1.3rem;">आवेदन जांचने के लिए समाज के प्रतिनिधियों के खातों का प्रबंधन करें।</p>
                    </div>
                    <button class="btn btn-success fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#addRepModal" style="font-size: 1.25rem; background-color: #10b981; border-color: #10b981;">
                        <i class="bi bi-person-plus-fill me-2"></i> नया प्रतिनिधि जोड़ें
                    </button>
                </div>

                <!-- Reps Table Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="table-responsive table-responsive-card">
                            <table class="table align-middle admin-table" style="font-size: 1.3rem;">
                                <thead>
                                    <tr>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">नाम</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">ईमेल</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">पंजीकरण तिथि</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3">स्थिति</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3 text-end">कार्रवाई</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php if (!empty($reps)): ?>
                                        <?php foreach ($reps as $rep): 
                                            $isSuspended = (int) $rep['status'] === 2;
                                        ?>
                                            <tr>
                                                <td class="fw-semibold text-secondary py-3" data-label="नाम">
                                                    <?= htmlspecialchars($rep['username'] ?? '') ?>
                                                </td>
                                                <td class="text-secondary py-3" data-label="ईमेल">
                                                    <?= htmlspecialchars($rep['email']) ?>
                                                </td>
                                                <td class="text-muted py-3" data-label="पंजीकरण तिथि">
                                                    <?= date('d M Y', $rep['registered']) ?>
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
                                                        <form action="<?= admin_path('reps/' . $rep['id'] ?>/toggle-status" method="post" class="m-0">
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
                                                        <form action="/admin/reps/<?= $rep['id'] ?>/delete" method="post" class="m-0" 
                                                              onsubmit="return confirm('क्या आप वाकई इस प्रतिनिधि को हटाना चाहते हैं? यह कार्रवाई अपरिवर्तनीय है।');">
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
                                            <td colspan="5" class="text-center py-5 text-muted fw-bold">
                                                कोई प्रतिनिधि पंजीकृत नहीं है।
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

    <!-- Register Rep Modal -->
    <div class="modal fade" id="addRepModal" tabindex="-1" aria-labelledby="addRepModalLabel" aria-hidden="true" style="font-family: 'Inter', sans-serif;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: 0;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark h4" id="addRepModalLabel">नया प्रतिनिधि जोड़ें</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= admin_path('reps/create')) ?>" method="post">
                    <?= Csrf::field() ?>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold text-secondary" style="font-size: 1.2rem;">नाम</label>
                            <input type="text" class="form-control" id="username" name="username" required 
                                   placeholder="उदा. राजेश कुमार" style="border-color: #cbd5e1; font-size: 1.25rem;">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-secondary" style="font-size: 1.2rem;">ईमेल</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   placeholder="rep@example.com" style="border-color: #cbd5e1; font-size: 1.25rem;">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-secondary" style="font-size: 1.2rem;">पासवर्ड</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6" 
                                   placeholder="कम से कम 6 अक्षर" style="border-color: #cbd5e1; font-size: 1.25rem;">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary fw-semibold px-4" data-bs-dismiss="modal" style="font-size: 1.25rem;">रद्द करें</button>
                        <button type="submit" class="btn btn-success fw-bold px-4" style="font-size: 1.25rem; background-color: #10b981; border-color: #10b981;">पंजीकृत करें</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>

</div>

<!-- Sidebar toggle script -->
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

</body>
</html>

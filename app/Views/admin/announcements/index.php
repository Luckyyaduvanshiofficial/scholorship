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
        $activeSidebarLink = '/admin/announcements';
        require VIEW_PATH . '/layouts/admin-sidebar.php';
        ?>

        <!-- ── MAIN WORKSPACE CONTENT AREA ── -->
        <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
            <div class="container-fluid p-0">

                <!-- Heading & Add Button -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <h2 class="h3 fw-bold text-dark mb-1" style="font-family: 'Manrope', sans-serif;">सूचनाएं (Announcements) प्रबंधन</h2>
                        <p class="text-secondary mb-0 small fw-semibold" style="font-size: 1.3rem;">छात्रों के लिए महत्वपूर्ण सूचनाएं और घोषणाएं जारी करें।</p>
                    </div>
                    <a href="/admin/announcements/create" class="btn btn-success fw-bold px-4 py-2" style="font-size: 1.25rem; background-color: #10b981; border-color: #10b981;">
                        <i class="bi bi-plus-circle-fill me-2"></i> नई सूचना लिखें
                    </a>
                </div>

                <!-- Announcements Table Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="table-responsive table-responsive-card">
                            <table class="table align-middle admin-table" style="font-size: 1.3rem;">
                                <thead>
                                    <tr>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3" style="width: 25%;">शीर्षक</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3" style="width: 45%;">विवरण</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3" style="width: 10%;">स्थिति</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3" style="width: 10%;">जारीकर्ता</th>
                                        <th class="text-secondary fw-bold border-bottom-0 pb-3 text-end" style="width: 10%;">कार्रवाई</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php if (!empty($announcements)): ?>
                                        <?php foreach ($announcements as $ann): 
                                            $isActive = (int) $ann['is_active'] === 1;
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark py-3" data-label="शीर्षक">
                                                    <?= htmlspecialchars($ann['title']) ?>
                                                </td>
                                                <td class="text-secondary py-3 text-truncate" style="max-width: 400px;" data-label="विवरण">
                                                    <?= htmlspecialchars(strip_tags($ann['content'])) ?>
                                                </td>
                                                <td class="py-3" data-label="स्थिति">
                                                    <?php if ($isActive): ?>
                                                        <span class="badge rounded-pill px-3 py-2 fw-bold bg-success text-white" style="font-size: 1.15rem;">
                                                            सक्रिय
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge rounded-pill px-3 py-2 fw-bold bg-secondary text-white" style="font-size: 1.15rem;">
                                                            निष्क्रिय
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-secondary py-3" data-label="जारीकर्ता">
                                                    <?= htmlspecialchars($ann['creator_name'] ?? 'Admin') ?>
                                                </td>
                                                <td class="py-3 text-end" data-label="कार्रवाई">
                                                    <div class="d-inline-flex gap-2">
                                                        <!-- Edit Button -->
                                                        <a href="/admin/announcements/<?= $ann['id'] ?>/edit" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1" style="font-size: 1.15rem;">
                                                            <i class="bi bi-pencil"></i> एडिट
                                                        </a>

                                                        <!-- Delete Button -->
                                                        <form action="/admin/announcements/<?= $ann['id'] ?>/delete" method="post" class="m-0" 
                                                              onsubmit="return confirm('क्या आप वाकई इस सूचना को हटाना चाहते हैं?');">
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
                                                कोई सूचना उपलब्ध नहीं है।
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

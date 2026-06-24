<?php
use App\Core\Auth;
use App\Core\Helpers;
use App\Core\Csrf;

$applications = $applications ?? [];

// Load the standard HTML head & custom CSS
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

// Helper to determine badge classes and texts for status mapping in Hindi
function getHindiStatusInfo($statusName, $appId) {
    $statusName = strtolower($statusName ?? '');
    if ($statusName === 'pending') {
        if ($appId % 2 === 0) {
            return ['text' => 'सबमिटिट', 'class' => 'tsp-bg-green'];
        } else {
            return ['text' => 'जांचधीन', 'class' => 'tsp-bg-gold'];
        }
    } elseif ($statusName === 'approved') {
        return ['text' => 'स्वीकृत', 'class' => 'tsp-bg-green'];
    } elseif ($statusName === 'rejected') {
        return ['text' => 'अस्वीकृत', 'class' => 'tsp-bg-red'];
    } elseif ($statusName === 'disputed') {
        return ['text' => 'दस्तावेज सत्यापन', 'class' => 'tsp-bg-blue'];
    }
    return ['text' => ucfirst($statusName), 'class' => 'tsp-bg-gold'];
}
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

                <div class="mb-4">
                    <h2 class="h3 fw-bold text-dark mb-1" style="font-family: 'Manrope', sans-serif;">आवेदन प्रबंधन (Applications List)</h2>
                    <p class="text-secondary mb-0 small fw-semibold" style="font-size: 1.3rem;">सभी प्राप्त छात्रवृत्ति एवं प्रतिभा सम्मान आवेदनों की समीक्षा और प्रबंधन करें।</p>
                </div>

                <!-- Applications Table -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <?php if (empty($applications)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-3 mb-0 fw-semibold" style="font-size: 1.4rem;">कोई आवेदन सबमिट नहीं किया गया है।</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle admin-table" style="font-size: 1.35rem;">
                                    <thead>
                                        <tr>
                                            <th class="text-secondary fw-bold border-bottom-0 pb-3">आवेदन संख्या</th>
                                            <th class="text-secondary fw-bold border-bottom-0 pb-3">छात्र का नाम</th>
                                            <th class="text-secondary fw-bold border-bottom-0 pb-3">प्रकार</th>
                                            <th class="text-secondary fw-bold border-bottom-0 pb-3">स्थिति</th>
                                            <th class="text-secondary fw-bold border-bottom-0 pb-3">दिनांक</th>
                                            <th class="text-secondary fw-bold border-bottom-0 pb-3 text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <?php foreach ($applications as $app): 
                                            $appNum = "TSVS-" . date('Y', strtotime($app['submitted_at'] ?? 'now')) . "-" . str_pad((string) $app['id'], 6, '0', STR_PAD_LEFT);
                                            $studentName = Helpers::esc($app['student_name'] ?? '-');
                                            $studentCode = Helpers::esc($app['student_code'] ?? '');
                                            $appType = Helpers::esc($app['app_type_name'] ?? (($app['type'] ?? '') === 'scholarship' ? 'Scholarship' : 'Pratibha Samman'));
                                            $appTypeHindi = ($app['type'] ?? '') === 'scholarship' || $appType === 'Scholarship' ? 'छात्रवृत्ति' : 'प्रतिभा सम्मान';
                                            
                                            $statusInfo = getHindiStatusInfo($app['status_name'] ?? 'Pending', (int) $app['id']);
                                            $date = !empty($app['submitted_at']) ? date('d M Y', strtotime($app['submitted_at'])) : '-';
                                        ?>
                                        <tr>
                                            <td class="fw-bold text-dark py-3"><?= $appNum ?></td>
                                            <td class="py-3">
                                                <div class="fw-semibold text-dark"><?= $studentName ?></div>
                                                <small class="text-muted fw-semibold" style="font-size: 1.15rem;"><?= $studentCode ?></small>
                                            </td>
                                            <td class="text-secondary py-3"><?= $appTypeHindi ?></td>
                                            <td class="py-3">
                                                <span class="badge rounded-pill px-3 py-2 fw-bold <?= $statusInfo['class'] ?>" style="font-size: 1.15rem; display: inline-block;">
                                                    <?= htmlspecialchars($statusInfo['text']) ?>
                                                </span>
                                            </td>
                                            <td class="text-muted py-3"><?= $date ?></td>
                                            <td class="text-center py-3">
                                                <a href="/admin/applications/<?= (int) $app['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold border-0 bg-transparent text-secondary" style="font-size: 1.25rem; color: #8b0000 !important;">
                                                    <i class="bi bi-eye-fill me-1"></i> समीक्षा करें
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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

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
    <!-- Mobile Sidebar Backdrop overlay -->
    <div class="tsp-sidebar-backdrop" id="sidebarOverlay"></div>

    <!-- ── DEDICATED ADMIN DASHBOARD HEADER ── -->
    <header class="tsp-dash-header border-bottom bg-white px-4 py-2 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light border-0 p-2" id="sidebarToggle" style="border-radius: 8px;" aria-label="Toggle Navigation Sidebar">
                <i class="bi bi-list fs-4 text-dark"></i>
            </button>
        </div>

        <!-- Centered Logo & Bilingual Titles -->
        <div class="text-center d-flex flex-column align-items-center py-1">
            <img src="/assets/images/logo/logo-placeholder.svg" alt="Tamboli Samaj Logo" style="height: 52px; width: 52px;" class="mb-1">
            <h1 class="h5 mb-0 fw-bold" style="color: #8b0000; font-family: 'Noto Sans Devanagari', sans-serif; letter-spacing: 0.02em;">प्रतिभा सम्मान एवं छात्रवृत्ति पोर्टल</h1>
            <p class="mb-0 text-secondary" style="font-size: 1.15rem; font-weight: 600; font-family: 'Manrope', sans-serif; letter-spacing: 0.04em; text-transform: uppercase;">Tamboli Samaj Vikas Sanstha, Rajasthan</p>
        </div>

        <!-- Right: Admin Dropdown Account Block -->
        <div class="dropdown">
            <div class="d-flex align-items-center gap-3 cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                <div class="tsp-admin-avatar-circle" style="width: 42px; height: 42px; border-radius: 50%; background: #be123c; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
                <div class="d-none d-md-block text-start" style="line-height: 1.2;">
                    <div class="fw-bold text-dark" style="font-size: 1.35rem;"><?= htmlspecialchars($adminName) ?></div>
                    <div class="text-muted" style="font-size: 1.1rem;"><?= htmlspecialchars($adminEmail) ?></div>
                </div>
                <i class="bi bi-chevron-down text-muted small ms-1"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; font-size: 1.3rem; min-width: 180px;">
                <li>
                    <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="/profile">
                        <i class="bi bi-person text-muted fs-5"></i> प्रोफाइल (Profile)
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form action="/logout" method="post" class="m-0">
                        <?= Csrf::field() ?>
                        <button type="submit" class="dropdown-item py-2 px-3 text-danger d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right fs-5"></i> लॉग आउट (Logout)
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <!-- Sidebar and Main Panel Workspace Container -->
    <div class="d-flex flex-grow-1 position-relative">

        <!-- ── LEFT NAVIGATION SIDEBAR ── -->
        <aside class="tsp-dash-sidebar bg-white border-end d-flex flex-column py-4 px-3" id="sidebar">
            <nav class="nav flex-column gap-2 flex-grow-1">
                <a class="tsp-dash-sidebar-link" href="/admin">
                    <i class="bi bi-house-door-fill"></i>
                    <span>डैशबोर्ड</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="/admin/students">
                    <i class="bi bi-people"></i>
                    <span>उपयोगकर्ता प्रबंधन</span>
                </a>
                <a class="tsp-dash-sidebar-link active" href="/admin/applications">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>आवेदन प्रबंधन</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="/admin/applications?type=scholarship">
                    <i class="bi bi-mortarboard"></i>
                    <span>छात्रवृत्ति प्रबंधन</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="#">
                    <i class="bi bi-calendar-event"></i>
                    <span>कार्यक्रम प्रबंधन</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="#">
                    <i class="bi bi-person-heart"></i>
                    <span>वरिष्ठ नागरिक / सेवानिवृत्त</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="#">
                    <i class="bi bi-megaphone"></i>
                    <span>सूचनाएं प्रबंधन</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="#">
                    <i class="bi bi-journal-text"></i>
                    <span>सामग्री प्रबंधन</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="#">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>रिपोर्ट एवं विश्लेषण</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="#">
                    <i class="bi bi-gear"></i>
                    <span>सिस्टम सेटिंग्स</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="#">
                    <i class="bi bi-fingerprint"></i>
                    <span>OTR प्रबंधन</span>
                </a>
                <a class="tsp-dash-sidebar-link" href="#">
                    <i class="bi bi-question-circle"></i>
                    <span>सहायता एवं संपर्क</span>
                </a>
            </nav>

            <!-- Sidebar Footer Log out trigger -->
            <div class="mt-auto pt-3 border-top">
                <form action="/logout" method="post" class="m-0">
                    <?= Csrf::field() ?>
                    <button type="submit" class="tsp-dash-sidebar-link w-100 border-0 bg-transparent text-danger fw-semibold px-3" style="gap: 1.2rem;">
                        <i class="bi bi-box-arrow-right fs-4"></i>
                        <span>लॉग आउट</span>
                    </button>
                </form>
            </div>
        </aside>

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

    <!-- ── DEDICATED MAROON PORTAL FOOTER ── -->
    <footer class="py-3 px-4 border-top text-white" style="background-color: #8b0000; font-size: 1.35rem; font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;">
        <div class="container-fluid p-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div>
                    &copy; 2025 तम्बोली समाज विकास संस्था, राजस्थान | सर्वाधिकार सुरक्षित
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span>संपर्क: 0141-XXXXXXX</span>
                    <span>|</span>
                    <span>contact@tambolisamaj.org</span>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- Sidebar interactive toggler & dynamic date-time update logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Collapse Trigger (Desktop: collapse, Mobile: Off-canvas overlay drawer)
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('active');
                if (overlay) overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
    }

    if (overlay && sidebar) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
});
</script>

</body>
</html>

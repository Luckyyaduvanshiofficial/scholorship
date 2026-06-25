<?php
use App\Core\Auth;
use App\Core\Csrf;

// Set admin vars for the shared header partial
$adminName  = Auth::userName() ?: 'प्रतिनिधि';
$adminEmail = '';

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';

// Representative sidebar links
$sidebarLinks = [
    ['href' => '/representative',              'icon' => 'bi-speedometer2',      'label' => 'डैशबोर्ड'],
    ['href' => '/representative/applications', 'icon' => 'bi-file-earmark-text', 'label' => 'आवेदन देखें'],
];
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<!-- Mobile backdrop -->
<div class="tsp-sidebar-backdrop" id="sidebarOverlay"></div>

<!-- Dashboard Layout -->
<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 76px);">

    <!-- ── REPRESENTATIVE SIDEBAR ── -->
    <aside class="tsp-dash-sidebar bg-white border-end d-flex flex-column py-4 px-3" id="sidebar">
        <nav class="nav flex-column gap-2 flex-grow-1">
            <?php foreach ($sidebarLinks as $link):
                $isActive = ($currentUri === $link['href'])
                         || str_starts_with($currentUri, $link['href'] . '/');
            ?>
                <a class="tsp-dash-sidebar-link <?= $isActive ? 'active' : '' ?>"
                   href="<?= htmlspecialchars($link['href']) ?>"
                   <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <i class="bi <?= htmlspecialchars($link['icon']) ?>"></i>
                    <span><?= htmlspecialchars($link['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="mt-auto pt-3 border-top">
            <form action="/logout" method="post" class="m-0">
                <?= Csrf::field() ?>
                <button type="submit"
                        class="tsp-dash-sidebar-link w-100 border-0 bg-transparent text-danger fw-semibold px-3"
                        style="gap:1.2rem;">
                    <i class="bi bi-box-arrow-right fs-4"></i>
                    <span>लॉग आउट</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ── MAIN CONTENT AREA ── -->
    <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
        <div class="container-fluid p-0">

            <!-- Greeting -->
            <div class="mb-4">
                <h2 class="h3 fw-bold text-dark mb-1" style="font-family:'Manrope',sans-serif;">
                    स्वागत है, <?= htmlspecialchars($adminName) ?> 👋
                </h2>
                <p class="text-secondary mb-0 fw-semibold" style="font-size:1.3rem;">
                    यहाँ आप अपने क्षेत्र के छात्रों के आवेदन देख और प्रबंधित कर सकते हैं।
                </p>
            </div>

            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="tsp-metric-card">
                        <div class="tsp-metric-icon-wrapper tsp-bg-red">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div class="tsp-metric-content">
                            <div class="tsp-metric-title">कुल आवेदन</div>
                            <div class="tsp-metric-value">0</div>
                            <div class="tsp-metric-desc">प्राप्त आवेदन</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="tsp-metric-card">
                        <div class="tsp-metric-icon-wrapper tsp-bg-gold">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="tsp-metric-content">
                            <div class="tsp-metric-title">जांचधीन</div>
                            <div class="tsp-metric-value">0</div>
                            <div class="tsp-metric-desc">समीक्षा लंबित</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="tsp-metric-card">
                        <div class="tsp-metric-icon-wrapper tsp-bg-green">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="tsp-metric-content">
                            <div class="tsp-metric-title">स्वीकृत</div>
                            <div class="tsp-metric-value">0</div>
                            <div class="tsp-metric-desc">अनुमोदित</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="tsp-metric-card">
                        <div class="tsp-metric-icon-wrapper tsp-bg-blue">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="tsp-metric-content">
                            <div class="tsp-metric-title">छात्र</div>
                            <div class="tsp-metric-value">0</div>
                            <div class="tsp-metric-desc">पंजीकृत</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <h3 class="h5 fw-bold text-dark mb-4 font-heading">त्वरित कार्य (Quick Actions)</h3>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <a href="/representative/applications" class="tsp-quick-action-card">
                                <i class="bi bi-list-check"></i>
                                <span>आवेदन देखें</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>

<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

</body>
</html>

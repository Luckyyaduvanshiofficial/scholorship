<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helpers;

$adminName  = Auth::userName() ?: 'प्रतिनिधि';
$adminEmail = '';

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';

$sidebarLinks = [
    ['href' => '/representative',              'icon' => 'bi-speedometer2',      'label' => 'डैशबोर्ड'],
    ['href' => '/representative/applications', 'icon' => 'bi-file-earmark-text', 'label' => 'आवेदन देखें'],
];
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<div class="tsp-sidebar-backdrop" id="sidebarOverlay"></div>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 76px);">
    <aside class="tsp-dash-sidebar bg-white border-end d-flex flex-column py-4 px-3" id="sidebar">
        <nav class="nav flex-column gap-2 flex-grow-1">
            <?php foreach ($sidebarLinks as $link):
                $isActive = ($currentUri === $link['href'])
                         || str_starts_with($currentUri, $link['href'] . '/');
            ?>
                <a class="tsp-dash-sidebar-link <?= $isActive ? 'active' : '' ?>"
                   href="<?= Helpers::esc($link['href']) ?>"
                   <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <i class="bi <?= Helpers::esc($link['icon']) ?>"></i>
                    <span><?= Helpers::esc($link['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="mt-auto pt-3 border-top">
            <form action="/logout" method="post" class="m-0">
                <?= Csrf::field() ?>
                <button type="submit" class="tsp-dash-sidebar-link w-100 border-0 bg-transparent text-danger fw-semibold px-3" style="gap:1.2rem;">
                    <i class="bi bi-box-arrow-right fs-4"></i>
                    <span>लॉगआउट</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
        <div class="mb-4">
            <h1 class="h3 fw-bold text-dark mb-1">आवेदन सूची</h1>
            <p class="text-secondary mb-0">सभी जमा किए गए आवेदन (केवल देखने हेतु)</p>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>आवेदन संख्या</th>
                            <th>छात्र</th>
                            <th>प्रकार</th>
                            <th>स्थिति</th>
                            <th>जमा तिथि</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">कोई आवेदन नहीं मिला।</td></tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?= Helpers::esc($app['application_no'] ?? '—') ?></td>
                                    <td><?= Helpers::esc($app['student_name'] ?? '—') ?></td>
                                    <td><?= Helpers::esc($app['app_type_name'] ?? $app['type'] ?? '—') ?></td>
                                    <td><span class="badge bg-secondary"><?= Helpers::esc($app['status_name'] ?? '—') ?></span></td>
                                    <td><?= Helpers::esc($app['submitted_at'] ?? $app['created_at'] ?? '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>
<?php
declare(strict_types=1);

$activeSidebarLink = $activeSidebarLink ?? parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

use App\Core\Helpers;
use App\Core\Url;

if (!function_exists('admin_path')) {
    function admin_path(string $path = ''): string
    {
        return Url::admin($path);
    }
}
?>
<?php require VIEW_PATH . '/layouts/header.php'; ?>
<?php require VIEW_PATH . '/layouts/flash-message.php'; ?>
<div class="d-flex flex-column min-vh-100 bg-light">
    <?php require VIEW_PATH . '/layouts/admin-header.php'; ?>
    <div class="d-flex flex-grow-1 position-relative">
        <?php require VIEW_PATH . '/layouts/admin-sidebar.php'; ?>
        <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
            <?= $content ?? '' ?>
        </main>
    </div>
    <?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>
</div>
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>
</body>
</html>

<?php
/**
 * Mobile Bottom Tab Navigation (Android-app style)
 *
 * Visible only on small screens for logged-in users.
 * Includes safe-area inset support for notched devices.
 */
use App\Core\Auth;
use App\Core\Helpers;

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$role = 'student';
if (Auth::isAdmin()) {
    $role = 'admin';
} elseif (Auth::isRepresentative()) {
    $role = 'representative';
}

$dashHref = Auth::isAdmin()
    ? '/admin'
    : (Auth::isRepresentative() ? '/representative' : '/dashboard');

$items = [];

if ($role === 'student') {
    $items = [
        ['href' => '/dashboard', 'icon' => 'bi-house-door-fill', 'label' => 'होम', 'active' => $uri === '/dashboard'],
        ['href' => '/dashboard/applications/create', 'icon' => 'bi-pencil-square', 'label' => 'आवेदन', 'active' => str_starts_with($uri, '/dashboard/applications/create')],
        ['href' => '/dashboard/applications', 'icon' => 'bi-file-earmark-text', 'label' => 'आवेदन', 'active' => $uri === '/dashboard/applications' || str_starts_with($uri, '/dashboard/applications/') && !str_starts_with($uri, '/dashboard/applications/create')],
        ['href' => '/dashboard/profile', 'icon' => 'bi-person-fill', 'label' => 'प्रोफाइल', 'active' => str_starts_with($uri, '/dashboard/profile')],
    ];
} elseif ($role === 'admin') {
    $items = [
        ['href' => '/admin', 'icon' => 'bi-speedometer2', 'label' => 'डैशबोर्ड', 'active' => $uri === '/admin'],
        ['href' => '/admin/applications', 'icon' => 'bi-file-earmark-text', 'label' => 'आवेदन', 'active' => str_starts_with($uri, '/admin/applications')],
        ['href' => '/admin/students', 'icon' => 'bi-people-fill', 'label' => 'छात्र', 'active' => str_starts_with($uri, '/admin/students')],
        ['href' => '/admin/settings', 'icon' => 'bi-gear-fill', 'label' => 'सेटिंग', 'active' => str_starts_with($uri, '/admin/settings')],
    ];
} else {
    // representative
    $items = [
        ['href' => '/representative', 'icon' => 'bi-speedometer2', 'label' => 'डैशबोर्ड', 'active' => $uri === '/representative'],
        ['href' => '/dashboard/applications/create', 'icon' => 'bi-pencil-square', 'label' => 'आवेदन', 'active' => str_starts_with($uri, '/dashboard/applications/create')],
        ['href' => '/dashboard/applications', 'icon' => 'bi-file-earmark-text', 'label' => 'आवेदन', 'active' => $uri === '/dashboard/applications'],
        ['href' => '/dashboard/profile', 'icon' => 'bi-person-fill', 'label' => 'प्रोफाइल', 'active' => str_starts_with($uri, '/dashboard/profile')],
    ];
}
?>

<nav class="tsp-mobile-bottom-nav d-lg-none" aria-label="Mobile bottom navigation">
    <?php foreach ($items as $item): ?>
        <a href="<?= Helpers::esc($item['href']) ?>"
           class="tsp-mobile-bottom-item <?= $item['active'] ? 'active' : '' ?>"
           aria-current="<?= $item['active'] ? 'page' : 'false' ?>">
            <i class="bi <?= Helpers::esc($item['icon']) ?>"></i>
            <span><?= Helpers::esc($item['label']) ?></span>
        </a>
    <?php endforeach; ?>
</nav>

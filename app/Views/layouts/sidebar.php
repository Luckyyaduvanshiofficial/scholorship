<?php
/**
 * Sidebar — role-based rendering.
 * $role: 'student' | 'admin' | 'representative' | null
 */
$role = $role ?? null;

$currentUri = $_SERVER['REQUEST_URI'] ?? '';

$menus = [
    'admin' => [
        'label' => 'Admin Panel',
        'links' => [
            ['href' => '/admin',                    'icon' => 'bi-speedometer2',       'label' => 'Dashboard'],
            ['href' => '/admin/students',           'icon' => 'bi-people',             'label' => 'Students'],
            ['href' => '/admin/applications',       'icon' => 'bi-file-earmark-text',  'label' => 'Applications'],
            ['href' => '/admin/representatives',    'icon' => 'bi-person-badge',       'label' => 'Representatives'],
            ['href' => '/admin/announcements',      'icon' => 'bi-megaphone',          'label' => 'Announcements'],
            ['href' => '/admin/settings',           'icon' => 'bi-gear',               'label' => 'Settings'],
        ],
    ],
    'representative' => [
        'label' => 'Representative Panel',
        'links' => [
            ['href' => '/representative',               'icon' => 'bi-speedometer2',      'label' => 'Dashboard'],
            ['href' => '/representative/applications',  'icon' => 'bi-file-earmark-text', 'label' => 'Applications'],
        ],
    ],
    'student' => [
        'label' => 'Student Portal',
        'links' => [
            ['href' => '/dashboard',     'icon' => 'bi-speedometer2',      'label' => 'Dashboard'],
            ['href' => '/dashboard/profile',       'icon' => 'bi-person',            'label' => 'My Profile'],
            ['href' => '/academics',     'icon' => 'bi-book',              'label' => 'Academics'],
            ['href' => '/dashboard/applications',  'icon' => 'bi-file-earmark-text', 'label' => 'Applications'],
            ['href' => '/announcements', 'icon' => 'bi-megaphone',         'label' => 'Announcements'],
        ],
    ],
];

if (!$role || !isset($menus[$role])) return;

$menu = $menus[$role];
?>

<aside class="tsp-sidebar d-none d-lg-flex flex-column" aria-label="<?= htmlspecialchars($menu['label']) ?>">

    <div class="tsp-sidebar-header">
        <span class="tsp-sidebar-role-label"><?= htmlspecialchars($menu['label']) ?></span>
    </div>

    <nav class="tsp-sidebar-nav flex-grow-1">
        <?php foreach ($menu['links'] as $link):
            $isActive = ($currentUri === $link['href'])
                     || (str_starts_with($currentUri, $link['href'] . '/') && $link['href'] !== '/');
        ?>
            <a href="<?= htmlspecialchars($link['href']) ?>"
               class="tsp-sidebar-link <?= $isActive ? 'active' : '' ?>"
               <?= $isActive ? 'aria-current="page"' : '' ?>>
                <i class="bi <?= htmlspecialchars($link['icon']) ?>" aria-hidden="true"></i>
                <span><?= htmlspecialchars($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="tsp-sidebar-footer">
        <form action="/logout" method="post" class="m-0">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="tsp-sidebar-link w-100 border-0 bg-transparent text-start">
                <i class="bi bi-box-arrow-left" aria-hidden="true"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
<?php
/**
 * Shared Admin Sidebar Partial
 *
 * Variables expected from parent view:
 *   $activeSidebarLink (string) — href of the currently active link
 */
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Url;

$activeSidebarLink = $activeSidebarLink ?? '';

$sidebarLinks = [
    ['href' => Url::admin(),                         'icon' => 'bi-house-door-fill',    'label' => 'डैशबोर्ड'],
    ['href' => Url::admin('students'),                'icon' => 'bi-people',             'label' => 'उपयोगकर्ता प्रबंधन'],
];

if (Auth::isSuperAdmin()) {
    $sidebarLinks[] = ['href' => Url::admin('reps'),  'icon' => 'bi-shield-lock-fill',   'label' => 'प्रतिनिधि प्रबंधन'];
}

$sidebarLinks[] = ['href' => Url::admin('applications'),            'icon' => 'bi-file-earmark-text',  'label' => 'आवेदन प्रबंधन'];
$sidebarLinks[] = ['href' => Url::admin('events'),                  'icon' => 'bi-calendar-event',     'label' => 'ईवेंट प्रबंधन'];
$sidebarLinks[] = ['href' => Url::admin('blog'),                    'icon' => 'bi-newspaper',          'label' => 'ब्लॉग प्रबंधन'];
$sidebarLinks[] = ['href' => Url::admin('announcements'),           'icon' => 'bi-megaphone',          'label' => 'सूचनाएं प्रबंधन'];

if (Auth::isSuperAdmin()) {
    $sidebarLinks[] = ['href' => Url::admin('settings'), 'icon' => 'bi-gear-fill',          'label' => 'सिस्टम सेटिंग्स'];
}
?>

<!-- Mobile sidebar backdrop -->
<div class="tsp-sidebar-backdrop" id="sidebarOverlay"></div>

<!-- ── LEFT NAVIGATION SIDEBAR ── -->
<aside class="tsp-dash-sidebar bg-white border-end d-flex flex-column py-4 px-3" id="sidebar">
    <nav class="nav flex-column gap-2 flex-grow-1">
        <?php foreach ($sidebarLinks as $link):
            $isActive = ($activeSidebarLink === $link['href']);
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
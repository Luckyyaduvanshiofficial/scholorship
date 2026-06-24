<?php
use App\Core\Auth;
use App\Core\Helpers;

$types = $types ?? [];
$activeSession = $activeSession ?? [];
$existing = $existing ?? [];

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<main class="tsp-sec bg-white min-vh-100">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-3 d-none d-lg-block">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="p-3 text-center border-bottom" style="background: var(--tsp-green); border-radius: 0.8rem 0.8rem 0 0;">
                            <div class="bg-white rounded-circle d-inline-flex p-1 mb-2">
                                <img src="/assets/images/logo/logo-placeholder.svg" width="36" height="36" alt="logo">
                            </div>
                            <div class="text-white fw-semibold small"><?= Helpers::esc(Auth::userName()) ?></div>
                            <div class="text-white-50" style="font-size: 0.75rem;">Student</div>
                        </div>
                        <nav class="nav flex-column p-2">
                            <a class="nav-link text-muted" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                            <a class="nav-link text-muted" href="/profile"><i class="bi bi-person me-2"></i> My Profile</a>
                            <a class="nav-link active fw-semibold" href="/applications" style="color: var(--tsp-green);"><i class="bi bi-file-earmark-text me-2"></i> Applications</a>
                            <a class="nav-link text-muted" href="/announcements"><i class="bi bi-megaphone me-2"></i> Announcements</a>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="mb-4">
                    <a href="/applications" class="small text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Back to Applications
                    </a>
                </div>

                <h2 class="h4 fw-bold mb-1">New Application</h2>
                <p class="text-muted small mb-4">
                    Session: <?= Helpers::esc($activeSession['session_name'] ?? 'N/A') ?>
                </p>

                <div class="row g-3">
                    <?php foreach ($types as $type):
                        $isApplied = !empty($existing[$type['id']]);
                    ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 <?= $isApplied ? 'opacity-50' : 'tsp-card' ?>">
                            <div class="card-body p-4 text-center">
                                <div class="tsp-card-icon mb-3 mx-auto">
                                    <?php if ($type['name'] === 'Scholarship'): ?>
                                        <i class="bi bi-mortarboard-fill"></i>
                                    <?php else: ?>
                                        <i class="bi bi-trophy-fill"></i>
                                    <?php endif; ?>
                                </div>
                                <h5 class="fw-semibold mb-2"><?= Helpers::esc($type['name']) ?></h5>
                                <?php if ($isApplied): ?>
                                    <span class="badge bg-secondary mb-3">Already Applied</span>
                                <?php else: ?>
                                    <p class="small text-muted mb-3">
                                        <?= $type['name'] === 'Scholarship'
                                            ? 'Apply for education scholarship with bank details.'
                                            : 'Register for Pratibha Samman with achievement details.' ?>
                                    </p>
                                    <a href="/applications/<?= $type['name'] === 'Scholarship' ? 'scholarship' : 'pratibha' ?>"
                                       class="btn tsp-btn">
                                        Apply Now <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

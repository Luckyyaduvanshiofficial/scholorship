<?php
use App\Core\Auth;
use App\Core\Helpers;

$applications = $applications ?? [];
$statusClass = function(string $statusName): string {
    return match($statusName) {
        'Approved' => 'bg-success',
        'Rejected' => 'bg-danger',
        'Disputed' => 'bg-warning text-dark',
        default    => 'bg-secondary',
    };
};

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/navbar.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<main class="tsp-sec bg-white min-vh-100">
    <div class="container py-4">
        <div class="row g-4">
            <!-- Sidebar -->
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

            <!-- Main -->
            <div class="col-lg-9">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                    <div>
                        <h2 class="h4 fw-bold mb-1">My Applications</h2>
                        <p class="text-muted small mb-0">Track your scholarship and Pratibha Samman applications</p>
                    </div>
                    <a href="/applications/create" class="btn tsp-btn">
                        <i class="bi bi-plus-lg me-1"></i> New Application
                    </a>
                </div>

                <?php if (empty($applications)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-file-earmark-text fs-1" style="color: var(--tsp-muted);"></i>
                            <h5 class="mt-3 text-muted">No applications yet</h5>
                            <p class="text-muted small">Apply for scholarship or Pratibha Samman to get started.</p>
                            <a href="/applications/create" class="btn tsp-btn mt-2">
                                <i class="bi bi-plus-lg me-1"></i> New Application
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($applications as $app): ?>
                        <div class="col-12">
                            <a href="/applications/<?= (int) $app['id'] ?>" class="text-decoration-none">
                                <div class="card border-0 shadow-sm tsp-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                            <div class="d-flex gap-3 align-items-center">
                                                <div class="tsp-card-icon flex-shrink-0">
                                                    <?php if ($app['type'] === 'scholarship'): ?>
                                                        <i class="bi bi-mortarboard-fill"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-trophy-fill"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-1">
                                                        <?= Helpers::esc($app['app_type_name'] ?? ucfirst($app['type'])) ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        Session: <?= Helpers::esc($app['session_name'] ?? 'N/A') ?>
                                                        &middot; ID: TSVS-<?= date('Y') ?>-<?= str_pad((string) $app['id'], 6, '0', STR_PAD_LEFT) ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <span class="badge rounded-pill <?= $statusClass($app['status_name'] ?? 'Pending') ?>">
                                                <?= Helpers::esc($app['status_name'] ?? 'Pending') ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($app['dispute_message'])): ?>
                                            <div class="mt-2 small text-warning-emphasis bg-warning-subtle rounded p-2">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                <?= Helpers::esc($app['dispute_message']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

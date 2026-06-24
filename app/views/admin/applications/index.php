<?php
use App\Core\Auth;
use App\Core\Helpers;

$applications = $applications ?? [];
$statusClass = fn(string $s): string => match($s) {
    'Approved' => 'bg-success',
    'Rejected' => 'bg-danger',
    'Disputed' => 'bg-warning text-dark',
    default    => 'bg-secondary',
};

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
                        <div class="p-3 text-center border-bottom" style="background: var(--tsp-green-dark); border-radius: 0.8rem 0.8rem 0 0;">
                            <div class="bg-white rounded-circle d-inline-flex p-1 mb-2">
                                <img src="/assets/images/logo/logo-placeholder.svg" width="36" height="36" alt="logo">
                            </div>
                            <div class="text-white fw-semibold small"><?= Helpers::esc(Auth::userName()) ?></div>
                            <div class="text-white-50" style="font-size: 0.75rem;">Admin</div>
                        </div>
                        <nav class="nav flex-column p-2">
                            <a class="nav-link text-muted" href="/admin"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                            <a class="nav-link text-muted" href="/admin/students"><i class="bi bi-people me-2"></i> Students</a>
                            <a class="nav-link active fw-semibold" href="/admin/applications" style="color: var(--tsp-green);"><i class="bi bi-file-earmark-text me-2"></i> Applications</a>
                            <a class="nav-link text-muted" href="/admin/announcements"><i class="bi bi-megaphone me-2"></i> Announcements</a>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <h2 class="h4 fw-bold mb-1">All Applications</h2>
                <p class="text-muted small mb-4">Review, approve, reject, or mark disputed</p>

                <?php if (empty($applications)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-3 mb-0">No applications submitted yet.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle bg-white shadow-sm rounded">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td class="small fw-medium">
                                        TSVS-<?= date('Y') ?>-<?= str_pad((string) $app['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= Helpers::esc($app['student_name'] ?? '-') ?></div>
                                        <small class="text-muted"><?= Helpers::esc($app['student_code'] ?? '') ?></small>
                                    </td>
                                    <td><?= Helpers::esc($app['app_type_name'] ?? ucfirst($app['type'] ?? '')) ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?= $statusClass($app['status_name'] ?? 'Pending') ?>">
                                            <?= Helpers::esc($app['status_name'] ?? 'Pending') ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= !empty($app['submitted_at']) ? date('d M Y', strtotime($app['submitted_at'])) : '-' ?>
                                    </td>
                                    <td>
                                        <a href="/admin/applications/<?= (int) $app['id'] ?>"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye me-1"></i> Review
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

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

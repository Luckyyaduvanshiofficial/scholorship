<?php
use App\Core\Auth;
use App\Core\Csrf;

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
                        <div class="p-3 text-center border-bottom" style="background: var(--tsp-green-dark); border-radius: 0.8rem 0.8rem 0 0;">
                            <div class="bg-white rounded-circle d-inline-flex p-1 mb-2">
                                <img src="/assets/images/logo/logo-placeholder.svg" width="36" height="36" alt="logo">
                            </div>
                            <div class="text-white fw-semibold small"><?= Auth::userName() ?></div>
                            <div class="text-white-50" style="font-size: 0.75rem;">Admin</div>
                        </div>
                        <nav class="nav flex-column p-2">
                            <a class="nav-link active fw-semibold" href="/admin" style="color: var(--tsp-green);">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                            <a class="nav-link text-muted" href="/admin/students">
                                <i class="bi bi-people me-2"></i> Students
                            </a>
                            <a class="nav-link text-muted" href="/admin/applications">
                                <i class="bi bi-file-earmark-text me-2"></i> Applications
                            </a>
                            <a class="nav-link text-muted" href="/admin/announcements">
                                <i class="bi bi-megaphone me-2"></i> Announcements
                            </a>
                            <hr class="my-1">
                            <form action="/logout" method="post" class="m-0">
                                <?= Csrf::field() ?>
                                <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-lg-9">
                <div class="d-flex d-lg-none flex-wrap gap-2 mb-3">
                    <a href="/admin" class="btn btn-sm" style="background: var(--tsp-green); color: #fff;">Dashboard</a>
                    <a href="/admin/applications" class="btn btn-sm btn-outline-secondary">Applications</a>
                    <a href="/admin/students" class="btn btn-sm btn-outline-secondary">Students</a>
                    <a href="/admin/announcements" class="btn btn-sm btn-outline-secondary">Announcements</a>
                </div>

                <h2 class="h5 fw-bold mb-1">Admin Dashboard</h2>
                <p class="text-muted small mb-4">Welcome, <?= Auth::userName() ?></p>

                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3">
                            <i class="bi bi-file-earmark-text fs-3" style="color: var(--tsp-green);"></i>
                            <div class="fw-bold fs-5 mt-1">0</div>
                            <div class="small text-muted">Total Apps</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3">
                            <i class="bi bi-hourglass-split fs-3" style="color: #FFC107;"></i>
                            <div class="fw-bold fs-5 mt-1">0</div>
                            <div class="small text-muted">Pending</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3">
                            <i class="bi bi-check-circle fs-3" style="color: #198754;"></i>
                            <div class="fw-bold fs-5 mt-1">0</div>
                            <div class="small text-muted">Approved</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3">
                            <i class="bi bi-people fs-3" style="color: var(--tsp-green-dark);"></i>
                            <div class="fw-bold fs-5 mt-1">0</div>
                            <div class="small text-muted">Students</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-3">Quick Actions</h5>
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-4">
                                <a href="/admin/applications" class="btn tsp-btn w-100 text-start">
                                    <i class="bi bi-list-check me-2"></i> Review Applications
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <a href="/admin/announcements" class="btn btn-outline-secondary w-100 text-start">
                                    <i class="bi bi-megaphone me-2"></i> Announcements
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

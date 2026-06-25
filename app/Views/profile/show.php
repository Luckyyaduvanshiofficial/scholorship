<?php
use App\Core\Auth;
use App\Core\Helpers;

$student = $student ?? [];
$photo = !empty($student['profile_photo']) ? '/uploads/profiles/' . $student['profile_photo'] : null;
$fullName = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <?php
    $activeLink = 'profile';
    require VIEW_PATH . '/layouts/student-sidebar.php';
    ?>

    <!-- Main Content Area -->
    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                    <div>
                        <h2 class="h4 fw-bold mb-1">My Profile</h2>
                        <p class="text-muted small mb-0">Student Code: <strong><?= Helpers::esc($student['student_code'] ?? '') ?></strong></p>
                    </div>
                    <a href="/profile/edit" class="btn tsp-btn">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <?php if ($photo): ?>
                                        <img src="<?= Helpers::esc($photo) ?>"
                                             alt="Profile Photo" width="120" height="120"
                                             class="rounded-circle border border-3" style="object-fit: cover; border-color: var(--tsp-green) !important;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                                             style="width: 120px; height: 120px; border: 3px solid var(--tsp-green);">
                                            <i class="bi bi-person-fill" style="font-size: 3rem; color: var(--tsp-green);"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <h5 class="fw-bold mb-1"><?= Helpers::esc($fullName) ?></h5>
                                <p class="small text-muted mb-3"><?= Helpers::esc($student['email'] ?? '') ?></p>
                                <a href="/profile/edit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-camera me-1"></i> Change Photo
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 p-md-4">
                                <h5 class="fw-bold mb-3">Personal Details</h5>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">First Name</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['first_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">Last Name</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['last_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">Gender</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['gender'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">Date of Birth</label>
                                        <span class="fw-medium"><?= !empty($student['dob']) ? date('d M Y', strtotime($student['dob'])) : '-' ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">Mobile</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['mobile'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">Email</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['email'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">Father's Name</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['father_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">Mother's Name</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['mother_name'] ?? '-') ?></span>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <h5 class="fw-bold mb-3">Address</h5>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="small text-muted d-block">Address</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['address'] ?: '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">City</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['city'] ?: '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">District</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['district'] ?: '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">State</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['state'] ?: '-') ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-muted d-block">Pincode</label>
                                        <span class="fw-medium"><?= Helpers::esc($student['pincode'] ?: '-') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
        </div>
    </main>
</div>

<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>
<?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>

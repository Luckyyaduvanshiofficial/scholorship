<?php
use App\Core\Auth;
use App\Core\Helpers;

$student = $student ?? [];
$photo = !empty($student['profile_photo']) ? '/uploads/profiles/' . $student['profile_photo'] : null;
$fullName = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
$memberSince = !empty($student['created_at']) ? date('F Y', strtotime($student['created_at'])) : '';

require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

<div class="tsp-dash-container">
    <?php
    $activeLink = 'profile';
    require VIEW_PATH . '/layouts/student-sidebar.php';
    ?>

    <main class="tsp-dash-content-area">
        <div class="container-fluid px-0">

            <!-- Page Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                <div>
                    <h2 class="h4 fw-bold mb-1">My Profile</h2>
                    <p class="text-muted small mb-0">Manage your personal information</p>
                </div>
            </div>

            <div class="row g-4">

                <!-- ─── Left Column: Profile Card ─── -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body text-center py-4 px-3">
                            <!-- Avatar -->
                            <div class="profile-avatar-wrap mx-auto mb-3">
                                <?php if ($photo): ?>
                                    <img src="<?= Helpers::esc($photo) ?>"
                                         alt="Profile Photo"
                                         class="profile-avatar">
                                <?php else: ?>
                                    <div class="profile-avatar profile-avatar--empty">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Name & Code -->
                            <h3 class="profile-name"><?= Helpers::esc($fullName) ?></h3>
                            <div class="profile-code-badge">
                                <i class="bi bi-upc-scan"></i>
                                <?= Helpers::esc($student['student_code'] ?? '') ?>
                            </div>

                            <!-- Email muted -->
                            <p class="profile-email-text">
                                <i class="bi bi-envelope"></i>
                                <?= Helpers::esc($student['email'] ?? '') ?>
                            </p>

                            <hr class="my-3 opacity-25">

                            <!-- Quick info row -->
                            <div class="d-flex justify-content-around text-start mb-3 px-2">
                                <div>
                                    <div class="text-muted small mb-1">Gender</div>
                                    <div class="fw-bold"><?= Helpers::esc($student['gender'] ?? '-') ?></div>
                                </div>
                                <div>
                                    <div class="text-muted small mb-1">Mobile</div>
                                    <div class="fw-bold"><?= Helpers::esc($student['mobile'] ?? '-') ?></div>
                                </div>
                            </div>

                            <!-- Edit Button -->
                            <a href="/dashboard/profile/edit" class="btn w-100 profile-edit-btn">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>

                            <?php if ($memberSince): ?>
                                <p class="profile-member-since mt-3 mb-0">
                                    <i class="bi bi-calendar3"></i>
                                    Member since <strong><?= Helpers::esc($memberSince) ?></strong>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ─── Right Column: Details ─── -->
                <div class="col-md-8">
                    <div class="d-flex flex-column gap-4">

                        <!-- Personal Details Card -->
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="profile-section-accent"></div>
                                    <h4 class="profile-section-title mb-0">
                                        <i class="bi bi-person-badge"></i> Personal Details
                                    </h4>
                                </div>
                                <p class="profile-section-subtitle">Your basic personal information</p>

                                <div class="profile-detail-grid">
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">First Name</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['first_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">Last Name</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['last_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">Gender</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['gender'] ?? '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">Date of Birth</span>
                                        <span class="profile-detail-value"><?= !empty($student['dob']) ? date('d M Y', strtotime($student['dob'])) : '-' ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">Mobile</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['mobile'] ?? '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">Email</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['email'] ?? '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">Father's Name</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['father_name'] ?? '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">Mother's Name</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['mother_name'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Card -->
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="profile-section-accent"></div>
                                    <h4 class="profile-section-title mb-0">
                                        <i class="bi bi-geo-alt"></i> Address
                                    </h4>
                                </div>
                                <p class="profile-section-subtitle">Your residential address details</p>

                                <div class="profile-detail-grid">
                                    <div class="profile-detail-row profile-detail-row--full">
                                        <span class="profile-detail-label">Address</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['address'] ?: '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">City</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['city'] ?: '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">District</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['district'] ?: '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">State</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['state'] ?: '-') ?></span>
                                    </div>
                                    <div class="profile-detail-row">
                                        <span class="profile-detail-label">Pincode</span>
                                        <span class="profile-detail-value"><?= Helpers::esc($student['pincode'] ?: '-') ?></span>
                                    </div>
                                </div>
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

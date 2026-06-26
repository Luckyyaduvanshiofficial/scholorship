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

<!-- Dashboard Main Container -->
<div class="tsp-dash-container">
    <?php
    $activeLink = 'profile';
    require VIEW_PATH . '/layouts/student-sidebar.php';
    ?>

    <!-- Main Content Area -->
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
                    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
                        <!-- Gradient Header -->
                        <div class="profile-card-header"></div>

                        <!-- Avatar & Info -->
                        <div class="profile-card-body">
                            <div class="profile-avatar-wrapper">
                                <?php if ($photo): ?>
                                    <img src="<?= Helpers::esc($photo) ?>"
                                         alt="Profile Photo"
                                         class="profile-avatar-img">
                                <?php else: ?>
                                    <div class="profile-avatar-placeholder">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <h3 class="profile-name"><?= Helpers::esc($fullName) ?></h3>

                            <div class="profile-code-badge">
                                <i class="bi bi-upc-scan"></i>
                                <?= Helpers::esc($student['student_code'] ?? '') ?>
                            </div>

                            <div class="profile-email-text">
                                <i class="bi bi-envelope me-1"></i>
                                <?= Helpers::esc($student['email'] ?? '') ?>
                            </div>

                            <a href="/dashboard/profile/edit" class="profile-edit-btn">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>

                            <?php if ($memberSince): ?>
                                <div class="profile-member-since">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Member since <strong><?= Helpers::esc($memberSince) ?></strong>
                                </div>
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
                                <h4 class="profile-section-title">
                                    <i class="bi bi-person-badge"></i> Personal Details
                                </h4>
                                <p class="profile-section-subtitle">Your basic personal information</p>

                                <div class="profile-detail-grid">
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-person"></i> First Name
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['first_name'] ?? '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-person"></i> Last Name
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['last_name'] ?? '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-gender-ambiguous"></i> Gender
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['gender'] ?? '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-calendar-heart"></i> Date of Birth
                                        </div>
                                        <div class="profile-detail-value"><?= !empty($student['dob']) ? date('d M Y', strtotime($student['dob'])) : '-' ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-phone"></i> Mobile
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['mobile'] ?? '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-envelope"></i> Email
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['email'] ?? '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-person-standing"></i> Father's Name
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['father_name'] ?? '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-person-standing-dress"></i> Mother's Name
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['mother_name'] ?? '-') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Card -->
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h4 class="profile-section-title">
                                    <i class="bi bi-geo-alt"></i> Address
                                </h4>
                                <p class="profile-section-subtitle">Your residential address details</p>

                                <div class="profile-detail-grid">
                                    <div class="profile-detail-item full-width">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-house-door"></i> Address
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['address'] ?: '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-building"></i> City
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['city'] ?: '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-map"></i> District
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['district'] ?: '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-globe2"></i> State
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['state'] ?: '-') ?></div>
                                    </div>
                                    <div class="profile-detail-item">
                                        <div class="profile-detail-label">
                                            <i class="bi bi-mailbox"></i> Pincode
                                        </div>
                                        <div class="profile-detail-value"><?= Helpers::esc($student['pincode'] ?: '-') ?></div>
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

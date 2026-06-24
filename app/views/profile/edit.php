<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helpers;

$student = $student ?? [];
$old = Flash::get('old');
$old = $old[0] ?? [];

// Merge old flash values on top of DB values for sticky form
$oldFirst  = $old['first_name'] ?? $student['first_name'] ?? '';
$oldLast   = $old['last_name'] ?? $student['last_name'] ?? '';
$oldGender = $old['gender'] ?? $student['gender'] ?? '';
$oldDob    = $old['dob'] ?? $student['dob'] ?? '';
$oldMobile = $old['mobile'] ?? $student['mobile'] ?? '';
$oldFather = $old['father_name'] ?? $student['father_name'] ?? '';
$oldMother = $old['mother_name'] ?? $student['mother_name'] ?? '';
$oldAddr   = $old['address'] ?? $student['address'] ?? '';
$oldCity   = $old['city'] ?? $student['city'] ?? '';
$oldDist   = $old['district'] ?? $student['district'] ?? '';
$oldState  = $old['state'] ?? $student['state'] ?? '';
$oldPin    = $old['pincode'] ?? $student['pincode'] ?? '';

$photo = !empty($student['profile_photo']) ? '/uploads/profiles/' . $student['profile_photo'] : null;

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
                            <a class="nav-link active fw-semibold" href="/profile" style="color: var(--tsp-green);"><i class="bi bi-person me-2"></i> My Profile</a>
                            <a class="nav-link text-muted" href="/academics"><i class="bi bi-book me-2"></i> Academics</a>
                            <a class="nav-link text-muted" href="/applications"><i class="bi bi-file-earmark-text me-2"></i> Applications</a>
                            <a class="nav-link text-muted" href="/announcements"><i class="bi bi-megaphone me-2"></i> Announcements</a>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                    <h2 class="h4 fw-bold mb-0">Edit Profile</h2>
                    <a href="/profile" class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-1"></i> View Profile
                    </a>
                </div>

                <!-- Photo Upload -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-3">Profile Photo</h5>
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($photo): ?>
                                <img src="<?= Helpers::esc($photo) ?>" width="80" height="80"
                                     class="rounded-circle border" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                                     style="width: 80px; height: 80px; border: 2px solid var(--tsp-border);">
                                    <i class="bi bi-person-fill fs-3" style="color: var(--tsp-muted);"></i>
                                </div>
                            <?php endif; ?>
                            <form action="/profile/photo" method="post" enctype="multipart/form-data" class="flex-fill">
                                <?= Csrf::field() ?>
                                <div class="input-group">
                                    <input type="file" name="profile_photo" class="form-control form-control-sm"
                                           accept="image/jpeg,image/png">
                                    <button type="submit" class="btn btn-sm tsp-btn">
                                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload
                                    </button>
                                </div>
                                <div class="small text-muted mt-1">JPG or PNG, max 2 MB</div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <form action="/profile" method="post">
                            <?= Csrf::field() ?>

                            <h5 class="fw-bold mb-3">Personal Details</h5>

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="first_name" class="form-label small fw-semibold">First Name *</label>
                                    <input type="text" name="first_name" id="first_name"
                                           class="form-control" value="<?= Helpers::esc($oldFirst) ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="last_name" class="form-label small fw-semibold">Last Name *</label>
                                    <input type="text" name="last_name" id="last_name"
                                           class="form-control" value="<?= Helpers::esc($oldLast) ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold">Gender</label>
                                    <div class="d-flex gap-2">
                                        <input type="radio" class="btn-check" name="gender" id="gMale" value="Male"
                                            <?= $oldGender === 'Male' ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-secondary btn-sm flex-fill" for="gMale">Male</label>
                                        <input type="radio" class="btn-check" name="gender" id="gFemale" value="Female"
                                            <?= $oldGender === 'Female' ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-secondary btn-sm flex-fill" for="gFemale">Female</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="dob" class="form-label small fw-semibold">Date of Birth</label>
                                    <input type="date" name="dob" id="dob"
                                           class="form-control" value="<?= Helpers::esc($oldDob) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="mobile" class="form-label small fw-semibold">Mobile *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+91</span>
                                        <input type="tel" name="mobile" id="mobile" maxlength="10"
                                               class="form-control" value="<?= Helpers::esc($oldMobile) ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold">Email</label>
                                    <div class="form-control bg-light text-muted"><?= Helpers::esc($student['email'] ?? '') ?></div>
                                    <div class="small text-muted mt-1">Email cannot be changed.</div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <h5 class="fw-bold mb-3">Family Details</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="father_name" class="form-label small fw-semibold">Father's Name</label>
                                    <input type="text" name="father_name" id="father_name"
                                           class="form-control" value="<?= Helpers::esc($oldFather) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="mother_name" class="form-label small fw-semibold">Mother's Name</label>
                                    <input type="text" name="mother_name" id="mother_name"
                                           class="form-control" value="<?= Helpers::esc($oldMother) ?>">
                                </div>
                            </div>

                            <hr class="my-3">

                            <h5 class="fw-bold mb-3">Address</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label for="address" class="form-label small fw-semibold">Address</label>
                                    <textarea name="address" id="address" rows="2"
                                              class="form-control"><?= Helpers::esc($oldAddr) ?></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label for="city" class="form-label small fw-semibold">City</label>
                                    <input type="text" name="city" id="city"
                                           class="form-control" value="<?= Helpers::esc($oldCity) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="district" class="form-label small fw-semibold">District</label>
                                    <input type="text" name="district" id="district"
                                           class="form-control" value="<?= Helpers::esc($oldDist) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="state" class="form-label small fw-semibold">State</label>
                                    <input type="text" name="state" id="state"
                                           class="form-control" value="<?= Helpers::esc($oldState) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="pincode" class="form-label small fw-semibold">Pincode</label>
                                    <input type="text" name="pincode" id="pincode" maxlength="6" pattern="\d{6}"
                                           class="form-control" value="<?= Helpers::esc($oldPin) ?>">
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="/profile" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn tsp-btn px-4">
                                    <i class="bi bi-check-lg me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>

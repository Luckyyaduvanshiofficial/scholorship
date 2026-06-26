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
                    <h2 class="h4 fw-bold mb-0">Edit Profile</h2>
                    <a href="/dashboard/profile" class="btn btn-outline-secondary">
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
                            <form action="/dashboard/profile/photo" method="post" enctype="multipart/form-data" class="flex-fill" id="photoUploadForm">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="cropped_image" id="croppedImageInput">
                                <div class="input-group">
                                    <input type="file" id="profilePhotoInput" class="form-control form-control-sm"
                                           accept="image/jpeg,image/png" required>
                                    <button type="button" class="btn btn-sm tsp-btn" id="fakeUploadBtn" style="background: var(--nav-red); color: white;">
                                        <i class="bi bi-cloud-arrow-up me-1"></i> Crop & Upload
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
                        <form action="/dashboard/profile" method="post">
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
                                               class="form-control" value="<?= Helpers::esc($oldMobile) ?>" required
                                               pattern="[6-9]\d{9}" inputmode="numeric" autocomplete="tel">
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
                                <a href="/dashboard/profile" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn tsp-btn px-4">
                                    <i class="bi bi-check-lg me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
            </div>
        </div>
    </main>
</div>

<!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="cropperModalLabel">
                    <i class="bi bi-crop text-muted me-1"></i> फोटो क्रॉप करें / Crop Photo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="img-container bg-light border rounded overflow-hidden mb-3" style="max-height: 380px;">
                    <img id="cropperImage" style="max-width: 100%; display: block; margin: 0 auto;">
                </div>
                <div class="text-muted small">माउस या टच जेस्चर का उपयोग करके फोटो को क्रॉप करें (Aspect Ratio 1:1)</div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">रद्द करें / Cancel</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="cropAndSaveBtn">क्रॉप व अपलोड / Crop & Upload</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('profilePhotoInput');
    const fakeUploadBtn = document.getElementById('fakeUploadBtn');
    const form = document.getElementById('photoUploadForm');
    const croppedInput = document.getElementById('croppedImageInput');

    const modalEl = document.getElementById('cropperModal');
    const cropperImage = document.getElementById('cropperImage');
    const cropAndSaveBtn = document.getElementById('cropAndSaveBtn');

    let cropper = null;
    let bsModal = null;

    // Load Cropper.js dynamically
    const loadCropperAssets = (callback) => {
        if (window.Cropper) {
            callback();
            return;
        }

        // CSS
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css';
        document.head.appendChild(link);

        // JS
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js';
        script.onload = callback;
        document.head.appendChild(script);
    };

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('फोटो का आकार 2MB से अधिक नहीं होना चाहिए। / File is too large (max 2MB).');
                this.value = '';
                return;
            }

            // Validate image type
            if (!file.type.startsWith('image/')) {
                alert('कृपया केवल इमेज फाइल चुनें। / Please select an image file only.');
                this.value = '';
                return;
            }

            loadCropperAssets(() => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    cropperImage.src = e.target.result;
                    if (!bsModal) {
                        bsModal = new bootstrap.Modal(modalEl);
                    }
                    bsModal.show();
                };
                reader.readAsDataURL(file);
            });
        });
    }

    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function () {
            cropper = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false
            });
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            if (fileInput) fileInput.value = '';
        });
    }

    if (cropAndSaveBtn) {
        cropAndSaveBtn.addEventListener('click', function () {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            if (canvas) {
                const dataURL = canvas.toDataURL('image/jpeg', 0.9);
                croppedInput.value = dataURL;
                if (bsModal) {
                    bsModal.hide();
                }
                form.submit();
            }
        });
    }

    if (fakeUploadBtn) {
        fakeUploadBtn.addEventListener('click', function () {
            if (fileInput && fileInput.files.length === 0) {
                alert('कृपया पहले फोटो फाइल चुनें। / Please choose a photo first.');
            }
        });
    }
});
</script>

<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>
<?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>
.php'; ?>

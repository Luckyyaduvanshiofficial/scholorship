<?php
use App\Core\Auth;
use App\Core\Csrf;

// Load the standard HTML head & custom CSS
require VIEW_PATH . '/layouts/header.php';
require VIEW_PATH . '/layouts/flash-message.php';
?>

<!-- Outer full-viewport shell -->
<div class="d-flex flex-column min-vh-100 bg-light" style="font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;">

    <?php require VIEW_PATH . '/layouts/admin-header.php'; ?>

    <!-- Sidebar and Main Panel Workspace Container -->
    <div class="d-flex flex-grow-1 position-relative">

        <?php
        $activeSidebarLink = admin_path('announcements');
        require VIEW_PATH . '/layouts/admin-sidebar.php';
        ?>

        <!-- ── MAIN WORKSPACE CONTENT AREA ── -->
        <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
            <div class="container-fluid p-0" style="max-width: 800px;">

                <!-- Back button & Heading -->
                <div class="mb-4">
                    <a href="<?= admin_path('announcements') ?>" class="btn btn-sm btn-outline-secondary fw-semibold mb-3">
                        <i class="bi bi-arrow-left"></i> वापस जाएं
                    </a>
                    <h2 class="h3 fw-bold text-dark mb-1" style="font-family: 'Manrope', sans-serif;">नई सूचना जारी करें</h2>
                    <p class="text-secondary mb-0 small fw-semibold" style="font-size: 1.3rem;">छात्रों को दिखने वाली नई घोषणा लिखें।</p>
                </div>

                <!-- Create Form Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <form action="<?= admin_path('announcements/create') ?>" method="post">
                            <?= Csrf::field() ?>

                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold text-secondary" style="font-size: 1.25rem;">सूचना का शीर्षक (Title)</label>
                                <input type="text" class="form-control" id="title" name="title" required 
                                       placeholder="उदा. छात्रवृत्ति आवेदन की अंतिम तिथि बढ़ाई गई" style="border-color: #cbd5e1; font-size: 1.3rem; height: 50px;">
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label fw-bold text-secondary" style="font-size: 1.25rem;">सूचना का विवरण (Content)</label>
                                <textarea class="form-control" id="content" name="content" rows="8" required 
                                          placeholder="यहाँ अपनी सूचना का पूरा विवरण विस्तार से लिखें..." style="border-color: #cbd5e1; font-size: 1.3rem; resize: vertical;"></textarea>
                            </div>

                            <div class="mb-4 form-check form-switch d-flex align-items-center gap-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked style="width: 50px; height: 25px; cursor: pointer;">
                                <label class="form-check-label fw-semibold text-secondary" for="is_active" style="font-size: 1.25rem; cursor: pointer; user-select: none;">
                                    सूचना तुरंत सक्रिय करें (छात्रों को दिखाएं)
                                </label>
                            </div>

                            <hr class="my-4" style="border-color: #e2e8f0;">

                            <div class="d-flex gap-3 justify-content-end">
                                <a href="<?= admin_path('announcements') ?>" class="btn btn-outline-secondary fw-semibold px-4 py-2" style="font-size: 1.25rem;">
                                    रद्द करें
                                </a>
                                <button type="submit" class="btn btn-success fw-bold px-5 py-2" style="font-size: 1.25rem; background-color: #10b981; border-color: #10b981;">
                                    सूचना प्रकाशित करें
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <?php require VIEW_PATH . '/layouts/dash-footer.php'; ?>

</div>

<!-- Sidebar toggle script -->
<?php require VIEW_PATH . '/layouts/admin-sidebar-script.php'; ?>

</body>
</html>

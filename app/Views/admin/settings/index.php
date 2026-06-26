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
        $activeSidebarLink = '/admin/settings';
        require VIEW_PATH . '/layouts/admin-sidebar.php';
        ?>

        <!-- ── MAIN WORKSPACE CONTENT AREA ── -->
        <main class="tsp-dash-content-area flex-grow-1 p-4 bg-light">
            <div class="container-fluid p-0">

                <!-- Heading -->
                <div class="mb-4">
                    <h2 class="h3 fw-bold text-dark mb-1" style="font-family: 'Manrope', sans-serif;">सिस्टम सेटिंग्स एवं शैक्षणिक सत्र</h2>
                    <p class="text-secondary mb-0 small fw-semibold" style="font-size: 1.3rem;">पोर्टल के सामान्य पैरामीटर और शैक्षणिक सत्र का प्रबंधन करें।</p>
                </div>

                <div class="row g-4">
                    
                    <!-- Left: Global Settings Form -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h3 class="h5 fw-bold text-dark mb-4 font-heading" style="border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                                    सामान्य पोर्टल सेटिंग्स
                                </h3>
                                
                                <form action="/admin/settings/update" method="post">
                                    <?= Csrf::field() ?>

                                    <!-- Site Name -->
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label fw-semibold text-secondary" style="font-size: 1.2rem;">पोर्टल का नाम (Site Name)</label>
                                        <input type="text" class="form-control" id="site_name" name="site_name" 
                                               value="<?= htmlspecialchars($settings['site_name'] ?? 'Tamboli Samaj Portal') ?>" required
                                               style="border-color: #cbd5e1; font-size: 1.25rem;">
                                    </div>

                                    <!-- Contact Email -->
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label fw-semibold text-secondary" style="font-size: 1.2rem;">संपर्क ईमेल (Contact Email)</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                               value="<?= htmlspecialchars($settings['contact_email'] ?? 'admin@tamoli.org') ?>" required
                                               style="border-color: #cbd5e1; font-size: 1.25rem;">
                                    </div>

                                    <!-- Contact Phone -->
                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label fw-semibold text-secondary" style="font-size: 1.2rem;">संपर्क मोबाइल नंबर (Contact Phone)</label>
                                        <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                                               value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>"
                                               placeholder="+91-XXXXXXXXXX"
                                               style="border-color: #cbd5e1; font-size: 1.25rem;">
                                    </div>

                                    <hr class="my-4" style="border-color: #e2e8f0;">

                                    <!-- Toggle Switches for Registrations -->
                                    <h4 class="h6 fw-bold text-dark mb-3">आवेदन खोलने / बंद करने की सेटिंग्स</h4>

                                    <!-- Scholarship Switch -->
                                    <div class="mb-3 form-check form-switch d-flex align-items-center gap-3">
                                        <input class="form-check-input" type="checkbox" id="scholarship_open" name="scholarship_open" value="1" 
                                               <?= ($settings['scholarship_open'] ?? '0') === '1' ? 'checked' : '' ?>
                                               style="width: 45px; height: 22px; cursor: pointer;">
                                        <label class="form-check-label fw-semibold text-secondary" for="scholarship_open" style="font-size: 1.2rem; cursor: pointer; user-select: none;">
                                            छात्रवृत्ति (Scholarship) आवेदन खोलें
                                        </label>
                                    </div>

                                    <!-- Pratibha Switch -->
                                    <div class="mb-4 form-check form-switch d-flex align-items-center gap-3">
                                        <input class="form-check-input" type="checkbox" id="pratibha_open" name="pratibha_open" value="1" 
                                               <?= ($settings['pratibha_open'] ?? '0') === '1' ? 'checked' : '' ?>
                                               style="width: 45px; height: 22px; cursor: pointer;">
                                        <label class="form-check-label fw-semibold text-secondary" for="pratibha_open" style="font-size: 1.2rem; cursor: pointer; user-select: none;">
                                            प्रतिभा सम्मान (Pratibha Samman) आवेदन खोलें
                                        </label>
                                    </div>

                                    <!-- Active Session ID (hidden or styled read-only, updated via active session control) -->
                                    <input type="hidden" name="current_session_id" value="<?= htmlspecialchars($settings['current_session_id'] ?? '1') ?>">

                                    <button type="submit" class="btn btn-success fw-bold px-5 py-2 w-100 mt-2" style="font-size: 1.25rem; background-color: #10b981; border-color: #10b981;">
                                        पोर्टल सेटिंग्स सहेजें (Save Settings)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Academic Session Management -->
                    <div class="col-lg-5">
                        <!-- Add Academic Session Card -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h3 class="h5 fw-bold text-dark mb-3 font-heading">नया शैक्षणिक सत्र जोड़ें</h3>
                                <form action="/admin/settings/session/create" method="post" class="d-flex gap-2 align-items-end">
                                    <?= Csrf::field() ?>
                                    <div class="flex-grow-1">
                                        <label for="session_name" class="form-label small fw-semibold text-secondary">सत्र का नाम (उदा. 2026-27)</label>
                                        <input type="text" class="form-control" id="session_name" name="session_name" required 
                                               placeholder="2026-27" style="border-color: #cbd5e1; font-size: 1.2rem; height: 42px;">
                                    </div>
                                    <button type="submit" class="btn btn-primary fw-bold" style="height: 42px; font-size: 1.15rem; background-color: #3b82f6; border-color: #3b82f6;">
                                        सत्र जोड़ें
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Sessions List Card -->
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h3 class="h5 fw-bold text-dark mb-4 font-heading" style="border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                                    सत्र सूची (Academic Sessions)
                                </h3>
                                
                                <div class="table-responsive table-responsive-card">
                                    <table class="table align-middle admin-table" style="font-size: 1.25rem;">
                                        <thead>
                                            <tr>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3">सत्र नाम</th>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3">स्थिति</th>
                                                <th class="text-secondary fw-bold border-bottom-0 pb-3 text-end">कार्रवाई</th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top-0">
                                            <?php if (!empty($sessions)): ?>
                                                <?php foreach ($sessions as $session): 
                                                    $isActive = (int) $session['is_active'] === 1;
                                                ?>
                                                    <tr>
                                                        <td class="fw-bold text-dark py-3" data-label="सत्र नाम">
                                                            <?= htmlspecialchars($session['session_name']) ?>
                                                        </td>
                                                        <td class="py-3" data-label="स्थिति">
                                                            <?php if ($isActive): ?>
                                                                <span class="badge rounded-pill px-3 py-1 fw-bold bg-success text-white" style="font-size: 1.1rem;">
                                                                    सक्रिय
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge rounded-pill px-3 py-1 fw-bold bg-secondary text-white" style="font-size: 1.1rem;">
                                                                    निष्क्रिय
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="py-3 text-end" data-label="कार्रवाई">
                                                            <?php if (!$isActive): ?>
                                                                <form action="/admin/settings/session/<?= $session['id'] ?>/activate" method="post" class="m-0">
                                                                    <?= Csrf::field() ?>
                                                                    <button type="submit" class="btn btn-sm btn-outline-success fw-bold px-3 py-1" style="font-size: 1.1rem;">
                                                                        सक्रिय करें
                                                                    </button>
                                                                </form>
                                                            <?php else: ?>
                                                                <span class="text-muted fw-bold small" style="font-size: 1.1rem;">सक्रिय है</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted">
                                                        कोई शैक्षणिक सत्र उपलब्ध नहीं है।
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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

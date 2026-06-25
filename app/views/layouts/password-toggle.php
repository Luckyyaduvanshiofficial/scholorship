<?php
/**
 * Show/hide password toggle for any password input.
 *
 * Usage in a view:
 *   <div class="tsp-auth-input-group tsp-password-group">
 *     <span class="input-group-text"><i class="bi bi-lock"></i></span>
 *     <input type="password" name="password" id="password" class="form-control" ...>
 *     <?php require VIEW_PATH . '/layouts/password-toggle.php'; ?>
 *   </div>
 *
 * The toggle auto-targets the previous password input inside the same .tsp-password-group.
 * Requires Bootstrap Icons (bi-eye / bi-eye-slash) to be loaded.
 */
?>
<button type="button" class="btn btn-outline-secondary tsp-password-toggle"
        aria-label="Show password" title="दिखाएं / Show">
    <i class="bi bi-eye"></i>
</button>
<script>
(function () {
    document.querySelectorAll('.tsp-password-toggle').forEach(function (btn) {
        if (btn.dataset.bound === '1') return;
        btn.dataset.bound = '1';
        btn.addEventListener('click', function () {
            var group = btn.closest('.tsp-password-group');
            if (!group) return;
            var input = group.querySelector('input[type="password"], input[type="text"][data-pw="1"]');
            if (!input) return;
            var icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                input.setAttribute('data-pw', '1');
                if (icon) { icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
                btn.setAttribute('aria-label', 'Hide password');
                btn.setAttribute('title', 'छिपाएं / Hide');
            } else {
                input.type = 'password';
                input.removeAttribute('data-pw');
                if (icon) { icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
                btn.setAttribute('aria-label', 'Show password');
                btn.setAttribute('title', 'दिखाएं / Show');
            }
        });
    });
})();
</script>
<?php
use App\Core\Flash;
use App\Core\Helpers;

$flashMessages = Flash::all();

if (empty($flashMessages)) return;

$config = [
    'success' => ['class' => 'alert-success', 'icon' => 'bi-check-circle-fill'],
    'error'   => ['class' => 'alert-danger',  'icon' => 'bi-exclamation-circle-fill'],
    'warning' => ['class' => 'alert-warning', 'icon' => 'bi-exclamation-triangle-fill'],
    'info'    => ['class' => 'alert-info',    'icon' => 'bi-info-circle-fill'],
    'default' => ['class' => 'alert-secondary','icon' => 'bi-bell-fill'],
];
?>

<div class="tsp-flash-container" aria-live="polite" aria-atomic="true">
    <?php foreach ($flashMessages as $type => $messages):
        $c = $config[$type] ?? $config['default'];
    ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert <?= $c['class'] ?> alert-dismissible fade show tsp-flash-alert" role="alert">
                <i class="bi <?= $c['icon'] ?> me-2 flex-shrink-0" aria-hidden="true"></i>
                <span><?= Helpers::esc($message) ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.tsp-flash-alert');
        alerts.forEach(function(alert) {
            if (window.bootstrap && bootstrap.Alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) bsAlert.close();
            } else {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        });
    }, 5000);
});
</script>
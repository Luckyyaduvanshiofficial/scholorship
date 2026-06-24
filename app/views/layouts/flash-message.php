<?php
use App\Core\Flash;
use App\Core\Helpers;

$flashMessages = Flash::all();
?>

<?php foreach ($flashMessages as $type => $messages): ?>
    <?php
    $alertClass = match ($type) {
        'success' => 'alert-success',
        'error'   => 'alert-danger',
        'warning' => 'alert-warning',
        'info'    => 'alert-info',
        default   => 'alert-secondary',
    };
    $icon = match ($type) {
        'success' => 'check-circle',
        'error'   => 'exclamation-circle',
        'warning' => 'exclamation-triangle',
        'info'    => 'info-circle',
        default   => 'bell',
    };
    ?>
    <?php foreach ($messages as $message): ?>
        <div class="alert <?= $alertClass ?> alert-dismissible fade show m-3" role="alert">
            <i class="bi bi-<?= $icon ?> me-2"></i>
            <?= Helpers::esc($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>

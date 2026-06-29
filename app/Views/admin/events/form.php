<?php
/**
 * Admin Event Form (Create/Edit)
 *
 * Available: $event, $errors, $old
 */
declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helpers;

$isEdit = !empty($event);
$title  = $isEdit ? 'Edit Event' : 'Create Event';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= $title ?></h2>
        <a href="<?= admin_path('events') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Events
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= Helpers::esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="<?= admin_path($isEdit ? 'events/' . $event['id'] . '/edit' : 'events/create') ?>">
                <?= Csrf::field() ?>

                <div class="row g-3">
                    <!-- Title -->
                    <div class="col-12">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required maxlength="200"
                               value="<?= Helpers::esc($old['title'] ?? $event['title'] ?? '') ?>">
                    </div>

                    <!-- Event Date -->
                    <div class="col-md-6">
                        <label class="form-label">Event Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="event_date" class="form-control" required
                               value="<?= Helpers::esc($old['event_date'] ?? ($event['event_date'] ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : '')) ?>">
                    </div>

                    <!-- Location -->
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" maxlength="255"
                               value="<?= Helpers::esc($old['location'] ?? $event['location'] ?? '') ?>">
                    </div>

                    <!-- Excerpt -->
                    <div class="col-12">
                        <label class="form-label">Excerpt <small class="text-muted">(short summary)</small></label>
                        <textarea name="excerpt" class="form-control" rows="2"><?= Helpers::esc($old['excerpt'] ?? $event['excerpt'] ?? '') ?></textarea>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="6"><?= Helpers::esc($old['description'] ?? $event['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Options -->
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1"
                                   <?= ($old['is_active'] ?? $event['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="registration_required" class="form-check-input" id="regRequired" value="1"
                                   <?= ($old['registration_required'] ?? $event['registration_required'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="regRequired">Registration Required</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Max Participants</label>
                        <input type="number" name="max_participants" class="form-control" min="1"
                               value="<?= Helpers::esc($old['max_participants'] ?? $event['max_participants'] ?? '') ?>"
                               placeholder="Leave empty for unlimited">
                    </div>

                    <!-- Submit -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update Event' : 'Create Event' ?>
                        </button>
                        <a href="<?= admin_path('events') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
/**
 * Admin Blog Post Form (Create/Edit)
 *
 * Available: $post, $errors, $old
 */
declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helpers;

$isEdit = !empty($post);
$title  = $isEdit ? 'Edit Blog Post' : 'Create Blog Post';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= $title ?></h2>
        <a href="<?= admin_path('blog') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Blog
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
            <form method="POST" action="<?= admin_path($isEdit ? 'blog/' . $post['id'] . '/edit' : 'blog/create') ?>">
                <?= Csrf::field() ?>

                <div class="row g-3">
                    <!-- Title -->
                    <div class="col-12">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required maxlength="200"
                               value="<?= Helpers::esc($old['title'] ?? $post['title'] ?? '') ?>">
                    </div>

                    <!-- Status -->
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= ($old['status'] ?? $post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($old['status'] ?? $post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="archived" <?= ($old['status'] ?? $post['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>

                    <!-- Excerpt -->
                    <div class="col-12">
                        <label class="form-label">Excerpt <small class="text-muted">(short summary for cards)</small></label>
                        <textarea name="excerpt" class="form-control" rows="2"><?= Helpers::esc($old['excerpt'] ?? $post['excerpt'] ?? '') ?></textarea>
                    </div>

                    <!-- Content -->
                    <div class="col-12">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="12" required><?= Helpers::esc($old['content'] ?? $post['content'] ?? '') ?></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update Post' : 'Create Post' ?>
                        </button>
                        <a href="<?= admin_path('blog') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

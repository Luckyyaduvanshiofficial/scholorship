<?php
/**
 * Blog Post Detail Page
 *
 * Available: $post, $isLoggedIn, $userName
 */
declare(strict_types=1);

use App\Core\Helpers;
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Back Link -->
                <a href="/blog" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Back to Blog
                </a>

                <!-- Post Header -->
                <h1 class="mt-3 mb-3"><?= Helpers::esc($post['title']) ?></h1>

                <div class="d-flex align-items-center text-muted mb-4">
                    <?php if (!empty($post['author_name'])): ?>
                        <span class="me-3">
                            <i class="bi bi-person me-1"></i><?= Helpers::esc($post['author_name']) ?>
                        </span>
                    <?php endif; ?>
                    <span>
                        <i class="bi bi-calendar3 me-1"></i>
                        <?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                    </span>
                </div>

                <!-- Featured Image -->
                <?php if (!empty($post['featured_image'])): ?>
                    <img src="<?= Helpers::esc($post['featured_image']) ?>" class="img-fluid rounded mb-4" alt="<?= Helpers::esc($post['title']) ?>">
                <?php endif; ?>

                <!-- Post Content -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="blog-content" style="line-height:1.8;font-size:1.05rem;">
                            <?= nl2br(Helpers::esc($post['content'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Share / Back -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="/blog" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-left me-1"></i> All Posts
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

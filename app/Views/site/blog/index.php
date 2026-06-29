<?php
/**
 * Blog List Page
 *
 * Available: $posts, $pagination, $isLoggedIn, $userName
 */
declare(strict_types=1);

use App\Core\Helpers;
?>

<section class="py-5">
    <div class="container">
        <h1 class="section-title">Blog <span class="lang-hi">ब्लॉग</span></h1>

        <?php if (empty($posts)): ?>
            <div class="text-center py-5">
                <i class="bi bi-newspaper text-muted" style="font-size:4rem;"></i>
                <h4 class="mt-3 text-muted">No posts yet</h4>
                <p class="text-muted">अभी तक कोई पोस्ट नहीं</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-blog">
                            <?php if (!empty($post['featured_image'])): ?>
                                <img src="<?= Helpers::esc($post['featured_image']) ?>" class="card-img-top" alt="<?= Helpers::esc($post['title']) ?>" style="height:200px;object-fit:cover;">
                            <?php else: ?>
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height:200px;background:#f0f0f0;color:#999;">
                                    <i class="bi bi-newspaper" style="font-size:3rem;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= Helpers::esc($post['title']) ?></h5>
                                <?php if (!empty($post['excerpt'])): ?>
                                    <p class="card-text text-muted small"><?= Helpers::esc(mb_substr($post['excerpt'], 0, 120)) ?>...</p>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i><?= Helpers::esc($post['author_name'] ?? 'Admin') ?>
                                        <br>
                                        <?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                                    </small>
                                    <a href="/blog/<?= Helpers::esc($post['slug']) ?>" class="btn btn-outline-dark btn-sm">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['last_page'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="/blog?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php
/**
 * Admin Blog Posts List
 *
 * Available: $posts, $pagination
 */
declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helpers;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Blog Posts</h2>
        <a href="<?= admin_path('blog/create') ?>" class="btn btn-warning">
            <i class="bi bi-plus-lg me-1"></i> Create Post
        </a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="text-center py-5">
            <i class="bi bi-newspaper text-muted" style="font-size:3rem;"></i>
            <p class="mt-2 text-muted">No blog posts found. Create your first post!</p>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <strong><?= Helpers::esc($post['title']) ?></strong>
                                </td>
                                <td><?= Helpers::esc($post['author_name'] ?? 'Admin') ?></td>
                                <td>
                                    <?php
                                        $statusClass = match($post['status']) {
                                            'published' => 'bg-success',
                                            'draft'     => 'bg-secondary',
                                            'archived'  => 'bg-warning',
                                            default     => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst($post['status']) ?></span>
                                </td>
                                <td>
                                    <?= $post['published_at'] ? date('d M Y', strtotime($post['published_at'])) : '—' ?>
                                </td>
                                <td>
                                    <a href="<?= admin_path('blog/' . $post['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= admin_path('blog/' . $post['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this post?');">
                                        <?= Csrf::field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['last_page'] > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="<?= admin_path('blog?page=' . $i) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

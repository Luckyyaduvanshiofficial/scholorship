<?php
/**
 * Main Website Homepage
 *
 * Available: $upcomingEvents, $latestPosts, $isLoggedIn, $userName
 */
declare(strict_types=1);

use App\Core\Helpers;
use App\Core\Url;
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>तम्बोली समाज</h1>
        <p class="mb-0">Tamboli Samaj — Community, Education & Culture</p>
        <?php if (!$isLoggedIn): ?>
            <div class="mt-3">
                <a href="<?= Url::portal('/register') ?>" class="btn btn-light btn-lg me-2">Join Our Community</a>
                <a href="/events" class="btn btn-outline-light btn-lg">View Events</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Upcoming Events -->
<?php if (!empty($upcomingEvents)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Upcoming Events <span class="lang-hi">आगामी कार्यक्रम</span></h2>
        <div class="row g-4">
            <?php foreach ($upcomingEvents as $event): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-event">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?= Helpers::esc($event['image']) ?>" class="card-img-top" alt="<?= Helpers::esc($event['title']) ?>" style="height:200px;object-fit:cover;">
                        <?php else: ?>
                            <div class="card-img-top d-flex align-items-center justify-content-center" style="height:200px;background:linear-gradient(135deg,#d4a017,#b8860b);color:#fff;">
                                <i class="bi bi-calendar-event" style="font-size:3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <span class="card-badge">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= date('d M Y', strtotime($event['event_date'])) ?>
                                </span>
                            </div>
                            <h5 class="card-title"><?= Helpers::esc($event['title']) ?></h5>
                            <?php if (!empty($event['excerpt'])): ?>
                                <p class="card-text text-muted small"><?= Helpers::esc($event['excerpt']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($event['location'])): ?>
                                <p class="card-text small mb-2">
                                    <i class="bi bi-geo-alt text-danger me-1"></i><?= Helpers::esc($event['location']) ?>
                                </p>
                            <?php endif; ?>
                            <a href="/events/<?= Helpers::esc($event['slug']) ?>" class="btn btn-outline-dark btn-sm mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/events" class="btn btn-outline-dark">View All Events <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Blog Posts -->
<?php if (!empty($latestPosts)): ?>
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="section-title">Latest Updates <span class="lang-hi">ताज़ा खबर</span></h2>
        <div class="row g-4">
            <?php foreach ($latestPosts as $post): ?>
                <div class="col-md-4">
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
                                    <?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                                </small>
                                <a href="/blog/<?= Helpers::esc($post['slug']) ?>" class="btn btn-outline-dark btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/blog" class="btn btn-outline-dark">View All Posts <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Quick Links -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="section-title text-center">Quick Links <span class="lang-hi">त्वरित लिंक</span></h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 col-6">
                <a href="/events" class="text-decoration-none">
                    <div class="p-4 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-calendar-event text-warning" style="font-size:2.5rem;"></i>
                        <h6 class="mt-2 text-dark">Events</h6>
                        <small class="text-muted">कार्यक्रम</small>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="/blog" class="text-decoration-none">
                    <div class="p-4 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-newspaper text-primary" style="font-size:2.5rem;"></i>
                        <h6 class="mt-2 text-dark">Blog</h6>
                        <small class="text-muted">ब्लॉग</small>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="<?= Url::portal('/dashboard') ?>" class="text-decoration-none">
                    <div class="p-4 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-person-badge text-success" style="font-size:2.5rem;"></i>
                        <h6 class="mt-2 text-dark">Portal</h6>
                        <small class="text-muted">पोर्टल</small>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="/about" class="text-decoration-none">
                    <div class="p-4 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-info-circle text-info" style="font-size:2.5rem;"></i>
                        <h6 class="mt-2 text-dark">About</h6>
                        <small class="text-muted">हमारे बारे में</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

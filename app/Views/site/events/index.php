<?php
/**
 * Events List Page
 *
 * Available: $events, $pagination, $isLoggedIn, $userName
 */
declare(strict_types=1);

use App\Core\Helpers;
?>

<section class="py-5">
    <div class="container">
        <h1 class="section-title">Events <span class="lang-hi">कार्यक्रम</span></h1>

        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size:4rem;"></i>
                <h4 class="mt-3 text-muted">No upcoming events</h4>
                <p class="text-muted">कोई आगामी कार्यक्रम नहीं</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($events as $event): ?>
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
                                    <?php if ($event['registration_required']): ?>
                                        <span class="badge bg-info ms-1">Registration Required</span>
                                    <?php endif; ?>
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
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <?php if ($event['registration_required'] && $event['max_participants']): ?>
                                        <small class="text-muted">
                                            <?= $event['registration_count'] ?? 0 ?>/<?= $event['max_participants'] ?> registered
                                        </small>
                                    <?php endif; ?>
                                    <a href="/events/<?= Helpers::esc($event['slug']) ?>" class="btn btn-outline-dark btn-sm">View Details</a>
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
                                <a class="page-link" href="/events?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

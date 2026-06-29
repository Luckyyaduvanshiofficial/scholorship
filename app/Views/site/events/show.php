<?php
/**
 * Event Detail Page
 *
 * Available: $event, $isRegistered, $isLoggedIn, $userName
 */
declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helpers;
?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Event Header -->
                <div class="mb-4">
                    <a href="/events" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Back to Events
                    </a>
                    <h1 class="mt-2"><?= Helpers::esc($event['title']) ?></h1>
                    <div class="d-flex flex-wrap gap-3 text-muted mt-3">
                        <span>
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= date('d M Y, h:i A', strtotime($event['event_date'])) ?>
                        </span>
                        <?php if (!empty($event['location'])): ?>
                            <span>
                                <i class="bi bi-geo-alt me-1"></i>
                                <?= Helpers::esc($event['location']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Event Image -->
                <?php if (!empty($event['image'])): ?>
                    <img src="<?= Helpers::esc($event['image']) ?>" class="img-fluid rounded mb-4" alt="<?= Helpers::esc($event['title']) ?>">
                <?php endif; ?>

                <!-- Event Description -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <?php if (!empty($event['description'])): ?>
                            <div class="event-content">
                                <?= nl2br(Helpers::esc($event['description'])) ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No detailed description available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Event Info Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Event Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <i class="bi bi-calendar3 text-warning me-2"></i>
                                <strong>Date:</strong><br>
                                <span class="ms-4"><?= date('l, d M Y', strtotime($event['event_date'])) ?></span>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-clock text-warning me-2"></i>
                                <strong>Time:</strong><br>
                                <span class="ms-4"><?= date('h:i A', strtotime($event['event_date'])) ?></span>
                            </li>
                            <?php if (!empty($event['location'])): ?>
                                <li class="mb-3">
                                    <i class="bi bi-geo-alt text-danger me-2"></i>
                                    <strong>Location:</strong><br>
                                    <span class="ms-4"><?= Helpers::esc($event['location']) ?></span>
                                </li>
                            <?php endif; ?>
                            <li class="mb-3">
                                <i class="bi bi-people text-info me-2"></i>
                                <strong>Registrations:</strong><br>
                                <span class="ms-4">
                                    <?= $event['registration_count'] ?? 0 ?>
                                    <?php if ($event['max_participants']): ?>
                                        / <?= $event['max_participants'] ?>
                                    <?php endif; ?>
                                    registered
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Registration Card -->
                <?php if ($event['registration_required']): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Registration</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!$isLoggedIn): ?>
                                <p class="text-muted">Please login to register for this event.</p>
                                <a href="/login" class="btn btn-warning w-100">Login to Register</a>
                            <?php elseif ($isRegistered): ?>
                                <div class="text-center">
                                    <i class="bi bi-check-circle text-success" style="font-size:3rem;"></i>
                                    <h5 class="mt-2 text-success">You're Registered!</h5>
                                    <p class="text-muted small">You have already registered for this event.</p>
                                </div>
                            <?php else: ?>
                                <?php
                                    $isFull = $event['max_participants'] && ($event['registration_count'] ?? 0) >= $event['max_participants'];
                                ?>
                                <?php if ($isFull): ?>
                                    <p class="text-danger fw-bold">Registration is full.</p>
                                <?php else: ?>
                                    <form method="POST" action="/events/<?= Helpers::esc($event['slug']) ?>/register">
                                        <?= Csrf::field() ?>
                                        <div class="mb-3">
                                            <label class="form-label small">Name</label>
                                            <input type="text" name="name" class="form-control" value="<?= Helpers::esc($userName) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small">Mobile (optional)</label>
                                            <input type="text" name="mobile" class="form-control" placeholder="+91 XXXXX XXXXX">
                                        </div>
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="bi bi-check-lg me-1"></i> Register Now
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

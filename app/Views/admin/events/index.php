<?php
/**
 * Admin Events List
 *
 * Available: $events, $pagination
 */
declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helpers;
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Events</h2>
        <a href="<?= admin_path('events/create') ?>" class="btn btn-warning">
            <i class="bi bi-plus-lg me-1"></i> Create Event
        </a>
    </div>

    <?php if (empty($events)): ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size:3rem;"></i>
            <p class="mt-2 text-muted">No events found. Create your first event!</p>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Registrations</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <strong><?= Helpers::esc($event['title']) ?></strong>
                                    <?php if ($event['registration_required']): ?>
                                        <span class="badge bg-info ms-1">Reg Required</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y', strtotime($event['event_date'])) ?></td>
                                <td><?= Helpers::esc($event['location'] ?? '—') ?></td>
                                <td><?= $event['registration_count'] ?? 0 ?></td>
                                <td>
                                    <?php if ($event['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= admin_path('events/' . $event['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= admin_path('events/' . $event['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this event?');">
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
                            <a class="page-link" href="<?= admin_path('events?page=' . $i) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

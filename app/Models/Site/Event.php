<?php

declare(strict_types=1);

namespace App\Models\Site;

use App\Core\Database;
use App\Core\Helpers;
use PDO;

class Event
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get paginated active events.
     */
    public function getAll(int $perPage = 12, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("
            SELECT e.*, u.username AS creator_name,
                   (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status = 'registered') AS registration_count
            FROM events e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.is_active = 1
            ORDER BY e.event_date DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->db->query("SELECT COUNT(*) FROM events WHERE is_active = 1");
        $total = (int) $countStmt->fetchColumn();

        return [
            'data'       => $events,
            'total'      => $total,
            'per_page'   => $perPage,
            'current_page' => $page,
            'last_page'  => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Get upcoming events for homepage.
     */
    public function getUpcoming(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, u.username AS creator_name,
                   (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status = 'registered') AS registration_count
            FROM events e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.is_active = 1 AND e.event_date >= NOW()
            ORDER BY e.event_date ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get event by slug.
     */
    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, u.username AS creator_name,
                   (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status = 'registered') AS registration_count
            FROM events e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.slug = ?
        ");
        $stmt->execute([$slug]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        return $event ?: null;
    }

    /**
     * Get event by ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, u.username AS creator_name,
                   (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status = 'registered') AS registration_count
            FROM events e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        return $event ?: null;
    }

    /**
     * Get all events (admin — includes inactive).
     */
    public function getAllAdmin(int $perPage = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("
            SELECT e.*, u.username AS creator_name,
                   (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status = 'registered') AS registration_count
            FROM events e
            LEFT JOIN users u ON e.created_by = u.id
            ORDER BY e.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->db->query("SELECT COUNT(*) FROM events");
        $total = (int) $countStmt->fetchColumn();

        return [
            'data'       => $events,
            'total'      => $total,
            'per_page'   => $perPage,
            'current_page' => $page,
            'last_page'  => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Create a new event.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO events (title, slug, excerpt, description, event_date, location, image, is_active, registration_required, max_participants, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['excerpt'] ?? null,
            $data['description'] ?? null,
            $data['event_date'],
            $data['location'] ?? null,
            $data['image'] ?? null,
            $data['is_active'] ?? 1,
            $data['registration_required'] ?? 0,
            $data['max_participants'] ?? null,
            $data['created_by'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an event.
     */
    public function update(int $id, array $data): bool
    {
        $allowed = ['title', 'slug', 'excerpt', 'description', 'event_date', 'location', 'image', 'is_active', 'registration_required', 'max_participants'];
        $fields = [];
        $values = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "`{$field}` = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;
        $sql = "UPDATE events SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($values);
    }

    /**
     * Delete an event.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");

        return $stmt->execute([$id]);
    }

    /**
     * Generate a unique slug from title.
     */
    public function generateSlug(string $title): string
    {
        $slug = Helpers::slug($title);
        $original = $slug;
        $counter = 1;

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM events WHERE slug = ?");
        while (true) {
            $stmt->execute([$slug]);
            if ((int) $stmt->fetchColumn() === 0) {
                return $slug;
            }
            $slug = $original . '-' . $counter;
            $counter++;
        }
    }
}

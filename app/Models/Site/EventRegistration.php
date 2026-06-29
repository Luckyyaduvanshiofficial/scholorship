<?php

declare(strict_types=1);

namespace App\Models\Site;

use App\Core\Database;
use PDO;

class EventRegistration
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Register a user for an event.
     */
    public function register(int $eventId, int $userId, string $name, string $mobile = ''): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO event_registrations (event_id, user_id, name, mobile)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE name = VALUES(name), mobile = VALUES(mobile), status = 'registered'
        ");
        $stmt->execute([$eventId, $userId, $name, $mobile]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Check if a user is registered for an event.
     */
    public function isRegistered(int $eventId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM event_registrations
            WHERE event_id = ? AND user_id = ? AND status = 'registered'
        ");
        $stmt->execute([$eventId, $userId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Get all registrations for an event.
     */
    public function getForEvent(int $eventId): array
    {
        $stmt = $this->db->prepare("
            SELECT er.*, u.email
            FROM event_registrations er
            LEFT JOIN users u ON er.user_id = u.id
            WHERE er.event_id = ?
            ORDER BY er.registered_at DESC
        ");
        $stmt->execute([$eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get events a user is registered for.
     */
    public function getForUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT er.*, e.title, e.slug, e.event_date, e.location
            FROM event_registrations er
            JOIN events e ON er.event_id = e.id
            WHERE er.user_id = ?
            ORDER BY e.event_date DESC
        ");
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cancel a registration.
     */
    public function cancel(int $eventId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE event_registrations SET status = 'cancelled'
            WHERE event_id = ? AND user_id = ?
        ");

        return $stmt->execute([$eventId, $userId]);
    }

    /**
     * Get registration count for an event.
     */
    public function countForEvent(int $eventId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM event_registrations
            WHERE event_id = ? AND status = 'registered'
        ");
        $stmt->execute([$eventId]);

        return (int) $stmt->fetchColumn();
    }
}

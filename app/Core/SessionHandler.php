<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Database-backed session handler for cross-subdomain authentication.
 *
 * Stores sessions in the `sessions` table instead of filesystem,
 * allowing shared login state across subdomains (portal., admin., etc.).
 */
class SessionHandler implements \SessionHandlerInterface
{
    private ?PDO $db = null;

    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        $stmt = $this->db()->prepare(
            "SELECT data FROM sessions WHERE id = ?"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetchColumn();

        return $result !== false ? (string) $result : '';
    }

    public function write(string $id, string $data): bool
    {
        $stmt = $this->db()->prepare("
            INSERT INTO sessions (id, data, last_access)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE data = VALUES(data), last_access = VALUES(last_access)
        ");

        return $stmt->execute([$id, $data, time()]);
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->db()->prepare("DELETE FROM sessions WHERE id = ?");

        return $stmt->execute([$id]);
    }

    public function gc(int $maxLifetime): int
    {
        $stmt = $this->db()->prepare(
            "DELETE FROM sessions WHERE last_access < ?"
        );
        $stmt->execute([time() - $maxLifetime]);

        return $stmt->rowCount();
    }

    private function db(): PDO
    {
        if ($this->db === null) {
            $this->db = Database::getInstance();
        }

        return $this->db;
    }
}

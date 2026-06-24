<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class ApplicationStatus
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM application_status ORDER BY id")->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM application_status WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByName(string $name): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM application_status WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
}

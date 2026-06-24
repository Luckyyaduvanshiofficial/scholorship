<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class AcademicSession
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM academic_sessions ORDER BY session_name DESC")->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM academic_sessions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function active(): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM academic_sessions WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findBySessionName(string $name): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM academic_sessions WHERE session_name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
}

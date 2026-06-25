<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new admin/representative user.
     */
    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash, role, status, created_at)
             VALUES (:name, :email, :password_hash, :role, :status, NOW())"
        );

        $result = $stmt->execute([
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':role'          => $data['role'] ?? 'representative',
            ':status'        => $data['status'] ?? 1,
        ]);

        return $result ? (int) $this->db->lastInsertId() : false;
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByRole(string $role): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        $values = [];

        foreach ($data as $key => $value) {
            $sets[] = "`$key` = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function updatePassword(int $id, string $hash): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM users")->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
        $stmt->execute([$role]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Student
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new student.
     */
    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare(
            "INSERT INTO students
             (id, student_code, first_name, last_name, gender, dob, mobile, email,
              father_name, mother_name, address, city, district, state, pincode,
              profile_photo, status, created_at)
             VALUES
             (:id, :student_code, :first_name, :last_name, :gender, :dob, :mobile, :email,
              :father_name, :mother_name, :address, :city, :district, :state, :pincode,
              :profile_photo, :status, NOW())"
        );

        $result = $stmt->execute([
            ':id'            => $data['id'],
            ':student_code'  => $data['student_code'] ?? null,
            ':first_name'    => $data['first_name'] ?? null,
            ':last_name'     => $data['last_name'] ?? null,
            ':gender'        => $data['gender'] ?? null,
            ':dob'           => $data['dob'] ?? null,
            ':mobile'        => $data['mobile'] ?? null,
            ':email'         => $data['email'] ?? null,
            ':father_name'   => $data['father_name'] ?? null,
            ':mother_name'   => $data['mother_name'] ?? null,
            ':address'       => $data['address'] ?? null,
            ':city'          => $data['city'] ?? null,
            ':district'      => $data['district'] ?? null,
            ':state'         => $data['state'] ?? null,
            ':pincode'       => $data['pincode'] ?? null,
            ':profile_photo' => $data['profile_photo'] ?? null,
            ':status'        => $data['status'] ?? 1,
        ]);

        return $result ? (int) $data['id'] : false;
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByMobile(string $mobile): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE mobile = ? LIMIT 1");
        $stmt->execute([$mobile]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByCode(string $studentCode): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE student_code = ? LIMIT 1");
        $stmt->execute([$studentCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM students ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM students WHERE status = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        $values = [];

        $whitelist = [
            'student_code', 'first_name', 'last_name', 'gender', 'dob',
            'mobile', 'email', 'father_name', 'mother_name', 'address',
            'city', 'district', 'state', 'pincode', 'profile_photo', 'status'
        ];

        foreach ($data as $key => $value) {
            if (!in_array($key, $whitelist, true)) {
                throw new \InvalidArgumentException("Invalid column update requested: {$key}");
            }
            $sets[] = "`$key` = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE students SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM students WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM students")->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function countByStatus(int $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM students WHERE status = ?");
        $stmt->execute([$status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }
}

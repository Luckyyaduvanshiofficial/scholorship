<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Application
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new application (scholarship or pratibha).
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO applications
             (student_id, session_id, application_type_id, status_id, reviewed_by,
              dispute_message, submitted_at, type,
              family_income, bank_name, account_number, ifsc_code,
              achievement_title, achievement_category, achievement_level, rank_position,
              family_occupation, family_members_count, earning_members_count,
              current_class, current_college, prev_scholarship_received,
              scholarship_amt_2023_24, scholarship_amt_2024_25, scholarship_amt_2025_26,
              account_holder_name, career_goal,
              created_at)
             VALUES
             (:student_id, :session_id, :application_type_id, :status_id, :reviewed_by,
              :dispute_message, :submitted_at, :type,
              :family_income, :bank_name, :account_number, :ifsc_code,
              :achievement_title, :achievement_category, :achievement_level, :rank_position,
              :family_occupation, :family_members_count, :earning_members_count,
              :current_class, :current_college, :prev_scholarship_received,
              :scholarship_amt_2023_24, :scholarship_amt_2024_25, :scholarship_amt_2025_26,
              :account_holder_name, :career_goal,
              NOW())";

        $params = [
            ':student_id'          => $data['student_id'],
            ':session_id'          => $data['session_id'],
            ':application_type_id' => $data['application_type_id'],
            ':status_id'           => $data['status_id'] ?? 1,
            ':reviewed_by'         => $data['reviewed_by'] ?? null,
            ':dispute_message'     => $data['dispute_message'] ?? null,
            ':submitted_at'        => $data['submitted_at'] ?? null,
            ':type'                => $data['type'] ?? 'scholarship',
            ':family_income'       => $data['family_income'] ?? null,
            ':bank_name'           => $data['bank_name'] ?? null,
            ':account_number'      => $data['account_number'] ?? null,
            ':ifsc_code'           => $data['ifsc_code'] ?? null,
            ':achievement_title'   => $data['achievement_title'] ?? null,
            ':achievement_category'=> $data['achievement_category'] ?? null,
            ':achievement_level'   => $data['achievement_level'] ?? null,
            ':rank_position'       => $data['rank_position'] ?? null,
            ':family_occupation'         => $data['family_occupation'] ?? null,
            ':family_members_count'      => isset($data['family_members_count']) ? (int)$data['family_members_count'] : null,
            ':earning_members_count'     => isset($data['earning_members_count']) ? (int)$data['earning_members_count'] : null,
            ':current_class'             => $data['current_class'] ?? null,
            ':current_college'           => $data['current_college'] ?? null,
            ':prev_scholarship_received' => $data['prev_scholarship_received'] ?? null,
            ':scholarship_amt_2023_24'   => $data['scholarship_amt_2023_24'] ?? null,
            ':scholarship_amt_2024_25'   => $data['scholarship_amt_2024_25'] ?? null,
            ':scholarship_amt_2025_26'   => $data['scholarship_amt_2025_26'] ?? null,
            ':account_holder_name'       => $data['account_holder_name'] ?? null,
            ':career_goal'               => $data['career_goal'] ?? null,
        ];

        try {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
        } catch (\PDOException $e) {
            if ($e->getCode() === '42S22' || str_contains($e->getMessage(), 'Unknown column')) {
                $this->autoMigrateSchema();
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute($params);
            } else {
                throw $e;
            }
        }

        return $result ? (int) $this->db->lastInsertId() : false;
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, 
                    s.first_name, s.last_name, s.student_code, s.father_name, s.mother_name, 
                    s.dob, s.gender, s.mobile, s.email, s.address, s.city, s.district, s.state, s.pincode, s.profile_photo,
                    sa.class_year, sa.course_name, sa.college_name, sa.board_university, sa.marks_obtained, sa.max_marks, sa.percentage,
                    ac.session_name, atp.name AS app_type_name, ast.name AS status_name
             FROM applications a
             LEFT JOIN students s ON a.student_id = s.id
             LEFT JOIN academic_sessions ac ON a.session_id = ac.id
             LEFT JOIN application_types atp ON a.application_type_id = atp.id
             LEFT JOIN application_status ast ON a.status_id = ast.id
             LEFT JOIN student_academics sa ON a.student_id = sa.student_id AND a.session_id = sa.session_id
             WHERE a.id = ?"
        );
        $stmt->execute([$id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($application) {
            $application['documents'] = $this->documents((int) $application['id']);
        }

        return $application;
    }

    public function findByStudent(int $studentId, int $sessionId, ?int $applicationTypeId = null): array|false
    {
        $sql = "SELECT * FROM applications WHERE student_id = ? AND session_id = ?";
        $params = [$studentId, $sessionId];

        if ($applicationTypeId !== null) {
            $sql .= " AND application_type_id = ?";
            $params[] = $applicationTypeId;
        }

        $stmt = $this->db->prepare($sql . " LIMIT 1");
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            "SELECT a.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                    s.student_code, ac.session_name,
                    atp.name AS app_type_name, ast.name AS status_name
             FROM applications a
             LEFT JOIN students s ON a.student_id = s.id
             LEFT JOIN academic_sessions ac ON a.session_id = ac.id
             LEFT JOIN application_types atp ON a.application_type_id = atp.id
             LEFT JOIN application_status ast ON a.status_id = ast.id
             ORDER BY a.created_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allBySession(int $sessionId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                    s.student_code,
                    atp.name AS app_type_name, ast.name AS status_name
             FROM applications a
             LEFT JOIN students s ON a.student_id = s.id
             LEFT JOIN application_types atp ON a.application_type_id = atp.id
             LEFT JOIN application_status ast ON a.status_id = ast.id
             WHERE a.session_id = ?
             ORDER BY a.created_at DESC"
        );
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByStudent(int $studentId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, ac.session_name, atp.name AS app_type_name, ast.name AS status_name
             FROM applications a
             LEFT JOIN academic_sessions ac ON a.session_id = ac.id
             LEFT JOIN application_types atp ON a.application_type_id = atp.id
             LEFT JOIN application_status ast ON a.status_id = ast.id
             WHERE a.student_id = ?
             ORDER BY a.created_at DESC"
        );
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByStatus(int $statusId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                    s.student_code, ac.session_name,
                    atp.name AS app_type_name, ast.name AS status_name
             FROM applications a
             LEFT JOIN students s ON a.student_id = s.id
             LEFT JOIN academic_sessions ac ON a.session_id = ac.id
             LEFT JOIN application_types atp ON a.application_type_id = atp.id
             LEFT JOIN application_status ast ON a.status_id = ast.id
             WHERE a.status_id = ?
             ORDER BY a.created_at DESC"
        );
        $stmt->execute([$statusId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByType(string $type): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                    s.student_code, ac.session_name,
                    ast.name AS status_name
             FROM applications a
             LEFT JOIN students s ON a.student_id = s.id
             LEFT JOIN academic_sessions ac ON a.session_id = ac.id
             LEFT JOIN application_status ast ON a.status_id = ast.id
             WHERE a.type = ?
             ORDER BY a.created_at DESC"
        );
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allPending(): array
    {
        return $this->allByStatus(1);
    }

    public function allApproved(): array
    {
        return $this->allByStatus(2);
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        $values = [];

        $whitelist = [
            'student_id', 'session_id', 'application_type_id', 'status_id',
            'reviewed_by', 'dispute_message', 'submitted_at', 'type',
            'family_income', 'bank_name', 'account_number', 'ifsc_code',
            'achievement_title', 'achievement_category', 'achievement_level', 'rank_position',
            'family_occupation', 'family_members_count', 'earning_members_count',
            'current_class', 'current_college', 'prev_scholarship_received',
            'scholarship_amt_2023_24', 'scholarship_amt_2024_25', 'scholarship_amt_2025_26',
            'account_holder_name', 'career_goal'
        ];

        foreach ($data as $key => $value) {
            if (!in_array($key, $whitelist, true)) {
                throw new \InvalidArgumentException("Invalid column update requested: {$key}");
            }
            $sets[] = "`$key` = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE applications SET " . implode(', ', $sets) . " WHERE id = ?";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (\PDOException $e) {
            if ($e->getCode() === '42S22' || str_contains($e->getMessage(), 'Unknown column')) {
                $this->autoMigrateSchema();
                $stmt = $this->db->prepare($sql);
                return $stmt->execute($values);
            } else {
                throw $e;
            }
        }
    }

    public function updateStatus(int $id, int $statusId, ?int $reviewedBy = null): bool
    {
        if ($reviewedBy !== null) {
            $stmt = $this->db->prepare(
                "UPDATE applications SET status_id = ?, reviewed_by = ?, updated_at = NOW() WHERE id = ?"
            );
            return $stmt->execute([$statusId, $reviewedBy, $id]);
        }

        $stmt = $this->db->prepare(
            "UPDATE applications SET status_id = ?, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$statusId, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM applications WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM applications")->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function countBySession(int $sessionId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM applications WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function countByStatus(int $statusId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM applications WHERE status_id = ?");
        $stmt->execute([$statusId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function countByType(string $type): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM applications WHERE type = ?");
        $stmt->execute([$type]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function documentTypeId(string $name): ?int
    {
        $stmt = $this->db->prepare("SELECT id FROM document_types WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int) $row['id'] : null;
    }

    public function addDocument(int $applicationId, string $documentType, array $file, string $storedName): bool
    {
        $documentTypeId = $this->documentTypeId($documentType);

        if ($documentTypeId === null) {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO application_documents
             (application_id, document_type_id, original_name, stored_name, mime_type, file_size, uploaded_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );

        return $stmt->execute([
            $applicationId,
            $documentTypeId,
            $file['name'] ?? '',
            $storedName,
            $file['type'] ?? null,
            $file['size'] ?? null,
        ]);
    }

    public function documents(int $applicationId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ad.*, dt.name AS document_type
             FROM application_documents ad
             LEFT JOIN document_types dt ON ad.document_type_id = dt.id
             WHERE ad.application_id = ?
             ORDER BY ad.uploaded_at ASC"
        );
        $stmt->execute([$applicationId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Auto-migrate schema to add missing applications table columns on the fly.
     */
    private function autoMigrateSchema(): void
    {
        try {
            $columnsToAdd = [
                'family_occupation'         => 'VARCHAR(150) DEFAULT NULL',
                'family_members_count'      => 'INT DEFAULT NULL',
                'earning_members_count'     => 'INT DEFAULT NULL',
                'current_class'             => 'VARCHAR(50) DEFAULT NULL',
                'current_college'           => 'VARCHAR(150) DEFAULT NULL',
                'prev_scholarship_received' => 'VARCHAR(10) DEFAULT NULL',
                'scholarship_amt_2023_24'   => 'DECIMAL(10, 2) DEFAULT NULL',
                'scholarship_amt_2024_25'   => 'DECIMAL(10, 2) DEFAULT NULL',
                'scholarship_amt_2025_26'   => 'DECIMAL(10, 2) DEFAULT NULL',
                'account_holder_name'       => 'VARCHAR(100) DEFAULT NULL',
                'career_goal'               => 'VARCHAR(255) DEFAULT NULL',
            ];

            // Describe table to check columns
            $stmt = $this->db->query("DESCRIBE applications");
            $existingColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');

            foreach ($columnsToAdd as $columnName => $columnDefinition) {
                if (!in_array($columnName, $existingColumns, true)) {
                    $this->db->exec("ALTER TABLE applications ADD COLUMN `{$columnName}` {$columnDefinition}");
                }
            }
        } catch (\Throwable $e) {
            \App\Core\Logger::error('Auto-migration failed: ' . $e->getMessage());
        }
    }
}

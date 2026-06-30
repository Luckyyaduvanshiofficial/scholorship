<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Application
{
    private PDO $db;

    /** Columns in the parent `applications` table */
    private const PARENT_COLS = [
        'student_id', 'session_id', 'application_type_id', 'status_id',
        'application_no',
        'self_declared', 'self_declared_at', 'self_declared_ip',
        'submitted_at', 'submitted_ip', 'resubmitted_at',
        'correction_count', 'correction_deadline',
        'reviewed_by', 'rejection_reason', 'admin_remarks',
        'created_by', 'updated_by',
    ];

    /** Columns in `scholarship_details` child table */
    private const SCHOLARSHIP_COLS = [
        'family_income', 'bank_name', 'account_number', 'ifsc_code', 'account_holder_name',
        'family_occupation', 'family_members_count', 'earning_members_count',
        'current_class', 'current_college', 'career_goal', 'prev_scholarship_received',
    ];

    /** Columns in `pratibha_details` child table */
    private const PRATIBHA_COLS = [
        'achievement_title', 'achievement_category', 'achievement_level', 'rank_position',
        'family_occupation', 'family_members_count', 'earning_members_count', 'career_goal',
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ──────────────────────────────────────────────
    //  CREATE
    // ──────────────────────────────────────────────

    public function create(array $data): int|false
    {
        $this->db->beginTransaction();
        try {
            $appId = $this->insertParent($data);
            if ($appId === false) {
                $this->db->rollBack();
                return false;
            }

            $typeId = (int) ($data['application_type_id'] ?? 0);

            if ($typeId === 1) {
                $this->upsertScholarship($appId, $data);
            } elseif ($typeId === 2) {
                $this->upsertPratibha($appId, $data);
            }

            $this->upsertAcademics((int) $data['student_id'], (int) $data['session_id'], $data);

            $this->syncScholarshipHistory((int) $appId, $data);

            $this->db->commit();
            return $appId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function insertParent(array $data): int|false
    {
        $params = [];
        $cols = [];
        foreach (self::PARENT_COLS as $col) {
            if (array_key_exists($col, $data)) {
                $cols[] = "`$col`";
                $params[":$col"] = $data[$col];
            }
        }
        $cols[] = 'created_at';
        $colList = implode(', ', $cols);
        $valList = implode(', ', array_map(fn($c) => str_replace('`', ':', $c), $cols));

        $sql = "INSERT INTO applications ({$colList}) VALUES ({$valList}, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params) ? (int) $this->db->lastInsertId() : false;
    }

    // ──────────────────────────────────────────────
    //  READ
    // ──────────────────────────────────────────────

    public function find(int $id): array|false
    {
        $sql = "SELECT a.*,
                       s.first_name, s.last_name, s.student_code, s.father_name, s.mother_name,
                       s.dob, s.gender, s.mobile, s.email, s.address, s.city, s.district, s.state, s.pincode, s.profile_photo,
                       sa.class_year, sa.course_name, sa.college_name, sa.board_university,
                       sa.marks_obtained, sa.max_marks, sa.percentage,
                       ac.session_name, atp.name AS app_type_name, ast.name AS status_name,
                       sd.family_income, sd.bank_name, sd.account_number, sd.ifsc_code, sd.account_holder_name,
                       COALESCE(sd.family_occupation, pd.family_occupation) AS family_occupation,
                       COALESCE(sd.family_members_count, pd.family_members_count) AS family_members_count,
                       COALESCE(sd.earning_members_count, pd.earning_members_count) AS earning_members_count,
                       COALESCE(sd.career_goal, pd.career_goal) AS career_goal,
                       sd.current_class, sd.current_college, sd.prev_scholarship_received,
                       pd.achievement_title, pd.achievement_category, pd.achievement_level, pd.rank_position,
                       CASE WHEN a.application_type_id = 1 THEN 'scholarship' ELSE 'pratibha' END AS `type`
                FROM applications a
                LEFT JOIN students s ON a.student_id = s.id
                LEFT JOIN student_academics sa ON a.student_id = sa.student_id AND a.session_id = sa.session_id
                LEFT JOIN academic_sessions ac ON a.session_id = ac.id
                LEFT JOIN application_types atp ON a.application_type_id = atp.id
                LEFT JOIN application_status ast ON a.status_id = ast.id
                LEFT JOIN scholarship_details sd ON a.id = sd.application_id
                LEFT JOIN pratibha_details pd ON a.id = pd.application_id
                WHERE a.id = ? AND a.deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($application) {
            $application['documents'] = $this->documents((int) $application['id']);
            $application['history'] = $this->history((int) $application['id']);

            $scholarshipHistory = $this->getScholarshipHistory((int) $application['id']);
            foreach ($scholarshipHistory as $sh) {
                $yearKey = 'scholarship_amt_' . str_replace('-', '_', $sh['session_year']);
                $application[$yearKey] = $sh['amount'];
            }
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

    // ──────────────────────────────────────────────
    //  LIST QUERIES
    // ──────────────────────────────────────────────

    private function listQuery(string $where = '', array $params = []): array
    {
        $sql = "SELECT a.id, a.application_no, a.application_type_id, a.status_id,
                       a.submitted_at, a.created_at, a.rejection_reason, a.correction_count, a.correction_deadline,
                       CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                       s.student_code, ac.session_name,
                       atp.name AS app_type_name, ast.name AS status_name,
                       CASE WHEN a.application_type_id = 1 THEN 'scholarship' ELSE 'pratibha' END AS `type`
                FROM applications a
                LEFT JOIN students s ON a.student_id = s.id
                LEFT JOIN academic_sessions ac ON a.session_id = ac.id
                LEFT JOIN application_types atp ON a.application_type_id = atp.id
                LEFT JOIN application_status ast ON a.status_id = ast.id";

        $conditions = ['a.deleted_at IS NULL'];
        if ($where) {
            $conditions[] = $where;
        }
        $sql .= ' WHERE ' . implode(' AND ', $conditions);

        $sql .= " ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        return $this->listQuery();
    }

    public function allBySession(int $sessionId): array
    {
        return $this->listQuery('a.session_id = ?', [$sessionId]);
    }

    public function allByStudent(int $studentId): array
    {
        return $this->listQuery('a.student_id = ?', [$studentId]);
    }

    public function allByStatus(int $statusId): array
    {
        return $this->listQuery('a.status_id = ?', [$statusId]);
    }

    // ──────────────────────────────────────────────
    //  UPDATE
    // ──────────────────────────────────────────────

    public function update(int $id, array $data): bool
    {
        $parentData = $this->filterData($data, self::PARENT_COLS);
        if (!empty($parentData)) {
            $parentData['updated_at'] = date('Y-m-d H:i:s');
            if (!$this->updateParent($id, $parentData)) {
                return false;
            }
        }

        $app = $this->find($id);
        if (!$app) {
            return false;
        }

        $typeId = (int) $app['application_type_id'];

        $schData = $this->filterData($data, self::SCHOLARSHIP_COLS);
        if (!empty($schData) && $typeId === 1) {
            $this->upsertScholarship($id, $schData);
        }

        $pratData = $this->filterData($data, self::PRATIBHA_COLS);
        if (!empty($pratData) && $typeId === 2) {
            $this->upsertPratibha($id, $pratData);
        }

        $acadCols = ['course_name', 'class_year', 'college_name', 'board_university',
                      'marks_obtained', 'max_marks', 'percentage'];
        $acadData = $this->filterData($data, $acadCols);
        if (!empty($acadData)) {
            $this->upsertAcademics((int) $app['student_id'], (int) $app['session_id'], $acadData);
        }

        $this->syncScholarshipHistory($id, $data);

        return true;
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

    // ──────────────────────────────────────────────
    //  DELETE / SOFT DELETE
    // ──────────────────────────────────────────────

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE applications SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ──────────────────────────────────────────────
    //  COUNTS
    // ──────────────────────────────────────────────

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM applications")->fetchColumn();
    }

    public function countBySession(int $sessionId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM applications WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        return (int) $stmt->fetchColumn();
    }

    public function countByStatus(int $statusId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM applications WHERE status_id = ?");
        $stmt->execute([$statusId]);
        return (int) $stmt->fetchColumn();
    }

    // ──────────────────────────────────────────────
    //  DOCUMENTS
    // ──────────────────────────────────────────────

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

        $storagePath = 'uploads/applications/' . $applicationId . '/';
        $uploadedBy = isset($_SESSION['auth_user_id']) ? (int) $_SESSION['auth_user_id'] : null;
        $uploadedIp = $_SERVER['REMOTE_ADDR'] ?? null;

        $stmt = $this->db->prepare(
            "INSERT INTO application_documents
             (application_id, document_type_id, original_name, stored_name, storage_path, mime_type, file_size, uploaded_by, uploaded_ip, uploaded_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        return $stmt->execute([
            $applicationId,
            $documentTypeId,
            $file['name'] ?? '',
            $storedName,
            $storagePath,
            $file['type'] ?? null,
            $file['size'] ?? null,
            $uploadedBy,
            $uploadedIp,
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

    public function getScholarshipHistory(int $applicationId): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, session_year, amount FROM scholarship_history WHERE application_id = ? ORDER BY session_year ASC"
        );
        $stmt->execute([$applicationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ──────────────────────────────────────────────
    //  HISTORY
    // ──────────────────────────────────────────────

    public function history(int $applicationId): array
    {
        $stmt = $this->db->prepare(
            "SELECT h.*, u.username, u.email,
                    COALESCE(CONCAT(s.first_name, ' ', s.last_name), u.email) AS performer_name
             FROM application_history h
             LEFT JOIN users u ON h.performed_by = u.id
             LEFT JOIN students s ON h.performed_by = s.id
             WHERE h.application_id = ?
             ORDER BY h.performed_at DESC"
        );
        $stmt->execute([$applicationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function logHistory(int $applicationId, string $action, int $performedBy, ?array $oldData = null, ?array $newData = null): bool
    {
        $sql = "INSERT INTO application_history
                (application_id, action, performed_by, performed_at, ip_address, user_agent, old_data, new_data)
                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)";

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $oldDataJson = $oldData !== null ? json_encode($oldData) : null;
        $newDataJson = $newData !== null ? json_encode($newData) : null;

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$applicationId, $action, $performedBy, $ip, $userAgent, $oldDataJson, $newDataJson]);
        } catch (\Throwable $e) {
            \App\Core\Logger::error("Failed to log history for app {$applicationId}: " . $e->getMessage());
            return false;
        }
    }

    // ──────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ──────────────────────────────────────────────

    private function filterData(array $data, array $allowed): array
    {
        $filtered = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $filtered[$col] = $data[$col];
            }
        }
        return $filtered;
    }

    private function updateParent(int $id, array $data): bool
    {
        $sets = [];
        $values = [];
        foreach ($data as $col => $val) {
            $sets[] = "`$col` = ?";
            $values[] = $val;
        }
        $values[] = $id;

        $sql = "UPDATE applications SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    private function upsertScholarship(int $applicationId, array $data): void
    {
        $fields = $this->filterData($data, self::SCHOLARSHIP_COLS);
        if (empty($fields)) {
            return;
        }

        $sets = [];
        $values = [];
        foreach ($fields as $col => $val) {
            $sets[] = "`$col` = ?";
            $values[] = $val;
        }
        $values[] = $applicationId;
        $values[] = $applicationId;

        $sql = "INSERT INTO scholarship_details (application_id, " . implode(', ', array_map(fn($c) => "`$c`", array_keys($fields))) . ")
                VALUES (?, " . implode(', ', array_fill(0, count($fields), '?')) . ")
                ON DUPLICATE KEY UPDATE " . implode(', ', $sets);
        $this->db->prepare($sql)->execute($values);
    }

    private function upsertPratibha(int $applicationId, array $data): void
    {
        $fields = $this->filterData($data, self::PRATIBHA_COLS);
        if (empty($fields)) {
            return;
        }

        $sets = [];
        $values = [];
        foreach ($fields as $col => $val) {
            $sets[] = "`$col` = ?";
            $values[] = $val;
        }
        $values[] = $applicationId;
        $values[] = $applicationId;

        $sql = "INSERT INTO pratibha_details (application_id, " . implode(', ', array_map(fn($c) => "`$c`", array_keys($fields))) . ")
                VALUES (?, " . implode(', ', array_fill(0, count($fields), '?')) . ")
                ON DUPLICATE KEY UPDATE " . implode(', ', $sets);
        $this->db->prepare($sql)->execute($values);
    }

    private function upsertAcademics(int $studentId, int $sessionId, array $data): void
    {
        $allowed = ['course_name', 'class_year', 'college_name', 'board_university',
                     'marks_obtained', 'max_marks', 'percentage'];
        $fields = $this->filterData($data, $allowed);
        if (empty($fields)) {
            return;
        }

        $colNames = array_keys($fields);
        $placeholders = [];
        $values = [$studentId, $sessionId];

        foreach ($fields as $val) {
            $values[] = $val;
        }

        foreach ($colNames as $col) {
            $placeholders[] = '?';
        }

        $updates = implode(', ', array_map(fn($c) => "`$c` = VALUES(`$c`)", $colNames));

        $sql = "INSERT INTO student_academics (student_id, session_id, " . implode(', ', array_map(fn($c) => "`$c`", $colNames)) . ")
                VALUES (?, ?, " . implode(', ', $placeholders) . ")
                ON DUPLICATE KEY UPDATE {$updates}";

        $this->db->prepare($sql)->execute($values);
    }

    // ──────────────────────────────────────────────
    //  WORKFLOW METHODS
    // ──────────────────────────────────────────────

    /**
     * Check whether an application has all required fields and documents filled.
     * Returns true only if the application is complete enough for final submit.
     */
    public function isComplete(int $id): bool
    {
        $app = $this->find($id);
        if (!$app) {
            return false;
        }

        $typeId = (int) ($app['application_type_id'] ?? 0);

        // Personal fields
        $requiredPersonal = ['first_name', 'last_name', 'father_name', 'mother_name',
                              'dob', 'gender', 'address', 'city', 'district', 'pincode',
                              'family_occupation', 'family_members_count', 'earning_members_count', 'career_goal'];
        foreach ($requiredPersonal as $f) {
            if (empty($app[$f])) {
                return false;
            }
        }

        // Academic fields
        $requiredAcademic = ['class_year', 'percentage'];
        foreach ($requiredAcademic as $f) {
            if (empty($app[$f])) {
                return false;
            }
        }

        // Type-specific fields
        if ($typeId === 1) {
            $required = ['current_class', 'current_college', 'bank_name',
                          'account_number', 'ifsc_code', 'account_holder_name', 'family_income'];
            foreach ($required as $f) {
                if (empty($app[$f])) {
                    return false;
                }
            }
        } else {
            if (empty($app['achievement_title'])) {
                return false;
            }
        }

        // Required documents
        $requiredDocs = ($typeId === 1)
            ? ['Photo', 'Signature', 'Marksheet', 'Passbook']
            : ['Photo', 'Signature', 'Marksheet', 'Certificate'];

        $uploadedTypes = array_column($app['documents'] ?? [], 'document_type');
        foreach ($requiredDocs as $doc) {
            if (!in_array($doc, $uploadedTypes, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Atomically generate and set the application number using the counters table.
     * Uses a FOR UPDATE row lock to prevent duplicates under concurrent submissions.
     */
    public function generateApplicationNumber(int $applicationId, int $typeId): string
    {
        $year = (int) date('Y');
        $typeKey = ($typeId === 1) ? 'scholarship' : 'pratibha';

        $ownsTransaction = !$this->db->inTransaction();
        if ($ownsTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT counter FROM application_counters
                 WHERE year = ? AND type = ? FOR UPDATE"
            );
            $stmt->execute([$year, $typeKey]);
            $counter = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($counter) {
                $newNumber = (int) $counter['counter'] + 1;
                $stmt = $this->db->prepare(
                    "UPDATE application_counters SET counter = ? WHERE year = ? AND type = ?"
                );
                $stmt->execute([$newNumber, $year, $typeKey]);
            } else {
                $newNumber = 1;
                $stmt = $this->db->prepare(
                    "INSERT INTO application_counters (year, type, counter) VALUES (?, ?, ?)"
                );
                $stmt->execute([$year, $typeKey, $newNumber]);
            }

            $prefix = ($typeId === 1) ? 'TSVS' : 'TSVP';
            $appNo = sprintf('%s-%d-%04d', $prefix, $year, $newNumber);

            $stmt = $this->db->prepare("UPDATE applications SET application_no = ? WHERE id = ?");
            $stmt->execute([$appNo, $applicationId]);

            if ($ownsTransaction) {
                $this->db->commit();
            }

            return $appNo;
        } catch (\Throwable $e) {
            if ($ownsTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            \App\Core\Logger::error("Atomic app number generation failed for app {$applicationId}: " . $e->getMessage());
            return \App\Core\ApplicationNumberGenerator::format($applicationId, (string) $year, $typeId);
        }
    }

    private function syncScholarshipHistory(int $applicationId, array $data): void
    {
        $yearKeyMap = [
            'scholarship_amt_2023_24' => '2023-24',
            'scholarship_amt_2024_25' => '2024-25',
            'scholarship_amt_2025_26' => '2025-26',
            'scholarship_amt_2026_27' => '2026-27',
        ];

        foreach ($yearKeyMap as $field => $sessionYear) {
            if (array_key_exists($field, $data)) {
                $amount = $data[$field];
                if ($amount !== null && $amount !== '' && (float) $amount > 0) {
                    $stmt = $this->db->prepare(
                        "INSERT INTO scholarship_history (application_id, session_year, amount)
                         VALUES (?, ?, ?)
                         ON DUPLICATE KEY UPDATE amount = VALUES(amount)"
                    );
                    $stmt->execute([$applicationId, $sessionYear, (float) $amount]);
                } else {
                    $stmt = $this->db->prepare(
                        "DELETE FROM scholarship_history WHERE application_id = ? AND session_year = ?"
                    );
                    $stmt->execute([$applicationId, $sessionYear]);
                }
            }
        }
    }
}

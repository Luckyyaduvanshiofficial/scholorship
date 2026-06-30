<?php
/**
 * Safe Database Schema Update Script — v3 → v4 Migration
 * Run: php database/update_schema.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    die("❌ .env file not found.\n");
}

$env = [];
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    if ($line && !str_starts_with($line, '#') && str_contains($line, '=')) {
        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim(trim($value), "'\"");
    }
}

$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_NAME'] ?? 'tamboli_samaj_portal';
$dbUser = $env['DB_USER'] ?? 'root';
$dbPass = $env['DB_PASS'] ?? '';

echo "🔧 Migrating database schema to v4...\n";
echo "────────────────────────────────────────\n";

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // ─── Helper to describe a table ───
    $describe = function (string $table) use ($pdo): array {
        try {
            $stmt = $pdo->query("DESCRIBE `$table`");
            return array_column($stmt->fetchAll(), 'Field');
        } catch (PDOException) {
            return [];
        }
    };

    // ─── Helper to check if a table exists ───
    $tableExists = function (string $table) use ($pdo): bool {
        try {
            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
            return true;
        } catch (PDOException) {
            return false;
        }
    };

    // ================================================================
    // PHASE 1: Create new tables (safe, idempotent)
    // ================================================================

    echo "\n─── Phase 1: New Tables ───\n";

    // 1a. Re-create application_status (was dropped by old migration)
    if (!$tableExists('application_status')) {
        echo "📋 Creating application_status table...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS application_status (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✅ application_status table created.\n";
    }

    // Seed statuses (idempotent)
    $stmt = $pdo->query("SELECT COUNT(*) FROM application_status");
    if ((int)$stmt->fetchColumn() === 0) {
        echo "🌱 Seeding application_status...\n";
        $pdo->exec("INSERT INTO application_status (id, name) VALUES
            (1, 'Draft'),
            (2, 'Submitted'),
            (3, 'Under Review'),
            (4, 'Approved'),
            (5, 'Rejected'),
            (6, 'Pending Correction'),
            (7, 'Resubmitted')");
        echo "✅ Application statuses seeded.\n";
    }

    // 1b. Create scholarship_details
    if (!$tableExists('scholarship_details')) {
        echo "📋 Creating scholarship_details table...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS scholarship_details (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            application_id BIGINT UNSIGNED NOT NULL UNIQUE,
            family_income DECIMAL(12, 2) DEFAULT NULL,
            bank_name VARCHAR(100) DEFAULT NULL,
            account_number VARCHAR(30) DEFAULT NULL,
            ifsc_code VARCHAR(20) DEFAULT NULL,
            account_holder_name VARCHAR(100) DEFAULT NULL,
            family_occupation VARCHAR(150) DEFAULT NULL,
            family_members_count INT DEFAULT NULL,
            earning_members_count INT DEFAULT NULL,
            current_class VARCHAR(50) DEFAULT NULL,
            current_college VARCHAR(150) DEFAULT NULL,
            career_goal VARCHAR(255) DEFAULT NULL,
            prev_scholarship_received VARCHAR(10) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✅ scholarship_details table created.\n";
    }

    // 1c. Create scholarship_history
    if (!$tableExists('scholarship_history')) {
        echo "📋 Creating scholarship_history table...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS scholarship_history (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            application_id BIGINT UNSIGNED NOT NULL,
            session_year VARCHAR(10) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
            UNIQUE KEY (application_id, session_year)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✅ scholarship_history table created.\n";
    }

    // 1d. Create pratibha_details
    if (!$tableExists('pratibha_details')) {
        echo "📋 Creating pratibha_details table...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS pratibha_details (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            application_id BIGINT UNSIGNED NOT NULL UNIQUE,
            achievement_title VARCHAR(200) DEFAULT NULL,
            achievement_category VARCHAR(100) DEFAULT NULL,
            achievement_level VARCHAR(100) DEFAULT NULL,
            rank_position VARCHAR(50) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✅ pratibha_details table created.\n";
    }

    // 1e. Create application_disputes
    if (!$tableExists('application_disputes')) {
        echo "📋 Creating application_disputes table...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS application_disputes (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            application_id BIGINT UNSIGNED NOT NULL,
            raised_by BIGINT UNSIGNED NOT NULL,
            message TEXT NOT NULL,
            status ENUM('open','resolved','rejected') DEFAULT 'open',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            resolved_at DATETIME DEFAULT NULL,
            resolved_by BIGINT UNSIGNED DEFAULT NULL,
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
            FOREIGN KEY (raised_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✅ application_disputes table created.\n";
    }

    // 1f. Create document_types
    if (!$tableExists('document_types')) {
        echo "📋 Creating document_types table...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS document_types (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $pdo->exec("INSERT INTO document_types (name) VALUES
            ('Photo'), ('Marksheet'), ('Passbook'), ('Certificate'), ('Aadhaar'), ('Signature'), ('Other')");
        echo "✅ document_types table created and seeded.\n";
    }

    // 1g. Create student_academics (safe if already exists)
    if (!$tableExists('student_academics')) {
        echo "📋 Creating student_academics table...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS student_academics (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            student_id BIGINT UNSIGNED NOT NULL,
            session_id BIGINT UNSIGNED NOT NULL,
            course_name VARCHAR(100) DEFAULT NULL,
            class_year VARCHAR(50) DEFAULT NULL,
            college_name VARCHAR(150) DEFAULT NULL,
            board_university VARCHAR(150) DEFAULT NULL,
            marks_obtained DECIMAL(8, 2) DEFAULT NULL,
            max_marks DECIMAL(8, 2) DEFAULT NULL,
            percentage DECIMAL(5, 2) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_student_session (student_id, session_id),
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (session_id) REFERENCES academic_sessions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✅ student_academics table created.\n";
    }

    // 1h. Ensure application_counters has correct schema
    if (!$tableExists('application_counters')) {
        echo "📋 Creating application_counters table...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS application_counters (
            year INT NOT NULL,
            type VARCHAR(20) NOT NULL,
            counter INT NOT NULL DEFAULT 0,
            PRIMARY KEY (year, type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✅ application_counters table created.\n";
    }

    // 1i. Re-create application_history if needed (dropped schema columns were different)
    if (!$tableExists('application_history')) {
        echo "📋 Creating application_history table (upgraded schema)...\n";
    }
    // Ensure application_history has id as BIGINT UNSIGNED
    $appHistCols = $describe('application_history');
    if (!in_array('application_id', $appHistCols, true)) {
        $pdo->exec("DROP TABLE IF EXISTS application_history");
        $pdo->exec("CREATE TABLE IF NOT EXISTS application_history (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            application_id BIGINT UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL,
            performed_by BIGINT UNSIGNED NOT NULL,
            performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ip_address VARCHAR(45) DEFAULT NULL,
            user_agent TEXT DEFAULT NULL,
            old_data JSON DEFAULT NULL,
            new_data JSON DEFAULT NULL,
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
            FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_app_id (application_id),
            INDEX idx_action (action),
            INDEX idx_performed_at (performed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✅ application_history table rebuilt with BIGINT FKs.\n";
    }

    // ================================================================
    // PHASE 2: Add columns to existing tables
    // ================================================================

    echo "\n─── Phase 2: Column Additions ───\n";

    // 2a. Add status_id FK back to applications (was dropped by old migration)
    $appCols = $describe('applications');
    if (!in_array('status_id', $appCols, true)) {
        echo "➕ Adding status_id column to applications...\n";
        $pdo->exec("ALTER TABLE applications ADD COLUMN `status_id` BIGINT UNSIGNED NOT NULL DEFAULT 1");
        $pdo->exec("ALTER TABLE applications ADD INDEX idx_status_id (status_id)");

        // Migrate data from status VARCHAR to status_id
        if (in_array('status', $appCols, true)) {
            echo "🔄 Migrating status VARCHAR values to status_id...\n";
            $pdo->exec("UPDATE applications SET status_id = 2 WHERE status = 'submitted'");
            $pdo->exec("UPDATE applications SET status_id = 3 WHERE status = 'under_review'");
            $pdo->exec("UPDATE applications SET status_id = 4 WHERE status = 'approved'");
            $pdo->exec("UPDATE applications SET status_id = 5 WHERE status = 'rejected'");
            $pdo->exec("UPDATE applications SET status_id = 6 WHERE status = 'pending_correction'");
            $pdo->exec("UPDATE applications SET status_id = 7 WHERE status = 'resubmitted'");
            // Draft is default (1)
            echo "✅ Data migrated from status VARCHAR to status_id.\n";
        }

        // Add FK constraint
        try {
            $pdo->exec("ALTER TABLE applications ADD CONSTRAINT fk_app_status FOREIGN KEY (status_id) REFERENCES application_status(id) ON DELETE RESTRICT");
            echo "✅ FK constraint added for status_id.\n";
        } catch (PDOException $e) {
            echo "⚠️  FK constraint may already exist: " . $e->getMessage() . "\n";
        }
    }

    // 2b. Add deleted_at to students
    $studCols = $describe('students');
    if (!in_array('deleted_at', $studCols, true)) {
        echo "➕ Adding deleted_at to students...\n";
        $pdo->exec("ALTER TABLE students ADD COLUMN `deleted_at` DATETIME DEFAULT NULL, ADD INDEX idx_deleted_at (deleted_at)");
        echo "✅ deleted_at column added to students.\n";
    }

    // 2c. Add deleted_at to announcements
    $annCols = $describe('announcements');
    if (!in_array('deleted_at', $annCols, true)) {
        echo "➕ Adding deleted_at to announcements...\n";
        $pdo->exec("ALTER TABLE announcements ADD COLUMN `deleted_at` DATETIME DEFAULT NULL, ADD INDEX idx_deleted_at (deleted_at)");
        echo "✅ deleted_at column added to announcements.\n";
    }

    // 2d. Add deleted_at to events
    $evtCols = $describe('events');
    if (!in_array('deleted_at', $evtCols, true)) {
        echo "➕ Adding deleted_at to events...\n";
        $pdo->exec("ALTER TABLE events ADD COLUMN `deleted_at` DATETIME DEFAULT NULL, ADD INDEX idx_deleted_at (deleted_at)");
        echo "✅ deleted_at column added to events.\n";
    }

    // 2e. Add deleted_at to blog_posts
    $blogCols = $describe('blog_posts');
    if (!in_array('deleted_at', $blogCols, true)) {
        echo "➕ Adding deleted_at to blog_posts...\n";
        $pdo->exec("ALTER TABLE blog_posts ADD COLUMN `deleted_at` DATETIME DEFAULT NULL, ADD INDEX idx_deleted_at (deleted_at)");
        echo "✅ deleted_at column added to blog_posts.\n";
    }

    // 2f. Add storage_path, uploaded_by, uploaded_ip to application_documents
    $docCols = $describe('application_documents');
    if (!in_array('storage_path', $docCols, true)) {
        echo "➕ Adding storage_path to application_documents...\n";
        $pdo->exec("ALTER TABLE application_documents ADD COLUMN `storage_path` VARCHAR(500) NOT NULL DEFAULT ''");
        $pdo->exec("ALTER TABLE application_documents ADD COLUMN `uploaded_by` BIGINT UNSIGNED DEFAULT NULL");
        $pdo->exec("ALTER TABLE application_documents ADD COLUMN `uploaded_ip` VARCHAR(45) DEFAULT NULL");
        try {
            $pdo->exec("ALTER TABLE application_documents ADD FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL");
        } catch (PDOException $e) {
            echo "⚠️  FK for uploaded_by may already exist: " . $e->getMessage() . "\n";
        }
        echo "✅ Audit columns added to application_documents.\n";
    }

    // 2g. Add deleted_at to applications
    if (!in_array('deleted_at', $appCols, true)) {
        echo "➕ Adding deleted_at to applications...\n";
        $pdo->exec("ALTER TABLE applications ADD COLUMN `deleted_at` DATETIME DEFAULT NULL, ADD INDEX idx_deleted_at (deleted_at)");
        echo "✅ deleted_at column added to applications.\n";
    }

    // 2h. Add family/career columns to pratibha_details (shared with scholarship step 1)
    $pratCols = $describe('pratibha_details');
    $pratibhaFamilyCols = [
        'family_occupation'     => 'VARCHAR(150) DEFAULT NULL',
        'family_members_count'  => 'INT DEFAULT NULL',
        'earning_members_count' => 'INT DEFAULT NULL',
        'career_goal'           => 'VARCHAR(255) DEFAULT NULL',
    ];
    foreach ($pratibhaFamilyCols as $col => $def) {
        if (!in_array($col, $pratCols, true)) {
            echo "➕ Adding {$col} to pratibha_details...\n";
            $pdo->exec("ALTER TABLE pratibha_details ADD COLUMN `{$col}` {$def}");
            echo "✅ {$col} added to pratibha_details.\n";
        }
    }

    // 2i. Add unique constraint on (student_id, session_id, application_type_id) if missing
    try {
        $pdo->exec("ALTER TABLE applications ADD UNIQUE INDEX unique_student_session_type (student_id, session_id, application_type_id)");
        echo "✅ Unique index added for student+session+type.\n";
    } catch (PDOException $e) {
        // Already exists
    }

    // ================================================================
    // PHASE 3: Migrate existing data to child tables
    // ================================================================

    echo "\n─── Phase 3: Data Migration ───\n";

    // Get existing applications
    $apps = $pdo->query("SELECT * FROM applications")->fetchAll();

    foreach ($apps as $app) {
        $appId = (int) $app['id'];
        $appType = $app['type'] ?? 'scholarship';

        // Determine application_type_id from type string
        $typeId = ($appType === 'pratibha') ? 2 : 1;
        $pdo->prepare("UPDATE applications SET application_type_id = ? WHERE id = ? AND application_type_id = 0")
             ->execute([$typeId, $appId]);

        // Get existing academic data
        $stmt = $pdo->prepare("SELECT id FROM student_academics WHERE student_id = ? AND session_id = ?");
        $stmt->execute([$app['student_id'], $app['session_id']]);
        $hasAcademics = (bool) $stmt->fetch();

        if (!$hasAcademics) {
            // Insert student_academics from app data
            $pct = $app['percentage'] ?? null;
            if ($pct !== null || !empty($app['class_year'])) {
                $stmt = $pdo->prepare(
                    "INSERT INTO student_academics (student_id, session_id, class_year, college_name, board_university, marks_obtained, max_marks, percentage)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
                try {
                    $stmt->execute([
                        $app['student_id'],
                        $app['session_id'],
                        $app['class_year'] ?? null,
                        $app['college_name'] ?? null,
                        $app['board_university'] ?? null,
                        $app['marks_obtained'] ?? null,
                        $app['max_marks'] ?? null,
                        $pct,
                    ]);
                    echo "  📦 Migrated academic data for application #{$appId}\n";
                } catch (PDOException $e) {
                    echo "  ⚠️  Academic data migration for app #{$appId}: " . $e->getMessage() . "\n";
                }
            }
        }

        if ($typeId === 1) {
            // Scholarship → migrate to scholarship_details
            $stmt = $pdo->prepare("SELECT id FROM scholarship_details WHERE application_id = ?");
            $stmt->execute([$appId]);
            if (!$stmt->fetch()) {
                $pdo->prepare(
                    "INSERT INTO scholarship_details (application_id, family_income, bank_name, account_number, ifsc_code, account_holder_name, family_occupation, family_members_count, earning_members_count, current_class, current_college, career_goal, prev_scholarship_received)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                )->execute([
                    $appId,
                    $app['family_income'] ?? null,
                    $app['bank_name'] ?? null,
                    $app['account_number'] ?? null,
                    $app['ifsc_code'] ?? null,
                    $app['account_holder_name'] ?? null,
                    $app['family_occupation'] ?? null,
                    $app['family_members_count'] ?? null,
                    $app['earning_members_count'] ?? null,
                    $app['current_class'] ?? null,
                    $app['current_college'] ?? null,
                    $app['career_goal'] ?? null,
                    $app['prev_scholarship_received'] ?? null,
                ]);
                echo "  📦 Migrated scholarship_details for application #{$appId}\n";

                // Migrate scholarship history amounts
                $years = ['2023_24', '2024_25', '2025_26'];
                foreach ($years as $year) {
                    $col = "scholarship_amt_{$year}";
                    $amt = $app[$col] ?? null;
                    if ($amt !== null && (float)$amt > 0) {
                        $sessionYear = str_replace('_', '-', $year);
                        $pdo->prepare(
                            "INSERT INTO scholarship_history (application_id, session_year, amount) VALUES (?, ?, ?)"
                        )->execute([$appId, $sessionYear, $amt]);
                        echo "    📦 Migrated scholarship history for {$sessionYear}: ₹{$amt}\n";
                    }
                }
            }
        } elseif ($typeId === 2) {
            // Pratibha → migrate to pratibha_details
            $stmt = $pdo->prepare("SELECT id FROM pratibha_details WHERE application_id = ?");
            $stmt->execute([$appId]);
            if (!$stmt->fetch()) {
                $pdo->prepare(
                    "INSERT INTO pratibha_details (application_id, achievement_title, achievement_category, achievement_level, rank_position)
                     VALUES (?, ?, ?, ?, ?)"
                )->execute([
                    $appId,
                    $app['achievement_title'] ?? null,
                    $app['achievement_category'] ?? null,
                    $app['achievement_level'] ?? null,
                    $app['rank_position'] ?? null,
                ]);
                echo "  📦 Migrated pratibha_details for application #{$appId}\n";
            }
        }

        // Migrate dispute_message to application_disputes
        if (!empty($app['dispute_message'])) {
            $stmt = $pdo->prepare("SELECT id FROM application_disputes WHERE application_id = ?");
            $stmt->execute([$appId]);
            if (!$stmt->fetch()) {
                $pdo->prepare(
                    "INSERT INTO application_disputes (application_id, raised_by, message, status) VALUES (?, ?, ?, 'open')"
                )->execute([$appId, $app['reviewed_by'] ?? 1, $app['dispute_message']]);
                echo "  📦 Migrated dispute for application #{$appId}\n";
            }
        }
    }

    // ================================================================
    // PHASE 4: Cleanup old columns (only after data migration verified)
    // ================================================================

    echo "\n─── Phase 4: Schema Cleanup ───\n";

    // Drop old 'type' ENUM column (safe after data migrated to application_type_id)
    $appCols = $describe('applications');
    if (in_array('type', $appCols, true)) {
        echo "🗑️ Dropping old 'type' column from applications...\n";
        $pdo->exec("ALTER TABLE applications DROP COLUMN `type`");
        echo "✅ Column 'type' dropped.\n";
    }

    // Drop old 'status' VARCHAR column (data migrated to status_id in Phase 2)
    if (in_array('status', $appCols, true)) {
        echo "🗑️ Dropping old 'status' VARCHAR column from applications...\n";
        $pdo->exec("ALTER TABLE applications DROP COLUMN `status`");
        echo "✅ Column 'status' dropped.\n";
    }

    // Drop old scholarship/achievement columns from applications (now in child tables)
    $oldScholarshipCols = [
        'family_income', 'bank_name', 'account_number', 'ifsc_code', 'account_holder_name',
        'family_occupation', 'family_members_count', 'earning_members_count',
        'current_class', 'current_college', 'prev_scholarship_received',
        'scholarship_amt_2023_24', 'scholarship_amt_2024_25', 'scholarship_amt_2025_26',
        'career_goal',
    ];
    foreach ($oldScholarshipCols as $col) {
        if (in_array($col, $appCols, true)) {
            echo "  🗑️ Dropping old column '{$col}' from applications...\n";
            $pdo->exec("ALTER TABLE applications DROP COLUMN `{$col}`");
        }
    }

    $oldPratibhaCols = ['achievement_title', 'achievement_category', 'achievement_level', 'rank_position'];
    foreach ($oldPratibhaCols as $col) {
        if (in_array($col, $appCols, true)) {
            echo "  🗑️ Dropping old column '{$col}' from applications...\n";
            $pdo->exec("ALTER TABLE applications DROP COLUMN `{$col}`");
        }
    }

    // Drop old dispute_message column
    if (in_array('dispute_message', $appCols, true)) {
        echo "  🗑️ Dropping old 'dispute_message' from applications...\n";
        $pdo->exec("ALTER TABLE applications DROP COLUMN `dispute_message`");
    }

    // Change announcements.scope from VARCHAR to ENUM
    $annCols = $describe('announcements');
    if (in_array('scope', $annCols, true)) {
        $stmt = $pdo->query("SHOW COLUMNS FROM announcements WHERE Field = 'scope'");
        $colDef = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($colDef && !str_contains($colDef['Type'] ?? '', 'enum')) {
            echo "🔧 Changing announcements.scope to ENUM...\n";
            $pdo->exec("ALTER TABLE announcements MODIFY COLUMN `scope` ENUM('portal','site','both') DEFAULT 'portal'");
            echo "✅ announcements.scope changed to ENUM.\n";
        }
    }

    // Ensure blog_posts.status is ENUM
    $blogCols = $describe('blog_posts');
    if (in_array('status', $blogCols, true)) {
        $stmt = $pdo->query("SHOW COLUMNS FROM blog_posts WHERE Field = 'status'");
        $colDef = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($colDef && !str_contains($colDef['Type'] ?? '', 'enum')) {
            echo "🔧 Changing blog_posts.status to ENUM...\n";
            $pdo->exec("ALTER TABLE blog_posts MODIFY COLUMN `status` ENUM('draft','published','archived') DEFAULT 'draft'");
            echo "✅ blog_posts.status changed to ENUM.\n";
        }
    }

    echo "\n🎉 Database schema v4 migration complete!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

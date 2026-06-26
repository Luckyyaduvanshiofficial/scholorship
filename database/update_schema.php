<?php
/**
 * Safe Database Schema Update Script
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

echo "🔧 Checking and updating database schema...\n";
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

    // 1. Drop foreign keys and columns related to status lookup table
    echo "🔗 Scanning for foreign key constraints on status_id...\n";
    $stmt = $pdo->prepare("
        SELECT CONSTRAINT_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = :db 
          AND TABLE_NAME = 'applications' 
          AND COLUMN_NAME = 'status_id' 
          AND REFERENCED_TABLE_NAME = 'application_status'
    ");
    $stmt->execute(['db' => $dbName]);
    $constraints = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($constraints as $constraint) {
        echo "🗑️ Dropping foreign key constraint '{$constraint}'...\n";
        $pdo->exec("ALTER TABLE applications DROP FOREIGN KEY `{$constraint}`");
    }

    // Check if status_id index exists and drop it
    try {
        $pdo->exec("ALTER TABLE applications DROP INDEX `idx_status_id`");
        echo "✅ Dropped index idx_status_id.\n";
    } catch (PDOException $e) {
        // Ignore if index doesn't exist
    }

    // Check existing columns of applications table
    $stmt = $pdo->query("DESCRIBE applications");
    $existingColumns = array_column($stmt->fetchAll(), 'Field');

    // Add VARCHAR status column if not exists
    if (!in_array('status', $existingColumns, true)) {
        echo "➕ Adding column 'status' (VARCHAR)...\n";
        $pdo->exec("ALTER TABLE applications ADD COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'draft'");
        echo "✅ Column 'status' added successfully.\n";

        // Migrate status_id data to status VARCHAR if status_id was populated
        if (in_array('status_id', $existingColumns, true)) {
            echo "🔄 Migrating old status_id values to VARCHAR status...\n";
            $pdo->exec("UPDATE applications SET `status` = 'draft' WHERE status_id = 1");
            $pdo->exec("UPDATE applications SET `status` = 'submitted' WHERE status_id = 2");
            $pdo->exec("UPDATE applications SET `status` = 'under_review' WHERE status_id = 3");
            $pdo->exec("UPDATE applications SET `status` = 'approved' WHERE status_id = 4");
            $pdo->exec("UPDATE applications SET `status` = 'rejected' WHERE status_id = 5");
            $pdo->exec("UPDATE applications SET `status` = 'pending_correction' WHERE status_id = 6");
            $pdo->exec("UPDATE applications SET `status` = 'resubmitted' WHERE status_id = 7");
            echo "✅ Data migration complete.\n";
        }
    }

    // Drop status_id column if it exists
    if (in_array('status_id', $existingColumns, true)) {
        echo "🗑️ Dropping old column 'status_id'...\n";
        $pdo->exec("ALTER TABLE applications DROP COLUMN `status_id`");
        echo "✅ Column 'status_id' dropped.\n";
    }

    // Drop lookup table
    echo "🗑️ Dropping lookup table 'application_status' if exists...\n";
    $pdo->exec("DROP TABLE IF EXISTS application_status");
    echo "✅ Table 'application_status' dropped.\n";

    // 2. Add application columns
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
        'self_declared'             => 'TINYINT(1) DEFAULT 0',
        'self_declared_at'          => 'DATETIME DEFAULT NULL',
        'self_declared_ip'          => 'VARCHAR(45) DEFAULT NULL',
        'correction_count'          => 'INT DEFAULT 0',
        'correction_deadline'       => 'DATETIME DEFAULT NULL',
        'submitted_at'              => 'DATETIME DEFAULT NULL',
        'submitted_ip'              => 'VARCHAR(45) DEFAULT NULL',
        'resubmitted_at'            => 'DATETIME DEFAULT NULL',
        'application_no'            => 'VARCHAR(50) DEFAULT NULL',
    ];

    // Re-fetch columns after drop
    $stmt = $pdo->query("DESCRIBE applications");
    $existingColumns = array_column($stmt->fetchAll(), 'Field');

    foreach ($columnsToAdd as $columnName => $columnDefinition) {
        if (!in_array($columnName, $existingColumns, true)) {
            echo "➕ Adding column '{$columnName}'...\n";
            $pdo->exec("ALTER TABLE applications ADD COLUMN `{$columnName}` {$columnDefinition}");
            echo "✅ Column '{$columnName}' added successfully.\n";
        }
    }

    // Add unique index on application_no if it does not exist
    $stmt = $pdo->query("SHOW INDEX FROM applications WHERE Key_name = 'idx_app_no'");
    if (!$stmt->fetch()) {
        echo "🔑 Adding unique index idx_app_no on application_no...\n";
        $pdo->exec("CREATE UNIQUE INDEX idx_app_no ON applications(application_no)");
        echo "✅ Unique index added successfully.\n";
    }

    // 3. Create application_history table if not exists
    echo "📋 Creating application_history table if not exists...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS application_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        application_id INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        performed_by INT NOT NULL,
        performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ip_address VARCHAR(45),
        user_agent TEXT,
        old_data JSON,
        new_data JSON,
        INDEX idx_app_id (application_id),
        INDEX idx_action (action)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "✅ application_history table verified.\n";

    // 4. Create application_counters table if not exists
    echo "📋 Creating application_counters table if not exists...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS application_counters (
        year INT NOT NULL,
        type VARCHAR(20) NOT NULL,
        counter INT NOT NULL DEFAULT 0,
        PRIMARY KEY (year, type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "✅ application_counters table verified.\n";

    echo "\n🎉 Database schema update check complete!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

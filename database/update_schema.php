<?php
/**
 * Safe Database Schema Update Script
 * Run: php database/update_schema.php
 */

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

    // List of columns to add with their types
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

    // Get existing columns
    $stmt = $pdo->query("DESCRIBE applications");
    $existingColumns = array_column($stmt->fetchAll(), 'Field');

    foreach ($columnsToAdd as $columnName => $columnDefinition) {
        if (!in_array($columnName, $existingColumns, true)) {
            echo "➕ Adding column '{$columnName}'...\n";
            $pdo->exec("ALTER TABLE applications ADD COLUMN `{$columnName}` {$columnDefinition}");
            echo "✅ Column '{$columnName}' added successfully.\n";
        } else {
            echo "ℹ️ Column '{$columnName}' already exists. Skipping.\n";
        }
    }

    echo "\n🎉 Database schema update check complete!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

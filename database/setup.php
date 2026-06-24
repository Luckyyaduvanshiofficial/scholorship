#!/usr/bin/php
<?php
/**
 * Database Setup Script
 * Run: php database/setup.php
 * 
 * This script:
 * 1. Creates the database (if not exists)
 * 2. Creates all 11 tables
 * 3. Inserts default data
 * 4. Inserts test admin user
 * 5. Inserts academic sessions
 */

// Load environment
require_once __DIR__ . '/../vendor/autoload.php';
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    die("❌ .env file not found. Create it first.\n");
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

echo "🔧 Database Setup\n";
echo "─────────────────────\n";
echo "Host: $dbHost\n";
echo "Port: $dbPort\n";
echo "Database: $dbName\n";
echo "User: $dbUser\n\n";

try {
    // Connect to MySQL server (without database first)
    $pdo = new PDO(
        "mysql:host=$dbHost;port=$dbPort;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Connected to MySQL server\n\n";
    
    // Step 1: Create database (fresh start)
    echo "📚 Creating database...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");
    $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");
    echo "✅ Database created\n\n";
    
    // Step 2: Read and execute schema
    echo "📋 Creating tables...\n";
    $schemaFile = __DIR__ . '/schema/001_create_tables.sql';
    
    if (!file_exists($schemaFile)) {
        die("❌ Schema file not found: $schemaFile\n");
    }
    
    $schema = file_get_contents($schemaFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(
        array_map('trim', explode(';', $schema)),
        fn($stmt) => !empty($stmt)
    );
    
    foreach ($statements as $stmt) {
        if (trim($stmt)) {
            $pdo->exec($stmt);
        }
    }
    
    echo "✅ All tables created\n\n";
    
    // Step 3: Insert test admin user
    echo "👤 Inserting test admin user...\n";
    
    // Use Delight Auth compliant password hash
    $passwordHash = \Delight\Auth\PasswordHash::from('password123');
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password, username, status, verified, roles_mask, registered) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'admin@tamoli.org',
        $passwordHash,
        'Admin User',
        0, // Status::NORMAL
        1, // verified
        262144, // Role::SUPER_ADMIN
        time()
    ]);
    
    echo "✅ Test admin created (email: admin@tamoli.org, password: password123)\n\n";
    
    // Step 4: Insert academic sessions
    echo "📅 Inserting academic sessions...\n";
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO academic_sessions (session_name, is_active) VALUES (?, ?)");
    $stmt->execute(['2025-26', 0]);
    $stmt->execute(['2026-27', 1]);
    
    echo "✅ Academic sessions created\n\n";
    
    // Step 5: Verify
    echo "🔍 Verifying setup...\n";
    $result = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()")->fetch();
    echo "✅ Tables in database: " . $result['count'] . "\n";
    
    $result = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "✅ Admin users: " . $result['count'] . "\n";
    
    $result = $pdo->query("SELECT COUNT(*) as count FROM academic_sessions")->fetch();
    echo "✅ Academic sessions: " . $result['count'] . "\n\n";
    
    echo "🎉 Setup Complete!\n";
    echo "─────────────────────\n";
    echo "Database ready for development\n";
    echo "\nTest connection: http://localhost:8000/test-db.php\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

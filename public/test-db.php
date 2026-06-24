<?php
// File: public/test-db.php

require __DIR__ . '/../vendor/autoload.php';

// Load configuration
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && !str_starts_with($line, '#') && str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim(trim($value), "'\"");
        }
    }
}

try {
    // Test connection
    $db = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_NAME']
        ),
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'] ?? ''
    );
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test query - simple version
    $result = $db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()")->fetch(PDO::FETCH_ASSOC);
    
    echo "✅ Database Connected!<br>";
    echo "✅ Database: " . $_ENV['DB_NAME'] . "<br>";
    echo "✅ Tables: " . $result['count'] . " tables found<br>";
    echo "✅ Ready for development!<br><br>";
    
    // List all tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables:<br>";
    foreach ($tables as $table) {
        echo "  ✓ $table<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Connection Error: " . $e->getMessage();
}
?>
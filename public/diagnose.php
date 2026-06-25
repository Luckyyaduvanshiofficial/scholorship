<?php
/**
 * Diagnostic script for Tamboli Samaj Portal deployment.
 * Upload this file to your public_html/ folder.
 * Access it at: https://portal.tambolisamaj.online/diagnose.php
 * IMPORTANT: Delete this file after troubleshooting for security.
 */

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "<h1>🔍 Tamboli Samaj Portal - Deployment Diagnostic Tool</h1>";
echo "<hr>";

// 1. PHP Version
$phpVersion = PHP_VERSION;
echo "<h3>1. PHP Environment</h3>";
echo "PHP Version: <strong>{$phpVersion}</strong> ";
if (version_compare($phpVersion, '8.1.0', '>=')) {
    echo "<span style='color:green;'>✔️ (Passed: PHP 8.1+ is active)</span>";
} else {
    echo "<span style='color:red;'>❌ (Failed: Requires PHP 8.1 or higher. Change PHP version in Hostinger panel)</span>";
}
echo "<br>";

// 2. Paths
$currentDir = __DIR__;
$parentDir = dirname($currentDir);
echo "<h3>2. Paths & Directories</h3>";
echo "Current directory (web root): <code>{$currentDir}</code><br>";
echo "Parent directory: <code>{$parentDir}</code><br>";

// Check vendor
$vendorAutoload = $parentDir . '/vendor/autoload.php';
echo "Autoload path: <code>{$vendorAutoload}</code> ";
if (file_exists($vendorAutoload)) {
    echo "<span style='color:green;'>✔️ (Found)</span>";
} else {
    echo "<span style='color:red;'>❌ (Not Found! Did you upload the 'vendor/' directory to the parent folder?)</span>";
    echo "<br><em>Note: If you uploaded everything inside public_html directly, check path configurations.</em>";
}
echo "<br>";

// Check .env
$envFile = $parentDir . '/.env';
echo "Environment file (.env) path: <code>{$envFile}</code> ";
if (file_exists($envFile)) {
    echo "<span style='color:green;'>✔️ (Found)</span>";
    
    // Parse .env (safely display variables without showing sensitive values)
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '' && !str_starts_with($line, '#') && str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim(trim($value), "'\"");
        }
    }
    
    echo "<blockquote>";
    echo "<strong>APP_NAME:</strong> " . ($env['APP_NAME'] ?? 'Not set') . "<br>";
    echo "<strong>APP_URL:</strong> " . ($env['APP_URL'] ?? 'Not set') . "<br>";
    echo "<strong>APP_DEBUG:</strong> " . ($env['APP_DEBUG'] ?? 'Not set') . "<br>";
    echo "<strong>DB_HOST:</strong> " . ($env['DB_HOST'] ?? 'Not set') . "<br>";
    echo "<strong>DB_NAME:</strong> " . ($env['DB_NAME'] ?? 'Not set') . "<br>";
    echo "<strong>DB_USER:</strong> " . ($env['DB_USER'] ?? 'Not set') . "<br>";
    echo "<strong>DB_PASS:</strong> " . (empty($env['DB_PASS']) ? "<span style='color:orange;'>Empty</span>" : "<span style='color:green;'>Filled (Hidden)</span>") . "<br>";
    echo "</blockquote>";
} else {
    echo "<span style='color:red;'>❌ (Not Found! Make sure you created a '.env' file in the parent folder parallel to 'public_html')</span>";
}
echo "<br>";

// 3. Database Connection test
echo "<h3>3. Database Connection</h3>";
if (isset($env['DB_HOST']) && isset($env['DB_NAME']) && isset($env['DB_USER'])) {
    try {
        $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'] ?? '', $options);
        echo "<span style='color:green;'>✔️ Database connection successful!</span><br>";
        
        // Check if tables exist
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables in database: <strong>" . count($tables) . "</strong><br>";
        if (count($tables) > 0) {
            echo "<span style='color:green;'>✔️ Database tables are loaded.</span>";
        } else {
            echo "<span style='color:red;'>❌ Database is empty! Please import 001_create_tables.sql</span>";
        }
    } catch (PDOException $e) {
        echo "<span style='color:red;'>❌ Connection failed: " . $e->getMessage() . "</span><br>";
        echo "<em>Verify DB_HOST, DB_NAME, DB_USER, and DB_PASS in your .env file. Also verify that you created the MySQL database on Hostinger hPanel.</em>";
    }
} else {
    echo "<span style='color:orange;'>⚠️ Cannot test connection (missing .env parameters)</span>";
}
echo "<br>";

// 4. PHP Extensions Checklist
echo "<h3>4. Required Extensions Check</h3>";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    echo "Extension <code>{$ext}</code>: ";
    if (extension_loaded($ext)) {
        echo "<span style='color:green;'>✔️ Loaded</span>";
    } else {
        echo "<span style='color:red;'>❌ Missing! Enable this in your Hostinger PHP Extensions menu.</span>";
    }
    echo "<br>";
}

echo "<hr>";
echo "<p style='color:orange;'><strong>⚠️ SECURITY NOTICE:</strong> Delete this file (<code>public_html/diagnose.php</code>) from your server immediately after you finish debugging.</p>";

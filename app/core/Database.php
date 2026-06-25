<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    /**
     * Return the single PDO connection. Creates it on first call.
     * Throws RuntimeException with a user-friendly message on failure.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        try {
            $config = require CONFIG_PATH . '/database.php';

            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            self::$instance = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );

            return self::$instance;

        } catch (PDOException $e) {
            Logger::error('Database connection failed', [
                'error' => $e->getMessage(),
                'host'  => $config['host'] ?? 'unknown',
                'db'    => $config['database'] ?? 'unknown',
            ]);

            throw new RuntimeException(
                'Unable to connect to the database. Please try again in a moment.',
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Close and reset the connection. Useful for tests.
     */
    public static function close(): void
    {
        self::$instance = null;
    }
}

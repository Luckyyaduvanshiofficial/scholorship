<?php

declare(strict_types=1);

namespace App\Core;

class Flash
{
    private const KEY = '_flash';

    /**
     * Set a flash message. Available until next read of that type.
     * Accepts strings (error/success messages) and arrays (form old-data, etc).
     */
    public static function set(string $type, mixed $message): void
    {
        Session::start();

        if (!isset($_SESSION[self::KEY][$type])) {
            $_SESSION[self::KEY][$type] = [];
        }

        $_SESSION[self::KEY][$type][] = $message;
    }

    /**
     * Get flash messages of a type and clear them.
     */
    public static function get(string $type): array
    {
        Session::start();

        $messages = $_SESSION[self::KEY][$type] ?? [];
        unset($_SESSION[self::KEY][$type]);

        return $messages;
    }

    /**
     * Check if flash messages exist for a type.
     */
    public static function has(string $type): bool
    {
        Session::start();

        return !empty($_SESSION[self::KEY][$type]);
    }

    /**
     * Get all flash messages grouped by type, then clear.
     */
    public static function all(): array
    {
        Session::start();

        $all = $_SESSION[self::KEY] ?? [];
        $_SESSION[self::KEY] = [];

        return $all;
    }
}

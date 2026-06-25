<?php

declare(strict_types=1);

namespace App\Core;

class Input
{
    public static function post(string $key, mixed $default = null): mixed
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    public static function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public static function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = self::post($key);
        }
        return $result;
    }

    public static function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}

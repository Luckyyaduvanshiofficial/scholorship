<?php

declare(strict_types=1);

namespace App\Core;

class Helpers
{
    /**
     * Escape HTML entities for safe output.
     */
    public static function esc(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Generate a random hex string.
     */
    public static function random(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Generate a URL-friendly slug from a string.
     */
    public static function slug(string $text): string
    {
        $text = preg_replace('/[^\pL\pN\s-]/u', '', $text);
        $text = preg_replace('/[\s-]+/', '-', trim((string) $text));
        $text = mb_strtolower($text);

        return $text ?: 'untitled';
    }

    /**
     * Get the current URL including query string.
     */
    public static function currentUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';

        return $scheme . '://' . $host . $uri;
    }

    /**
     * Build a URL relative to the app base.
     */
    public static function url(string $path = ''): string
    {
        $base = rtrim(($_ENV['APP_URL'] ?? 'http://localhost:8000'), '/');

        return $base . '/' . ltrim($path, '/');
    }

    /**
     * Redirect helper — delegates to Response for actual output.
     */
    public static function redirect(string $path): never
    {
        Response::redirect($path);
    }

    /**
     * Convert file size in bytes to a human-readable format.
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= (1024 ** $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get or set a value in a multi-dimensional array using dot notation.
     */
    public static function arrayGet(array $array, string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }
}

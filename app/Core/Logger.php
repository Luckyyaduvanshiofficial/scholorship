<?php

declare(strict_types=1);

namespace App\Core;

class Logger
{
    private static string $logDir;
    private static bool $initialized = false;

    private const LEVELS = [
        'DEBUG'   => 0,
        'INFO'    => 1,
        'WARNING' => 2,
        'ERROR'   => 3,
    ];

    private static int $minLevel = 1; // defaults to INFO and above

    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$logDir = LOG_PATH;
        self::$minLevel = APP_DEBUG ? 0 : 1;

        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0755, true);
        }

        self::$initialized = true;
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::write('DEBUG', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void
    {
        if (self::LEVELS[$level] < self::$minLevel) {
            return;
        }

        self::init();

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = $context ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $line = sprintf("[%s] [%s] %s%s\n", $timestamp, $level, $message, $contextStr);

        $filename = self::$logDir . '/app-' . date('Y-m-d') . '.log';
        @error_log($line, 3, $filename);
    }
}

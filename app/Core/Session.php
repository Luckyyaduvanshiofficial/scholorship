<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    /**
     * Start the session with secure configuration.
     *
     * Uses database session handler for cross-subdomain authentication
     * when APP_URL contains a production domain. Falls back to file-based
     * sessions for local development.
     */
    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $config   = require CONFIG_PATH . '/app.php';
        $session  = $config['session'];
        $lifetime = $session['lifetime'];
        $secure   = $session['secure'];
        $name     = $session['name'];

        // Cross-subdomain cookie domain (shared auth across site/portal/admin)
        $cookieDomain = defined('SESSION_DOMAIN') ? SESSION_DOMAIN : '';

        if ($cookieDomain === '' && str_contains((string) ($_ENV['APP_URL'] ?? ''), 'tambolisamaj.online')) {
            $cookieDomain = '.tambolisamaj.online';
        }

        session_name($name);
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => $cookieDomain,
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        // Use database session handler for production (cross-subdomain)
        if ($cookieDomain !== '') {
            $handler = new SessionHandler();
            session_set_save_handler($handler, true);
        }

        session_start();
        self::$started = true;

        // Initialize CSRF token if not present
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = Helpers::random(64);
        }

        // Initialize flash container
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
    }

    /**
     * Set a session value.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value. Returns $default if key not found.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists.
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Regenerate the session ID. Call after login.
     */
    public static function regenerate(): void
    {
        if (self::$started) {
            session_regenerate_id(true);
        }
    }

    /**
     * Destroy the session completely.
     */
    public static function destroy(): void
    {
        if (!self::$started) {
            self::start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }
}

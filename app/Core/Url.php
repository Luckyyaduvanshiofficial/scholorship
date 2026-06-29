<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Multi-site URL helpers for portal, admin, and main website hosts.
 */
class Url
{
    /**
     * Path prefix for admin routes on the current host.
     * Empty on admin.tambolisamaj.online; "/admin" on portal legacy paths.
     */
    public static function adminPrefix(): string
    {
        return APP_HOST === 'admin' ? '' : '/admin';
    }

    /**
     * Build an admin path relative to the current host.
     */
    public static function admin(string $path = ''): string
    {
        $path = trim($path, '/');
        $prefix = self::adminPrefix();

        if ($path === '') {
            return $prefix === '' ? '/' : $prefix;
        }

        return ($prefix === '' ? '' : $prefix) . '/' . $path;
    }

    /**
     * Absolute URL for the main public website.
     */
    public static function site(string $path = ''): string
    {
        return self::join(SITE_URL, $path);
    }

    /**
     * Absolute URL for the student portal.
     */
    public static function portal(string $path = ''): string
    {
        return self::join(PORTAL_URL, $path);
    }

    /**
     * Absolute URL for the admin panel subdomain.
     */
    public static function adminSite(string $path = ''): string
    {
        return self::join(ADMIN_URL, $path);
    }

    /**
     * Base URL for the current host (site, portal, or admin).
     */
    public static function currentBase(): string
    {
        return match (APP_HOST) {
            'site'   => SITE_URL,
            'admin'  => ADMIN_URL,
            'portal' => PORTAL_URL,
            default  => APP_URL,
        };
    }

    /**
     * Absolute URL on the current host.
     */
    public static function current(string $path = ''): string
    {
        return self::join(self::currentBase(), $path);
    }

    /**
     * Base URL for static assets (/assets/...).
     * Portal serves assets locally; admin/main load from portal host.
     */
    public static function asset(string $path = ''): string
    {
        $path = ltrim($path, '/');

        if (APP_HOST === 'portal') {
            if ($path === '') {
                return '/assets';
            }

            if (str_starts_with($path, 'assets/')) {
                return '/' . $path;
            }

            return '/assets/' . $path;
        }

        $base = rtrim(PORTAL_URL, '/');

        if ($path === '') {
            return $base . '/assets';
        }

        if (str_starts_with($path, 'assets/')) {
            return $base . '/' . $path;
        }

        return $base . '/assets/' . $path;
    }

    /**
     * URL for user-uploaded files (profiles in portal docroot, application docs via route).
     */
    public static function upload(string $path = ''): string
    {
        $path = ltrim($path, '/');

        if (APP_HOST === 'portal' || APP_HOST === 'site') {
            return '/' . ($path !== '' ? $path : 'uploads');
        }

        return self::join(PORTAL_URL, $path === '' ? 'uploads' : $path);
    }

    /**
     * Home URL for the current host (used by error pages).
     */
    public static function home(): string
    {
        return match (APP_HOST) {
            'site'   => SITE_URL,
            'admin'  => ADMIN_URL,
            'portal' => PORTAL_URL,
            default  => APP_URL,
        };
    }

    private static function join(string $base, string $path): string
    {
        $base = rtrim($base, '/');
        $path = trim($path, '/');

        return $path === '' ? $base : $base . '/' . $path;
    }
}
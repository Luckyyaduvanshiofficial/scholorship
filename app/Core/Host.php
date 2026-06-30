<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Detects application host and document-root layout for Hostinger deployments.
 *
 * Mode A (recommended): each subdomain document root → main/, portal/, or admin/
 * Mode B (fallback):    all subdomains → public_html/ with root index.php
 */
class Host
{
    /**
     * Resolve APP_HOST from entry point constant or HTTP_HOST + .env URLs.
     */
    public static function resolve(): string
    {
        if (defined('APP_HOST')) {
            return APP_HOST;
        }

        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
        $host = (string) preg_replace('/:\d+$/', '', $host);

        $adminHost  = self::hostFromEnv('ADMIN_URL');
        $portalHost = self::hostFromEnv('PORTAL_URL');
        $siteHost   = self::hostFromEnv('SITE_URL');

        if ($adminHost !== '' && ($host === $adminHost || str_starts_with($host, 'admin.'))) {
            return 'admin';
        }

        if ($portalHost !== '' && ($host === $portalHost || str_starts_with($host, 'portal.'))) {
            return 'portal';
        }

        if ($siteHost !== '' && $host === $siteHost) {
            return 'site';
        }

        // Laragon / local fallbacks
        if (str_contains($host, 'tamoli-admin')) {
            return 'admin';
        }

        if (str_contains($host, 'tamoli-main')) {
            return 'site';
        }

        if (str_contains($host, 'tamoli-prathibha') || str_contains($host, 'portal.')) {
            return 'portal';
        }

        return 'portal';
    }

    /**
     * Which folder is the web server document root: main, portal, admin, or unified (public_html).
     */
    public static function docRootEntry(): string
    {
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

        if ($docRoot === '') {
            return 'unified';
        }

        $real = realpath($docRoot);

        if ($real === false) {
            return 'unified';
        }

        $base = basename(str_replace('\\', '/', $real));

        return match ($base) {
            'main', 'portal', 'admin' => $base,
            default                     => 'unified',
        };
    }

    /**
     * Whether all hosts share one document root (public_html).
     */
    public static function isUnifiedDocRoot(): bool
    {
        return self::docRootEntry() === 'unified';
    }

    /**
     * Web path prefix for portal static files (assets, favicon) on the current host.
     */
    public static function portalWebPrefix(): string
    {
        return self::isUnifiedDocRoot() ? '/portal' : '';
    }

    private static function hostFromEnv(string $key): string
    {
        $url = $_ENV[$key] ?? '';

        if ($url === '') {
            return '';
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) ? strtolower($host) : '';
    }
}
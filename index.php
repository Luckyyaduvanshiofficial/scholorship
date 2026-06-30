<?php

declare(strict_types=1);

/**
 * Unified entry point — use when ALL domains point to public_html/ (Mode B).
 *
 * Detects site / portal / admin from HTTP_HOST + .env URLs.
 * Mode A (recommended): point each subdomain to main/, portal/, admin/ instead.
 */

require __DIR__ . '/bootstrap.php';
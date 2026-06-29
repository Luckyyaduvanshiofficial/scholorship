<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * Bootstrap the application and handle the HTTP request.
     */
    public function run(): void
    {
        try {
            $this->redirectLegacyPortalAdmin();

            // Load routes — pass router as $router variable
            $router = $this->router;
            require APP_ROOT . '/app/Routes/web.php';

            // Resolve the current request
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $uri    = $_SERVER['REQUEST_URI'] ?? '/';

            $this->router->resolve($method, $uri);

        } catch (\PDOException $e) {
            Logger::error('Database error: ' . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            Response::abort(500, 'A database error occurred.');

        } catch (\Throwable $e) {
            Logger::error('Application error: ' . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            Response::abort(500, 'An unexpected error occurred.');
        }
    }

    /**
     * Portal no longer serves /admin/* — redirect to admin subdomain.
     */
    private function redirectLegacyPortalAdmin(): void
    {
        if (!defined('APP_HOST') || APP_HOST !== 'portal') {
            return;
        }

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        if (!str_starts_with($path, '/admin')) {
            return;
        }

        $adminPath = substr($path, strlen('/admin')) ?: '';
        $query     = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
        $target    = rtrim(ADMIN_URL, '/') . $adminPath;

        if ($query) {
            $target .= '?' . $query;
        }

        Response::redirect($target);
    }
}

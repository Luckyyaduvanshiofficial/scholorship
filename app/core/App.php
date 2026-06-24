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
            // Load routes — pass router as $router variable
            $router = $this->router;
            require APP_ROOT . '/app/routes/web.php';

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
}

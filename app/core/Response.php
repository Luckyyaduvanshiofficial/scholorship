<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    /**
     * Redirect to another URL and terminate.
     */
    public static function redirect(string $url, int $statusCode = 302): never
    {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }

    /**
     * Redirect back to the previous page. Falls back to '/' if no referrer.
     */
    public static function back(): never
    {
        $referrer = $_SERVER['HTTP_REFERER'] ?? Helpers::url('/');

        self::redirect($referrer);
    }

    /**
     * Send a JSON response.
     */
    public static function json(array $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Render a view file.
     */
    public static function view(string $template, array $data = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);

        // Extract variables for the view
        extract($data, EXTR_SKIP);

        $file = VIEW_PATH . '/' . $template . '.php';

        if (!file_exists($file)) {
            Logger::error("View not found: {$template}");

            self::abort(500, 'View not found');
        }

        require $file;
    }

    /**
     * Abort with an HTTP error page.
     */
    public static function abort(int $code, string $message = ''): never
    {
        http_response_code($code);

        $template = match ($code) {
            401 => 'errors/401',
            403 => 'errors/403',
            404 => 'errors/404',
            default => 'errors/500',
        };

        $file = VIEW_PATH . '/' . $template . '.php';

        // Extract variables for the error view
        $errorCode    = $code;
        $errorMessage = $message;

        require VIEW_PATH . '/layouts/header.php';
        require $file;
        require VIEW_PATH . '/layouts/footer.php';
        exit;
    }
}

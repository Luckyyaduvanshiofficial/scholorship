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
     * Redirect to an admin path (host-aware: / on admin host, /admin/ on portal).
     */
    public static function redirectAdmin(string $path = '', int $statusCode = 302): never
    {
        self::redirect(Url::admin($path), $statusCode);
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
     *
     * @param string      $template  View template path (relative to VIEW_PATH)
     * @param array       $data      Variables to extract for the view
     * @param string|null $layout    Optional layout to wrap the view in
     * @param int         $statusCode HTTP status code
     */
    public static function view(string $template, array $data = [], ?string $layout = null, int $statusCode = 200): void
    {
        http_response_code($statusCode);

        // Extract variables for the view
        extract($data, EXTR_SKIP);

        $file = VIEW_PATH . '/' . $template . '.php';

        if (!file_exists($file)) {
            Logger::error("View not found: {$template}");

            self::abort(500, 'View not found');
        }

        // If a layout is specified, render view content into layout
        if ($layout !== null) {
            $layoutFile = VIEW_PATH . '/' . $layout . '.php';

            if (!file_exists($layoutFile)) {
                Logger::error("Layout not found: {$layout}");

                self::abort(500, 'Layout not found');
            }

            // Capture view content into $content variable
            ob_start();
            require $file;
            $content = ob_get_clean();

            // Render layout with $content available
            require $layoutFile;
        } else {
            // No layout — render view directly (existing behavior)
            require $file;
        }
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

        // Force generic error message in production for server errors
        $errorMessage = $message;
        if ($code >= 500 && (!defined('APP_DEBUG') || !APP_DEBUG)) {
            $errorMessage = 'An internal server error occurred. Please try again later.';
        }

        // Extract variables for the error view
        $errorCode    = $code;

        require $file;
        exit;
    }
}

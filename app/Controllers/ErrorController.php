<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

class ErrorController
{
    /**
     * Render 401 Unauthorized page.
     */
    public function unauthorized(): void
    {
        Response::abort(401);
    }

    /**
     * Render 403 Forbidden page.
     */
    public function forbidden(): void
    {
        Response::abort(403);
    }

    /**
     * Render 404 Not Found page.
     */
    public function notFound(): void
    {
        Response::abort(404);
    }

    /**
     * Render 500 Internal Server Error page.
     */
    public function serverError(): void
    {
        Response::abort(500);
    }
}

<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(): void
    {
        if (Auth::guest()) {
            Flash::set('error', 'Please sign in to continue.');
            Response::redirect('/login');
        }
    }
}

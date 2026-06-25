<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Response;

class AdminMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Flash::set('error', 'Please sign in to continue.');
            Response::redirect('/login');
        }

        if (!Auth::isAdmin()) {
            Flash::set('error', 'You do not have permission to access this area.');
            Response::redirect('/dashboard');
        }
    }
}

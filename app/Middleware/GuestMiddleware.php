<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Response;

class GuestMiddleware
{
    public function handle(): void
    {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                Response::redirect('/admin');
            } elseif (Auth::isRepresentative()) {
                Response::redirect('/representative');
            } else {
                Response::redirect('/dashboard');
            }
        }
    }
}

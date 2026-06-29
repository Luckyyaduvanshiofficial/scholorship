<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Response;
use App\Core\Url;

class GuestMiddleware
{
    public function handle(): void
    {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                Response::redirect(APP_HOST === 'admin' ? '/' : Url::adminSite());
            } elseif (Auth::isRepresentative()) {
                Response::redirect('/representative');
            } else {
                Response::redirect('/dashboard');
            }
        }
    }
}

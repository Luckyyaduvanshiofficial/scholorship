<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;

class SuperAdminMiddleware
{
    public function handle(): void
    {
        Auth::guardSuperAdmin();
    }
}
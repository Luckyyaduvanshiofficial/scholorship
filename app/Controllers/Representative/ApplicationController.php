<?php

declare(strict_types=1);

namespace App\Controllers\Representative;

use App\Core\Auth;
use App\Core\Response;
use App\Models\Application;

class ApplicationController
{
    /**
     * Read-only application list for representatives.
     */
    public function index(): void
    {
        if (!Auth::isRepresentative()) {
            Response::redirect('/login');
        }

        $appModel = new Application();
        $applications = $appModel->all();

        Response::view('dashboard/rep-applications', [
            'title'        => 'Applications — Representative Dashboard',
            'applications' => $applications,
        ]);
    }
}
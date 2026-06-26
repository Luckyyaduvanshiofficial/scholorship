<?php

declare(strict_types=1);

namespace App\Controllers\Representative;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Response;

class DashboardController
{
    /**
     * Representative dashboard.
     */
    public function index(): void
    {
        if (!Auth::isRepresentative()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        Response::view('dashboard/representative', [
            'title'     => 'Representative Dashboard — Tamboli Samaj Portal',
            'repName'   => Auth::userName(),
        ]);
    }
}

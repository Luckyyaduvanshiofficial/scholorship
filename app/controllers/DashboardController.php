<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Response;

class DashboardController
{
    /**
     * Student dashboard — lists their applications and status.
     */
    public function student(): void
    {
        if (!Auth::check()) {
            Response::redirect('/login');
        }

        Response::view('dashboard/student', [
            'title'      => 'Student Dashboard — Tamboli Samaj Portal',
            'studentName'=> Auth::userName(),
            'studentCode'=> Auth::studentCode(),
        ]);
    }

    /**
     * Admin dashboard — all applications, approve/dispute actions.
     */
    public function admin(): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        Response::view('dashboard/admin', [
            'title'     => 'Admin Dashboard — Tamboli Samaj Portal',
            'adminName' => Auth::userName(),
        ]);
    }

    /**
     * Representative dashboard.
     */
    public function representative(): void
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

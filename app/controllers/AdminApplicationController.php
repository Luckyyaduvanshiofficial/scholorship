<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Input;
use App\Core\Response;
use App\Models\Application;

class AdminApplicationController
{
    /**
     * List all applications.
     */
    public function index(): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $appModel = new Application();
        $applications = $appModel->all();

        Response::view('admin/applications/index', [
            'title'        => 'Applications — Admin Dashboard',
            'applications' => $applications,
        ]);
    }

    /**
     * View a single application detail.
     */
    public function show(int $id): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $appModel = new Application();
        $app = $appModel->find($id);

        if (!$app) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/admin/applications');
        }

        Response::view('admin/applications/show', [
            'title'       => 'Review Application — Admin Dashboard',
            'application' => $app,
        ]);
    }

    /**
     * Approve an application.
     */
    public function approve(int $id): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/admin/applications/' . $id);
        }

        $appModel = new Application();
        $appModel->updateStatus($id, 2, (int) Auth::id());

        Flash::set('success', 'Application approved.');
        Response::redirect('/admin/applications');
    }

    /**
     * Reject an application.
     */
    public function reject(int $id): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/admin/applications/' . $id);
        }

        $appModel = new Application();
        $appModel->updateStatus($id, 3, (int) Auth::id());

        Flash::set('success', 'Application rejected.');
        Response::redirect('/admin/applications');
    }

    /**
     * Mark an application as disputed with a message.
     */
    public function dispute(int $id): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/admin/applications/' . $id);
        }

        $message = Input::post('dispute_message', '');

        if (trim($message) === '') {
            Flash::set('error', 'Please provide a dispute message.');
            Response::redirect('/admin/applications/' . $id);
        }

        $appModel = new Application();
        $appModel->update($id, [
            'status_id'       => 4,
            'reviewed_by'     => (int) Auth::id(),
            'dispute_message' => $message,
        ]);

        Flash::set('success', 'Application marked as disputed.');
        Response::redirect('/admin/applications');
    }
}

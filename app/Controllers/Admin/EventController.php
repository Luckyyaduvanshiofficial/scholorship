<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Input;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Site\Event;

class EventController
{
    /**
     * List all events (admin).
     */
    public function index(): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/login');
        }

        $model = new Event();
        $page  = max(1, (int) Input::get('page', '1'));
        $events = $model->getAllAdmin(20, $page);

        Response::view('admin/events/index', [
            'title'      => 'Manage Events — Admin',
            'events'     => $events['data'],
            'pagination' => $events,
        ], 'layouts/admin');
    }

    /**
     * Show create event form.
     */
    public function create(): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/login');
        }

        Response::view('admin/events/form', [
            'title' => 'Create Event — Admin',
            'event' => null,
            'errors' => [],
        ], 'layouts/admin');
    }

    /**
     * Store new event (POST).
     */
    public function store(): void
    {
        Csrf::validate();

        if (!Auth::isAdmin()) {
            Response::redirect('/login');
        }

        $title       = trim(Input::post('title', ''));
        $description = trim(Input::post('description', ''));
        $excerpt     = trim(Input::post('excerpt', ''));
        $eventDate   = trim(Input::post('event_date', ''));
        $location    = trim(Input::post('location', ''));
        $isActive    = Input::post('is_active') ? 1 : 0;
        $regRequired = Input::post('registration_required') ? 1 : 0;
        $maxParticipants = Input::post('max_participants') ? (int) Input::post('max_participants') : null;

        $validator = Validator::make([
            'title'      => $title,
            'event_date' => $eventDate,
        ], [
            'title'      => 'required|max:200',
            'event_date' => 'required',
        ]);

        if (!$validator->passes()) {
            Response::view('admin/events/form', [
                'title'  => 'Create Event — Admin',
                'event'  => null,
                'errors' => $validator->errors(),
                'old'    => $_POST,
            ], 'layouts/admin');
            return;
        }

        $model = new Event();
        $slug  = $model->generateSlug($title);

        $model->create([
            'title'                => $title,
            'slug'                 => $slug,
            'excerpt'              => $excerpt ?: null,
            'description'          => $description ?: null,
            'event_date'           => $eventDate,
            'location'             => $location ?: null,
            'image'                => null,
            'is_active'            => $isActive,
            'registration_required' => $regRequired,
            'max_participants'     => $maxParticipants,
            'created_by'           => Auth::id(),
        ]);

        Flash::set('success', 'Event created successfully.');
        Response::redirectAdmin('events');
    }

    /**
     * Show edit event form.
     */
    public function edit(int $id): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/login');
        }

        $model = new Event();
        $event = $model->getById($id);

        if (!$event) {
            Response::abort(404);
        }

        Response::view('admin/events/form', [
            'title'  => 'Edit Event — Admin',
            'event'  => $event,
            'errors' => [],
        ], 'layouts/admin');
    }

    /**
     * Update event (POST).
     */
    public function update(int $id): void
    {
        Csrf::validate();

        if (!Auth::isAdmin()) {
            Response::redirect('/login');
        }

        $model = new Event();
        $event = $model->getById($id);

        if (!$event) {
            Response::abort(404);
        }

        $title       = trim(Input::post('title', ''));
        $description = trim(Input::post('description', ''));
        $excerpt     = trim(Input::post('excerpt', ''));
        $eventDate   = trim(Input::post('event_date', ''));
        $location    = trim(Input::post('location', ''));
        $isActive    = Input::post('is_active') ? 1 : 0;
        $regRequired = Input::post('registration_required') ? 1 : 0;
        $maxParticipants = Input::post('max_participants') ? (int) Input::post('max_participants') : null;

        $validator = Validator::make([
            'title'      => $title,
            'event_date' => $eventDate,
        ], [
            'title'      => 'required|max:200',
            'event_date' => 'required',
        ]);

        if (!$validator->passes()) {
            Response::view('admin/events/form', [
                'title'  => 'Edit Event — Admin',
                'event'  => $event,
                'errors' => $validator->errors(),
                'old'    => $_POST,
            ], 'layouts/admin');
            return;
        }

        $slug = ($title !== $event['title']) ? $model->generateSlug($title) : $event['slug'];

        $model->update($id, [
            'title'                => $title,
            'slug'                 => $slug,
            'excerpt'              => $excerpt ?: null,
            'description'          => $description ?: null,
            'event_date'           => $eventDate,
            'location'             => $location ?: null,
            'is_active'            => $isActive,
            'registration_required' => $regRequired,
            'max_participants'     => $maxParticipants,
        ]);

        Flash::set('success', 'Event updated successfully.');
        Response::redirectAdmin('events');
    }

    /**
     * Delete event (POST).
     */
    public function delete(int $id): void
    {
        Csrf::validate();

        if (!Auth::isAdmin()) {
            Response::redirect('/login');
        }

        $model = new Event();
        $model->delete($id);

        Flash::set('success', 'Event deleted successfully.');
        Response::redirectAdmin('events');
    }
}

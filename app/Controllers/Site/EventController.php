<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Input;
use App\Core\Response;
use App\Models\Site\Event;
use App\Models\Site\EventRegistration;

class EventController
{
    /**
     * List all active events.
     */
    public function index(): void
    {
        $model = new Event();
        $page  = max(1, (int) Input::get('page', '1'));
        $events = $model->getAll(12, $page);

        Response::view('site/events/index', [
            'title'      => 'Events — Tamboli Samaj',
            'events'     => $events['data'],
            'pagination' => $events,
            'isLoggedIn' => Auth::check(),
            'userName'   => Auth::check() ? Auth::userName() : '',
        ], 'layouts/site');
    }

    /**
     * Show a single event.
     */
    public function show(string $slug): void
    {
        $model = new Event();
        $event = $model->getBySlug($slug);

        if (!$event) {
            Response::abort(404);
        }

        $isRegistered = false;
        if (Auth::check()) {
            $regModel = new EventRegistration();
            $isRegistered = $regModel->isRegistered((int) $event['id'], Auth::id());
        }

        Response::view('site/events/show', [
            'title'         => $event['title'] . ' — Tamboli Samaj',
            'event'         => $event,
            'isRegistered'  => $isRegistered,
            'isLoggedIn'    => Auth::check(),
            'userName'      => Auth::check() ? Auth::userName() : '',
        ], 'layouts/site');
    }

    /**
     * Register for an event (POST).
     */
    public function register(string $slug): void
    {
        Csrf::validate();

        if (!Auth::check()) {
            Flash::set('error', 'Please login to register for events.');
            Response::redirect('/login');
        }

        $eventModel = new Event();
        $event = $eventModel->getBySlug($slug);

        if (!$event) {
            Response::abort(404);
        }

        if (!$event['registration_required']) {
            Flash::set('info', 'This event does not require registration.');
            Response::redirect('/events/' . $slug);
        }

        // Check max participants
        if ($event['max_participants'] !== null) {
            $regModel = new EventRegistration();
            $currentCount = $regModel->countForEvent((int) $event['id']);
            if ($currentCount >= $event['max_participants']) {
                Flash::set('error', 'Sorry, this event is full.');
                Response::redirect('/events/' . $slug);
            }
        }

        $name   = trim(Input::post('name', ''));
        $mobile = trim(Input::post('mobile', ''));

        if ($name === '') {
            $name = Auth::userName();
        }

        $regModel = new EventRegistration();
        $regModel->register((int) $event['id'], Auth::id(), $name, $mobile);

        Flash::set('success', 'You have been registered for this event!');
        Response::redirect('/events/' . $slug);
    }
}

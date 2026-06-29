<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Auth;
use App\Core\Response;
use App\Models\Site\BlogPost;
use App\Models\Site\Event;

class HomeController
{
    /**
     * Main website homepage.
     */
    public function index(): void
    {
        $eventModel = new Event();
        $blogModel  = new BlogPost();

        $upcomingEvents = $eventModel->getUpcoming(6);
        $latestPosts    = $blogModel->getLatest(3);

        Response::view('site/home', [
            'title'           => 'तम्बोली समाज — Tamboli Samaj',
            'upcomingEvents'  => $upcomingEvents,
            'latestPosts'     => $latestPosts,
            'isLoggedIn'      => Auth::check(),
            'userName'        => Auth::check() ? Auth::userName() : '',
        ], 'layouts/site');
    }

    /**
     * About the Samaj page.
     */
    public function about(): void
    {
        Response::view('site/about', [
            'title'      => 'About — Tamboli Samaj',
            'isLoggedIn' => Auth::check(),
            'userName'   => Auth::check() ? Auth::userName() : '',
        ], 'layouts/site');
    }
}

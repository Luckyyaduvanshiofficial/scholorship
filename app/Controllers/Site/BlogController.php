<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Auth;
use App\Core\Input;
use App\Core\Response;
use App\Models\Site\BlogPost;

class BlogController
{
    /**
     * List all published blog posts.
     */
    public function index(): void
    {
        $model = new BlogPost();
        $page  = max(1, (int) Input::get('page', '1'));
        $posts = $model->getAll(12, $page);

        Response::view('site/blog/index', [
            'title'      => 'Blog — Tamboli Samaj',
            'posts'      => $posts['data'],
            'pagination' => $posts,
            'isLoggedIn' => Auth::check(),
            'userName'   => Auth::check() ? Auth::userName() : '',
        ], 'layouts/site');
    }

    /**
     * Show a single blog post.
     */
    public function show(string $slug): void
    {
        $model = new BlogPost();
        $post  = $model->getBySlug($slug);

        if (!$post) {
            Response::abort(404);
        }

        Response::view('site/blog/show', [
            'title'      => $post['title'] . ' — Tamboli Samaj',
            'post'       => $post,
            'isLoggedIn' => Auth::check(),
            'userName'   => Auth::check() ? Auth::userName() : '',
        ], 'layouts/site');
    }
}

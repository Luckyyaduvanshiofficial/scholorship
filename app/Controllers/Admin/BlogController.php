<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Input;
use App\Core\Response;
use App\Core\Url;
use App\Core\Validator;
use App\Models\Site\BlogPost;

class BlogController
{
    /**
     * List all blog posts (admin).
     */
    public function index(): void
    {
        Auth::guardAdmin();

        $model = new BlogPost();
        $page  = max(1, (int) Input::get('page', '1'));
        $posts = $model->getAllAdmin(20, $page);

        Response::view('admin/blog/index', [
            'title'      => 'Manage Blog — Admin',
            'posts'      => $posts['data'],
            'pagination' => $posts,
        ], 'layouts/admin');
    }

    /**
     * Show create blog post form.
     */
    public function create(): void
    {
        Auth::guardAdmin();

        Response::view('admin/blog/form', [
            'title' => 'Create Blog Post — Admin',
            'post'  => null,
            'errors' => [],
        ], 'layouts/admin');
    }

    /**
     * Store new blog post (POST).
     */
    public function store(): void
    {
        Csrf::validateOrAbort(Url::admin('blog/create'));

        Auth::guardAdmin();

        $title      = trim(Input::post('title', ''));
        $content    = trim(Input::post('content', ''));
        $excerpt    = trim(Input::post('excerpt', ''));
        $status     = Input::post('status', 'draft');

        $validator = Validator::make([
            'title'   => $title,
            'content' => $content,
        ], [
            'title'   => 'required|max:200',
            'content' => 'required',
        ]);

        if (!$validator->passes()) {
            Response::view('admin/blog/form', [
                'title'  => 'Create Blog Post — Admin',
                'post'   => null,
                'errors' => $validator->errors(),
                'old'    => $_POST,
            ], 'layouts/admin');
            return;
        }

        $model = new BlogPost();
        $slug  = $model->generateSlug($title);

        $publishedAt = null;
        if ($status === 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $model->create([
            'title'           => $title,
            'slug'            => $slug,
            'content'         => $content,
            'excerpt'         => $excerpt ?: null,
            'featured_image'  => null,
            'author_id'       => Auth::id(),
            'status'          => $status,
            'published_at'    => $publishedAt,
        ]);

        Flash::set('success', 'Blog post created successfully.');
        Response::redirectAdmin('blog');
    }

    /**
     * Show edit blog post form.
     */
    public function edit(int $id): void
    {
        Auth::guardAdmin();

        $model = new BlogPost();
        $post  = $model->getById($id);

        if (!$post) {
            Response::abort(404);
        }

        Response::view('admin/blog/form', [
            'title'  => 'Edit Blog Post — Admin',
            'post'   => $post,
            'errors' => [],
        ], 'layouts/admin');
    }

    /**
     * Update blog post (POST).
     */
    public function update(int $id): void
    {
        Csrf::validateOrAbort(Url::admin("blog/{$id}/edit"));

        Auth::guardAdmin();

        $model = new BlogPost();
        $post  = $model->getById($id);

        if (!$post) {
            Response::abort(404);
        }

        $title   = trim(Input::post('title', ''));
        $content = trim(Input::post('content', ''));
        $excerpt = trim(Input::post('excerpt', ''));
        $status  = Input::post('status', $post['status']);

        $validator = Validator::make([
            'title'   => $title,
            'content' => $content,
        ], [
            'title'   => 'required|max:200',
            'content' => 'required',
        ]);

        if (!$validator->passes()) {
            Response::view('admin/blog/form', [
                'title'  => 'Edit Blog Post — Admin',
                'post'   => $post,
                'errors' => $validator->errors(),
                'old'    => $_POST,
            ], 'layouts/admin');
            return;
        }

        $slug = ($title !== $post['title']) ? $model->generateSlug($title) : $post['slug'];

        $publishedAt = $post['published_at'];
        if ($status === 'published' && $post['status'] !== 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $model->update($id, [
            'title'          => $title,
            'slug'           => $slug,
            'content'        => $content,
            'excerpt'        => $excerpt ?: null,
            'status'         => $status,
            'published_at'   => $publishedAt,
        ]);

        Flash::set('success', 'Blog post updated successfully.');
        Response::redirectAdmin('blog');
    }

    /**
     * Delete blog post (POST).
     */
    public function delete(int $id): void
    {
        Csrf::validateOrAbort(Url::admin('blog'));

        Auth::guardAdmin();

        $model = new BlogPost();
        $model->delete($id);

        Flash::set('success', 'Blog post deleted successfully.');
        Response::redirectAdmin('blog');
    }
}

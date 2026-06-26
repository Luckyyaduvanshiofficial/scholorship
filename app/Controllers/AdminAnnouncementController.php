<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Input;
use App\Core\Logger;
use App\Core\Response;
use App\Core\Validator;
use PDO;

class AdminAnnouncementController
{
    /**
     * List all announcements.
     */
    public function index(): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT a.*, u.username as creator_name FROM announcements a LEFT JOIN users u ON a.created_by = u.id ORDER BY a.created_at DESC");
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::view('admin/announcements/index', [
            'title'         => 'Announcements Management — Admin Dashboard',
            'announcements' => $announcements,
        ]);
    }

    /**
     * Show create announcement form.
     */
    public function create(): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        Response::view('admin/announcements/create', [
            'title' => 'Create Announcement — Admin Dashboard',
        ]);
    }

    /**
     * Store new announcement.
     */
    public function store(): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $title = trim(Input::post('title', ''));
        $content = trim(Input::post('content', ''));
        $isActive = Input::post('is_active') !== null ? 1 : 0;

        $v = Validator::make([
            'title'   => $title,
            'content' => $content,
        ]);

        $v->required('title', 'Title')
          ->required('content', 'Content');

        if ($v->fails()) {
            Flash::set('error', $v->first('title') ?? $v->first('content'));
            Response::redirect('/admin/announcements/create');
        }

        $db = Database::getInstance();

        // Generate clean URL slug (Unicode supportive)
        $slug = preg_replace('/\s+/u', '-', mb_strtolower($title));
        $slug = preg_replace('/[^\p{L}\p{N}-]+/u', '', $slug);
        $slug = trim($slug, '-');

        // Empty fallback
        if ($slug === '') {
            $slug = 'announcement-' . time();
        }

        // Ensure uniqueness
        $stmt = $db->prepare("SELECT COUNT(*) FROM announcements WHERE slug = ?");
        $stmt->execute([$slug]);
        if ((int) $stmt->fetchColumn() > 0) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare(
            "INSERT INTO announcements (title, slug, content, is_active, created_by, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$title, $slug, $content, $isActive, (int) Auth::id()]);

        Flash::set('success', 'सूचना सफलतापूर्वक जारी की गई।');
        Response::redirect('/admin/announcements');
    }

    /**
     * Show edit announcement form.
     */
    public function edit(int $id): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM announcements WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$announcement) {
            Flash::set('error', 'Announcement not found.');
            Response::redirect('/admin/announcements');
        }

        Response::view('admin/announcements/edit', [
            'title'        => 'Edit Announcement — Admin Dashboard',
            'announcement' => $announcement,
        ]);
    }

    /**
     * Update existing announcement.
     */
    public function update(int $id): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $title = trim(Input::post('title', ''));
        $content = trim(Input::post('content', ''));
        $isActive = Input::post('is_active') !== null ? 1 : 0;

        $v = Validator::make([
            'title'   => $title,
            'content' => $content,
        ]);

        $v->required('title', 'Title')
          ->required('content', 'Content');

        if ($v->fails()) {
            Flash::set('error', $v->first('title') ?? $v->first('content'));
            Response::redirect("/admin/announcements/{$id}/edit");
        }

        $db = Database::getInstance();

        // Check if exists
        try {
            $stmt = $db->prepare("SELECT id FROM announcements WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                Flash::set('error', 'Announcement not found.');
                Response::redirect('/admin/announcements');
            }

            $stmt = $db->prepare(
                "UPDATE announcements SET title = ?, content = ?, is_active = ? WHERE id = ?"
            );
            $stmt->execute([$title, $content, $isActive, $id]);

            Flash::set('success', 'सूचना सफलतापूर्वक अपडेट की गई।');
            Response::redirect('/admin/announcements');
        } catch (\Throwable $e) {
            Logger::error('Failed to update announcement', [
                'announcement_id' => $id,
                'user_id'         => Auth::id(),
                'error'           => $e->getMessage(),
            ]);
            Flash::set('error', 'एक त्रुटि हुई। कृपया पुनः प्रयास करें।');
            Response::redirect('/admin/announcements');
        }
    }

    /**
     * Delete announcement.
     */
    public function delete(int $id): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);

        Flash::set('success', 'सूचना सफलतापूर्वक हटा दी गई।');
        Response::redirect('/admin/announcements');
    }
}

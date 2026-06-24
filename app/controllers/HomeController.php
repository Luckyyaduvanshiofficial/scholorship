<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Response;
use PDO;
use Throwable;

class HomeController
{
    /**
     * Landing page — public, no auth required.
     */
    public function index(): void
    {
        $announcements = [];

        try {
            $db = Database::getInstance();
            $stmt = $db->query(
                "SELECT title, content, created_at
                 FROM announcements
                 WHERE is_active = 1
                 ORDER BY created_at DESC
                 LIMIT 5"
            );
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable) {
            $announcements = [];
        }

        Response::view('home/index', [
            'title'         => 'Tamboli Samaj Portal',
            'announcements' => $announcements,
        ]);
    }
}

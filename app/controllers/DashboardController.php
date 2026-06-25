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
        if (!Auth::isStudent()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
        $stmt->execute([Auth::id()]);
        $student = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];

        Response::view('dashboard/student', [
            'title'      => 'Student Dashboard — Tamboli Samaj Portal',
            'studentName'=> Auth::userName(),
            'studentCode'=> Auth::studentCode(),
            'student'    => $student,
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

        $db = \App\Core\Database::getInstance();

        // 1. Fetch counts in a single query
        $countsQuery = $db->query("
            SELECT 
                COUNT(*) as total_apps,
                SUM(CASE WHEN type = 'scholarship' THEN 1 ELSE 0 END) as scholarship_apps,
                SUM(CASE WHEN type = 'pratibha' THEN 1 ELSE 0 END) as pratibha_apps,
                SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as pending_apps,
                SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as approved_apps,
                SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as rejected_apps,
                SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) as disputed_apps
            FROM applications
        ");
        $counts = $countsQuery->fetch(\PDO::FETCH_ASSOC);

        $totalApps = (int) ($counts['total_apps'] ?? 0);
        $scholarshipApps = (int) ($counts['scholarship_apps'] ?? 0);
        $pratibhaApps = (int) ($counts['pratibha_apps'] ?? 0);

        $statusCounts = [
            'pending'  => (int) ($counts['pending_apps'] ?? 0),
            'approved' => (int) ($counts['approved_apps'] ?? 0),
            'rejected' => (int) ($counts['rejected_apps'] ?? 0),
            'disputed' => (int) ($counts['disputed_apps'] ?? 0),
        ];

        // 2. Registered students count
        $totalStudents = (int) $db->query("SELECT COUNT(*) FROM students")->fetchColumn();

        // 3. Total announcements count
        $totalAnnouncements = (int) $db->query("SELECT COUNT(*) FROM announcements")->fetchColumn();

        // 7. Recent applications (limit 5)
        $stmt = $db->query(
            "SELECT a.id, a.type, a.submitted_at, s.first_name, s.last_name, s.student_code, atp.name AS app_type_name, ast.name AS status_name 
             FROM applications a
             LEFT JOIN students s ON a.student_id = s.id
             LEFT JOIN application_types atp ON a.application_type_id = atp.id
             LEFT JOIN application_status ast ON a.status_id = ast.id
             ORDER BY a.submitted_at DESC, a.created_at DESC
             LIMIT 5"
        );
        $recentApps = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 8. Recent activities list compiled from db
        $activities = [];
        
        // Fetch 3 most recent applications
        $stmt = $db->query("SELECT a.id, s.first_name, s.last_name, a.created_at, a.type FROM applications a JOIN students s ON a.student_id = s.id ORDER BY a.created_at DESC LIMIT 3");
        $appsAct = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($appsAct as $act) {
            $typeName = $act['type'] === 'scholarship' ? 'छात्रवृत्ति' : 'प्रतिभा सम्मान';
            $activities[] = [
                'type' => 'application',
                'title' => "नया आवेदन TSVS-" . date('Y') . "-" . str_pad((string) $act['id'], 6, '0', STR_PAD_LEFT) . " सबमिट किया गया (" . $act['first_name'] . " " . $act['last_name'] . ")",
                'time' => date('h:i A', strtotime($act['created_at'])),
                'timestamp' => strtotime($act['created_at'])
            ];
        }

        // Fetch 3 most recent student registrations
        $stmt = $db->query("SELECT first_name, last_name, created_at FROM students ORDER BY created_at DESC LIMIT 3");
        $studAct = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($studAct as $act) {
            $activities[] = [
                'type' => 'student',
                'title' => "उपयोगकर्ता " . $act['first_name'] . " " . $act['last_name'] . " पंजीकृत हुआ",
                'time' => date('h:i A', strtotime($act['created_at'])),
                'timestamp' => strtotime($act['created_at'])
            ];
        }

        // Sort activities by timestamp descending
        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        $activities = array_slice($activities, 0, 5);

        $adminEmail = '';
        if (Auth::check()) {
            $stmt = $db->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([Auth::id()]);
            $adminEmail = $stmt->fetchColumn() ?: 'admin@tsvs.org';
        }

        Response::view('dashboard/admin', [
            'title'              => 'Admin Dashboard — Tamboli Samaj Portal',
            'adminName'          => Auth::userName(),
            'adminEmail'         => $adminEmail,
            'totalApps'          => $totalApps,
            'totalStudents'      => $totalStudents,
            'scholarshipApps'    => $scholarshipApps,
            'pratibhaApps'        => $pratibhaApps,
            'totalAnnouncements' => $totalAnnouncements,
            'statusCounts'       => $statusCounts,
            'recentApps'         => $recentApps,
            'activities'         => $activities,
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

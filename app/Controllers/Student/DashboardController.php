<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Response;

class DashboardController
{
    /**
     * Student dashboard — lists their applications and status.
     */
    public function index(): void
    {
        if (!Auth::isStudent()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $db = \App\Core\Database::getInstance();

        // Student profile
        $stmt = $db->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
        $stmt->execute([Auth::id()]);
        $student = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];

        // Student's applications with status
        $appModel = new \App\Models\Application();
        $applications = $appModel->allByStudent((int) Auth::id());

        // Count stats
        $totalApps = count($applications);
        $draftApps = 0;
        $pendingApps = 0;
        $approvedApps = 0;
        $rejectedApps = 0;
        $correctionApps = 0;
        foreach ($applications as $app) {
            $status = (int) ($app['status_id'] ?? 0);
            if ($app['submitted_at'] === null || $status === 1) {
                $draftApps++;
            } elseif (in_array($status, [2, 3, 7], true)) {
                $pendingApps++;
            } elseif ($status === 4) {
                $approvedApps++;
            } elseif ($status === 5) {
                $rejectedApps++;
            } elseif ($status === 6) {
                $correctionApps++;
            }
        }

        // Active session
        $sessionModel = new \App\Models\AcademicSession();
        $activeSession = $sessionModel->active();

        // Profile completion %
        $requiredFields = ['first_name', 'last_name', 'father_name', 'mother_name', 'dob', 'gender', 'mobile', 'email', 'address', 'city', 'state', 'pincode'];
        $filled = 0;
        foreach ($requiredFields as $field) {
            if (!empty($student[$field] ?? null)) {
                $filled++;
            }
        }
        $profileCompletion = (int) round(($filled / count($requiredFields)) * 100);

        // Upcoming deadlines — from settings if available, else defaults
        $appDeadline = '30 जून 2026';
        $ceremonyDate = '09 अगस्त 2026';
        $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'application_end_date' LIMIT 1");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row && !empty($row['value'])) {
            $appDeadline = $row['value'];
        }
        $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'ceremony_date' LIMIT 1");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row && !empty($row['value'])) {
            $ceremonyDate = $row['value'];
        }

        Response::view('dashboard/student', [
            'title'            => 'Student Dashboard — Tamboli Samaj Portal',
            'studentName'      => Auth::userName(),
            'studentCode'      => Auth::studentCode(),
            'student'          => $student,
            'applications'     => $applications,
            'totalApps'        => $totalApps,
            'draftApps'        => $draftApps,
            'pendingApps'      => $pendingApps,
            'approvedApps'     => $approvedApps,
            'correctionApps'   => $correctionApps,
            'rejectedApps'     => $rejectedApps,
            'activeSession'    => $activeSession ?: [],
            'profileCompletion'=> $profileCompletion,
            'appDeadline'      => $appDeadline,
            'ceremonyDate'     => $ceremonyDate,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Response;
use App\Core\Input;
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
        $trackResult = null;
        $trackError = null;
        $trackRef = trim((string) (Input::get('track_ref', '')));

        try {
            $db = Database::getInstance();
            
            // Fetch latest announcements
            $stmt = $db->query(
                "SELECT title, content, created_at
                 FROM announcements
                 WHERE is_active = 1
                 ORDER BY created_at DESC
                 LIMIT 5"
            );
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Handle Application Status tracking search
            if ($trackRef !== '') {
                $appId = null;

                // Parse TSVS-YYYY-ID format
                if (preg_match('/TSVS-\d+-(\d+)/i', $trackRef, $matches)) {
                    $appId = (int) $matches[1];
                } elseif (preg_match('/TSP-\d+-(\d+)/i', $trackRef, $matches)) {
                    $appId = (int) $matches[1];
                } else {
                    $appId = is_numeric($trackRef) ? (int) $trackRef : null;
                }

                if ($appId !== null) {
                    $stmt = $db->prepare(
                        "SELECT a.*, s.first_name, s.last_name, s.student_code, 
                                st.name as status_name, t.name as app_type_name,
                                sess.session_name
                         FROM applications a
                         JOIN students s ON a.student_id = s.id
                         JOIN application_status st ON a.status_id = st.id
                         JOIN application_types t ON a.application_type_id = t.id
                         JOIN academic_sessions sess ON a.session_id = sess.id
                         WHERE a.id = ?"
                    );
                    $stmt->execute([$appId]);
                    $trackResult = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$trackResult) {
                        $trackError = "कोई आवेदन नहीं मिला। कृपया संदर्भ संख्या की जांच करें। / No application found. Please check the reference number.";
                    }
                } else {
                    $trackError = "अमान्य संदर्भ प्रारूप। प्रारूप होना चाहिए: TSVS-YYYY-XXXXXX / Invalid reference format. Expected: TSVS-YYYY-XXXXXX";
                }
            }
        } catch (Throwable $e) {
            \App\Core\Logger::error('Home loading error: ' . $e->getMessage());
            if ($trackRef !== '') {
                $trackError = "स्थिति प्राप्त करने में तकनीकी त्रुटि। / A technical error occurred while retrieving the status.";
            }
        }

        Response::view('home/index', [
            'title'         => 'Tamboli Samaj Portal',
            'bodyClass'     => 'tsp-premium',
            'announcements' => $announcements,
            'trackResult'   => $trackResult,
            'trackError'    => $trackError,
            'trackRef'      => $trackRef,
        ]);
    }
}

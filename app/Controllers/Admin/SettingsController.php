<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Input;
use App\Core\Logger;
use App\Core\Response;
use App\Core\Validator;
use PDO;

class SettingsController
{
    /**
     * Show settings panel dashboard.
     */
    public function index(): void
    {
        if (!Auth::isSuperAdmin()) {
            Flash::set('error', 'Access denied. Super Admin role required.');
            Response::redirect('/');
        }

        $db = Database::getInstance();

        // Fetch all academic sessions
        $stmt = $db->query("SELECT * FROM academic_sessions ORDER BY session_name DESC");
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all settings as key-value pairs
        $stmt = $db->query("SELECT * FROM settings");
        $settingsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['key']] = $s['value'];
        }

        Response::view('admin/settings/index', [
            'title'    => 'System Settings — Super Admin Dashboard',
            'sessions' => $sessions,
            'settings' => $settings,
        ]);
    }

    /**
     * Save global portal config keys.
     */
    public function update(): void
    {
        if (!Auth::isSuperAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/admin/settings');
        }

        $db = Database::getInstance();

        $keys = [
            'site_name',
            'contact_email',
            'contact_phone',
            'scholarship_open',
            'pratibha_open',
            'current_session_id',
        ];

        foreach ($keys as $key) {
            $value = Input::post($key, '');
            
            // For checkbox values (open/closed settings)
            if ($key === 'scholarship_open' || $key === 'pratibha_open') {
                $value = Input::post($key) !== null ? '1' : '0';
            }

            // Check if setting exists first
            $stmt = $db->prepare("SELECT COUNT(*) FROM settings WHERE `key` = ?");
            $stmt->execute([$key]);
            if ((int) $stmt->fetchColumn() > 0) {
                $stmt = $db->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
                $stmt->execute([$value, $key]);
            } else {
                $stmt = $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?)");
                $stmt->execute([$key, $value]);
            }
        }

        Flash::set('success', 'सिस्टम सेटिंग्स सफलतापूर्वक अपडेट की गईं।');
        Response::redirect('/admin/settings');
    }

    /**
     * Create a new academic session.
     */
    public function createSession(): void
    {
        if (!Auth::isSuperAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/admin/settings');
        }

        $sessionName = trim(Input::post('session_name', ''));

        $v = Validator::make([
            'session_name' => $sessionName,
        ]);

        $v->required('session_name', 'Session Name');

        if ($v->fails()) {
            Flash::set('error', $v->first('session_name'));
            Response::redirect('/admin/settings');
        }

        $db = Database::getInstance();

        // Check if session name already exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM academic_sessions WHERE session_name = ?");
        $stmt->execute([$sessionName]);
        if ((int) $stmt->fetchColumn() > 0) {
            Flash::set('error', 'यह शैक्षणिक सत्र पहले से मौजूद है।');
            Response::redirect('/admin/settings');
        }

        $stmt = $db->prepare("INSERT INTO academic_sessions (session_name, is_active) VALUES (?, 0)");
        $stmt->execute([$sessionName]);

        Flash::set('success', "शैक्षणिक सत्र {$sessionName} सफलतापूर्वक जोड़ा गया।");
        Response::redirect('/admin/settings');
    }

    /**
     * Mark a session active (deactivates other sessions).
     */
    public function activateSession(int $id): void
    {
        if (!Auth::isSuperAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/admin/settings');
        }

        try {
            $db = Database::getInstance();

            // Check if session exists
            $stmt = $db->prepare("SELECT session_name FROM academic_sessions WHERE id = ?");
            $stmt->execute([$id]);
            $sessionName = $stmt->fetchColumn();

            if (!$sessionName) {
                Flash::set('error', 'Session not found.');
                Response::redirect('/admin/settings');
            }

            // Set all sessions to inactive
            $db->query("UPDATE academic_sessions SET is_active = 0");

            // Set this session to active
            $stmt = $db->prepare("UPDATE academic_sessions SET is_active = 1 WHERE id = ?");
            $stmt->execute([$id]);

            // Also update current_session_id in settings table
            $stmt = $db->prepare("UPDATE settings SET value = ? WHERE `key` = 'current_session_id'");
            $stmt->execute([(string) $id]);

            Flash::set('success', "शैक्षणिक सत्र {$sessionName} को सक्रिय सत्र के रूप में सेट किया गया है।");
            Response::redirect('/admin/settings');
        } catch (\Throwable $e) {
            Logger::error('Failed to activate academic session', [
                'session_id' => $id,
                'user_id'    => Auth::id(),
                'error'      => $e->getMessage(),
            ]);
            Flash::set('error', 'सत्र सक्रिय करने में त्रुटि। कृपया पुनः प्रयास करें।');
            Response::redirect('/admin/settings');
        }
    }
}

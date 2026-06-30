<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Input;
use App\Core\Response;
use App\Core\Url;
use App\Core\Validator;
use Delight\Auth\Role;
use PDO;

class UserController
{
    /**
     * List all students.
     */
    public function students(): void
    {
        Auth::guardAdmin();

        $db = Database::getInstance();
        $search = trim(Input::get('search', ''));
        $status = Input::get('status', 'all');

        $query = "SELECT s.*, u.status as user_status FROM students s JOIN users u ON s.id = u.id";
        $params = [];
        $where = [];

        if ($search !== '') {
            $where[] = "(s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ? OR s.mobile LIKE ? OR s.student_code LIKE ?)";
            $wildcard = "%{$search}%";
            array_push($params, $wildcard, $wildcard, $wildcard, $wildcard, $wildcard);
        }

        if ($status !== 'all') {
            $where[] = "u.status = ?";
            $params[] = (int) $status;
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $query .= " ORDER BY s.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::view('admin/students/index', [
            'title'    => 'Student Management — Admin Dashboard',
            'students' => $students,
            'search'   => $search,
            'status'   => $status,
        ]);
    }

    /**
     * Toggle student active/suspended status.
     */
    public function toggleStudentStatus(int $id): void
    {
        Csrf::validateOrAbort(Url::admin('students'));
        Auth::guardAdmin();

        $db = Database::getInstance();

        // Fetch current status
        $stmt = $db->prepare("SELECT status FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Flash::set('error', 'User not found.');
            Response::redirectAdmin('students');
        }

        // Toggles status: 0 (Normal/Active) <-> 2 (Suspended)
        $newStatus = ((int) $user['status'] === 0) ? 2 : 0;

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $id]);

            // Also update students status column to sync
            $stmt = $db->prepare("UPDATE students SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus === 0 ? 1 : 0, $id]);

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            Flash::set('error', 'स्थिति अपडेट करने में त्रुटि: ' . $e->getMessage());
            Response::redirectAdmin('students');
        }

        $statusText = ($newStatus === 0) ? 'सक्रिय' : 'निलंबित';
        Flash::set('success', "छात्र का खाता अब {$statusText} है।");
        Response::redirectAdmin('students');
    }

    /**
     * Delete student.
     */
    public function deleteStudent(int $id): void
    {
        Csrf::validateOrAbort(Url::admin('students'));
        Auth::guardAdmin();

        $db = Database::getInstance();

        // Verify user exists
        $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            Flash::set('error', 'Student not found.');
            Response::redirectAdmin('students');
        }

        // Delete from users table (cascades to students and applications)
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        Flash::set('success', 'छात्र का रिकॉर्ड सफलतापूर्वक हटा दिया गया है।');
        Response::redirectAdmin('students');
    }

    /**
     * List all representatives (Super Admin only).
     */
    public function reps(): void
    {
        Auth::guardSuperAdmin();

        $db = Database::getInstance();
        $search = trim(Input::get('search', ''));

        // MODERATOR role is 4096
        $query = "SELECT * FROM users WHERE (roles_mask & ?) > 0";
        $params = [Role::MODERATOR];

        if ($search !== '') {
            $query .= " AND (username LIKE ? OR email LIKE ?)";
            $wildcard = "%{$search}%";
            array_push($params, $wildcard, $wildcard);
        }

        $query .= " ORDER BY registered DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $reps = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::view('admin/reps/index', [
            'title'  => 'Representative Management — Super Admin Dashboard',
            'reps'   => $reps,
            'search' => $search,
        ]);
    }

    /**
     * Create a representative.
     */
    public function createRep(): void
    {
        Csrf::validateOrAbort(Url::admin('reps'));
        Auth::guardSuperAdmin();

        $username = trim(Input::post('username', ''));
        $email = trim(Input::post('email', ''));
        $password = Input::post('password', '');

        $v = Validator::make([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
        ]);

        $v->required('username', 'Name')
          ->required('email', 'Email')
          ->email('email', 'Email')
          ->required('password', 'Password')
          ->min('password', 6, 'Password');

        if ($v->fails()) {
            Flash::set('error', $v->first('username') ?? $v->first('email') ?? $v->first('password'));
            Response::redirectAdmin('reps');
        }

        try {
            $auth = Auth::getAuth();
            $userId = $auth->register($email, $password, $username);

            // Add moderator role
            $auth->admin()->addRoleForUserById($userId, Role::MODERATOR);

            // Force verify and activate
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE users SET verified = 1, status = 0 WHERE id = ?");
            $stmt->execute([$userId]);

            Flash::set('success', "प्रतिनिधि {$username} सफलतापूर्वक पंजीकृत किया गया।");
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            Flash::set('error', 'यह ईमेल पहले से पंजीकृत है।');
        } catch (\Throwable $e) {
            Flash::set('error', 'पंजीकरण में त्रुटि: ' . $e->getMessage());
        }

        Response::redirectAdmin('reps');
    }

    /**
     * Toggle representative status.
     */
    public function toggleRepStatus(int $id): void
    {
        Csrf::validateOrAbort(Url::admin('reps'));
        Auth::guardSuperAdmin();

        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT status FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Flash::set('error', 'Representative not found.');
            Response::redirectAdmin('reps');
        }

        $newStatus = ((int) $user['status'] === 0) ? 2 : 0;

        $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);

        $statusText = ($newStatus === 0) ? 'सक्रिय' : 'निलंबित';
        Flash::set('success', "प्रतिनिधि का खाता अब {$statusText} है।");
        Response::redirectAdmin('reps');
    }

    /**
     * Delete representative.
     */
    public function deleteRep(int $id): void
    {
        Csrf::validateOrAbort(Url::admin('reps'));
        Auth::guardSuperAdmin();

        $db = Database::getInstance();

        // Check if representative exists and has moderator role
        $stmt = $db->prepare("SELECT roles_mask FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $rolesMask = (int) ($stmt->fetchColumn() ?: 0);

        if (($rolesMask & Role::MODERATOR) === 0) {
            Flash::set('error', 'User is not a representative.');
            Response::redirectAdmin('reps');
        }

        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        Flash::set('success', 'प्रतिनिधि सफलतापूर्वक हटा दिया गया।');
        Response::redirectAdmin('reps');
    }
}

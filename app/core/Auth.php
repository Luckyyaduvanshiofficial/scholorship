<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Student;
use Delight\Auth\Auth as DelightAuth;
use Delight\Auth\Role;

class Auth
{
    private static ?DelightAuth $authInstance = null;

    /**
     * Get the single instance of Delight\Auth\Auth.
     */
    public static function getAuth(): DelightAuth
    {
        if (self::$authInstance === null) {
            self::$authInstance = new DelightAuth(Database::getInstance());
        }
        return self::$authInstance;
    }

    /**
     * Attempt login for an admin or representative.
     * Returns true on success, false on failure.
     */
    public static function login(string $email, string $password): bool
    {
        try {
            self::getAuth()->login($email, $password);

            // Access control check: must not be a student role (SUBSCRIBER)
            if (self::isStudent()) {
                self::logout();
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Attempt login for a student.
     * Returns true on success, false on failure.
     */
    public static function studentLogin(string $email, string $password): bool
    {
        try {
            self::getAuth()->login($email, $password);

            // Access control check: must be a student role (SUBSCRIBER)
            if (!self::isStudent()) {
                self::logout();
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Register a new student and log them in.
     */
    public static function registerStudent(array $data, string $rawPassword): int|false
    {
        try {
            $auth = self::getAuth();

            // 1. Register user with Delight Auth
            $userId = $auth->register(
                $data['email'],
                $rawPassword,
                $data['first_name'] . ' ' . $data['last_name']
            );

            // 2. Set role as SUBSCRIBER (Student)
            $auth->admin()->addRoleForUserById($userId, Role::SUBSCRIBER);

            // 3. Mark user verified and normal status
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE users SET verified = 1, status = 0 WHERE id = ?");
            $stmt->execute([$userId]);

            // 4. Create profile in students table under same ID
            $studentModel = new Student();
            $profileData = $data;
            $profileData['id'] = $userId;

            $studentId = $studentModel->create($profileData);

            if ($studentId) {
                // 5. Automatically log the student in
                $auth->login($data['email'], $rawPassword);
                return $studentId;
            }

            return false;
        } catch (\Throwable $e) {
            Logger::error('Student registration error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log out current user.
     */
    public static function logout(): void
    {
        try {
            self::getAuth()->logOut();
            // Also clear cached student code
            Session::remove('student_code');
        } catch (\Throwable $e) {
            // Safe fallback
        }
    }

    /**
     * Check if user is logged in.
     */
    public static function check(): bool
    {
        return self::getAuth()->isLoggedIn();
    }

    /**
     * Check if no user is logged in.
     */
    public static function guest(): bool
    {
        return !self::check();
    }

    /**
     * Get the authenticated user's ID.
     */
    public static function id(): ?int
    {
        return self::getAuth()->getUserId();
    }

    /**
     * Get the authenticated user's type.
     */
    public static function userType(): ?string
    {
        if (!self::check()) {
            return null;
        }

        $auth = self::getAuth();

        if ($auth->hasRole(Role::SUPER_ADMIN)) {
            return 'super_admin';
        }
        if ($auth->hasRole(Role::ADMIN)) {
            return 'admin';
        }
        if ($auth->hasRole(Role::MODERATOR)) {
            return 'representative';
        }
        if ($auth->hasRole(Role::SUBSCRIBER)) {
            return 'student';
        }

        return null;
    }

    /**
     * Get the authenticated user's display name.
     */
    public static function userName(): string
    {
        return self::getAuth()->getUsername() ?? '';
    }

    /**
     * Check if current user is a student.
     */
    public static function isStudent(): bool
    {
        return self::userType() === 'student';
    }

    /**
     * Check if current user is an admin or super_admin.
     */
    public static function isAdmin(): bool
    {
        return in_array(self::userType(), ['admin', 'super_admin'], true);
    }

    /**
     * Check if current user is a super admin.
     */
    public static function isSuperAdmin(): bool
    {
        return self::userType() === 'super_admin';
    }

    /**
     * Check if current user is a representative.
     */
    public static function isRepresentative(): bool
    {
        return self::userType() === 'representative';
    }

    /**
     * Get student code if logged in as student.
     */
    public static function studentCode(): ?string
    {
        if (!self::isStudent()) {
            return null;
        }

        if (!Session::has('student_code')) {
            $studentModel = new Student();
            $student = $studentModel->find((int) self::id());
            if ($student) {
                Session::set('student_code', $student['student_code']);
            }
        }

        return Session::get('student_code');
    }

    /**
     * Update the session display name (after profile edits).
     */
    public static function updateSessionName(string $name): void
    {
        try {
            self::getAuth()->changeUsername($name);
        } catch (\Throwable $e) {
            Logger::error('Error updating username: ' . $e->getMessage());
        }
    }

    /**
     * Get profile photo path if logged in as student.
     */
    public static function profilePhoto(): ?string
    {
        if (!self::isStudent()) {
            return null;
        }

        if (!Session::has('profile_photo')) {
            $studentModel = new Student();
            $student = $studentModel->find((int) self::id());
            if ($student && !empty($student['profile_photo'])) {
                Session::set('profile_photo', '/uploads/profiles/' . $student['profile_photo']);
            } else {
                Session::set('profile_photo', '');
            }
        }

        $photo = Session::get('profile_photo');
        return !empty($photo) ? $photo : null;
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Models\Student;

class Auth
{
    /**
     * Attempt login for a user (admin/representative).
     * Returns true on success, false on failure.
     */
    public static function login(string $email, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        if (empty($user['status'])) {
            return false;
        }

        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_type', $user['role']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);

        return true;
    }

    /**
     * Attempt login for a student.
     * Returns true on success, false on failure.
     */
    public static function studentLogin(string $email, string $password): bool
    {
        $studentModel = new Student();
        $student = $studentModel->findByEmail($email);

        if (!$student || !password_verify($password, $student['password_hash'])) {
            return false;
        }

        if (empty($student['status'])) {
            return false;
        }

        Session::regenerate();
        Session::set('user_id', $student['id']);
        Session::set('user_type', 'student');
        Session::set('user_name', $student['first_name'] . ' ' . $student['last_name']);
        Session::set('user_email', $student['email']);
        Session::set('student_code', $student['student_code']);

        return true;
    }

    /**
     * Register a new student and log them in.
     */
    public static function registerStudent(array $data): int|false
    {
        $studentModel = new Student();
        $studentId = $studentModel->create($data);

        if ($studentId) {
            Session::regenerate();
            Session::set('user_id', $studentId);
            Session::set('user_type', 'student');
            Session::set('user_name', ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            Session::set('user_email', $data['email']);
            Session::set('student_code', $data['student_code'] ?? '');
        }

        return $studentId;
    }

    /**
     * Destroy session — log out current user.
     */
    public static function logout(): void
    {
        Session::destroy();
    }

    /**
     * Check if any user (student or admin/rep) is logged in.
     */
    public static function check(): bool
    {
        return Session::has('user_id');
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
        return Session::get('user_id');
    }

    /**
     * Get the authenticated user's type (student, admin, super_admin, representative).
     */
    public static function userType(): ?string
    {
        return Session::get('user_type');
    }

    /**
     * Get the authenticated user's display name.
     */
    public static function userName(): string
    {
        return Session::get('user_name', '');
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
        return Session::get('student_code');
    }

    /**
     * Update the session display name (after profile edits).
     */
    public static function updateSessionName(string $name): void
    {
        Session::set('user_name', $name);
    }
}

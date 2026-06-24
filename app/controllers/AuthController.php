<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helpers;
use App\Core\Input;
use App\Core\Logger;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Student;
use App\Models\User;

class AuthController
{
    /**
     * Show login page.
     */
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirectToDashboard();
        }

        Response::view('auth/login', [
            'title' => 'Login — Tamboli Samaj Portal',
        ]);
    }

    /**
     * Process login form (handles both admin and student login).
     */
    public function login(): void
    {
        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token. Please try again.');
            Response::redirect('/login');
        }

        $email    = Input::post('email', '');
        $password = Input::post('password', '');
        $role     = Input::post('role', 'student');

        $v = Validator::make([
            'email'    => $email,
            'password' => $password,
        ]);

        $v->required('email', 'Email')
          ->email('email', 'Email')
          ->required('password', 'Password')
          ->min('password', 6, 'Password');

        if ($v->fails()) {
            Flash::set('error', $v->first('email') ?? $v->first('password'));
            Flash::set('old_email', $email);
            Response::redirect('/login');
        }

        if ($role === 'admin') {
            if (Auth::login($email, $password)) {
                Logger::info('Admin login successful', ['email' => $email]);
                Flash::set('success', 'Welcome back, ' . Auth::userName());
                $this->redirectToDashboard();
            }
        } else {
            if (Auth::studentLogin($email, $password)) {
                Logger::info('Student login successful', ['email' => $email]);
                Flash::set('success', 'Welcome, ' . Auth::userName());
                $this->redirectToDashboard();
            }
        }

        Flash::set('error', 'Invalid email or password.');
        Flash::set('old_email', $email);
        Response::redirect('/login');
    }

    /**
     * Show student registration page.
     */
    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirectToDashboard();
        }

        Response::view('auth/register', [
            'title' => 'Student Registration — Tamboli Samaj Portal',
        ]);
    }

    /**
     * Process student registration.
     */
    public function register(): void
    {
        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token. Please try again.');
            Response::redirect('/register');
        }

        $data = [
            'first_name'      => Input::post('first_name', ''),
            'last_name'       => Input::post('last_name', ''),
            'father_name'     => Input::post('father_name', ''),
            'email'           => Input::post('email', ''),
            'mobile'          => Input::post('mobile', ''),
            'gender'          => Input::post('gender', ''),
            'address'         => Input::post('address', ''),
            'city'            => Input::post('city', ''),
            'district'        => Input::post('district', ''),
            'pincode'         => Input::post('pincode', ''),
            'password'        => Input::post('password', ''),
            'password_confirm'=> Input::post('password_confirm', ''),
        ];

        $v = Validator::make($data);
        $v->required('first_name', 'First name')
          ->required('last_name', 'Last name')
          ->required('father_name', 'Father/Guardian name')
          ->required('email', 'Email')
          ->email('email', 'Email')
          ->required('mobile', 'Mobile')
          ->mobile('mobile', 'Mobile')
          ->required('address', 'Address')
          ->required('password', 'Password')
          ->min('password', 6, 'Password')
          ->matches('password_confirm', 'password', 'Password confirmation');

        if ($v->fails()) {
            foreach (['first_name', 'last_name', 'father_name', 'email', 'mobile', 'address', 'gender', 'password', 'password_confirm'] as $field) {
                $err = $v->first($field);
                if ($err) {
                    Flash::set('error', $err);
                    break;
                }
            }
            Flash::set('old', $data);
            Response::redirect('/register');
        }

        // Check for existing email or mobile
        $studentModel = new Student();

        if ($studentModel->findByEmail($data['email'])) {
            Flash::set('error', 'An account with this email already exists.');
            Flash::set('old', $data);
            Response::redirect('/register');
        }

        if ($studentModel->findByMobile($data['mobile'])) {
            Flash::set('error', 'An account with this mobile number already exists.');
            Flash::set('old', $data);
            Response::redirect('/register');
        }

        // Generate student code: TSVS-{year}-{id format}
        // We'll do a quick one: TSVS + year + random 4 digits (will be replaced by proper id later)
        $studentCode = 'TSVS-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));

        $studentData = [
            'student_code'  => $studentCode,
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'mobile'        => $data['mobile'],
            'gender'        => $data['gender'] ?: null,
            'father_name'   => $data['father_name'],
            'address'       => $data['address'],
            'city'          => $data['city'] ?: null,
            'district'      => $data['district'] ?: null,
            'state'         => 'Rajasthan',
            'pincode'       => $data['pincode'] ?: null,
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'status'        => 1,
        ];

        $studentId = Auth::registerStudent($studentData);

        if ($studentId) {
            Logger::info('Student registered', ['id' => $studentId, 'email' => $data['email']]);
            Flash::set('success', 'Registration successful! Welcome, ' . $data['first_name']);
            Response::redirect('/dashboard');
        }

        Logger::error('Student registration failed', ['email' => $data['email']]);
        Flash::set('error', 'Registration failed. Please try again.');
        Flash::set('old', $data);
        Response::redirect('/register');
    }

    /**
     * Log out the current user.
     */
    public function logout(): void
    {
        Auth::logout();
        Flash::set('success', 'You have been logged out.');
        Response::redirect('/');
    }

    /**
     * Redirect to the appropriate dashboard based on user type.
     */
    private function redirectToDashboard(): void
    {
        if (Auth::isAdmin()) {
            Response::redirect('/admin');
        } elseif (Auth::isRepresentative()) {
            Response::redirect('/representative');
        } else {
            Response::redirect('/dashboard');
        }
    }
}

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

        // Generate a unique student code: TSVS-{year}-{4 hex chars}
        $db = \App\Core\Database::getInstance();
        $retryCount = 0;
        do {
            $studentCode = 'TSVS-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
            $stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE student_code = ?");
            $stmt->execute([$studentCode]);
            $exists = ((int) $stmt->fetchColumn() > 0);
            $retryCount++;
        } while ($exists && $retryCount < 10);

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
            'status'        => 1,
        ];

        $studentId = Auth::registerStudent($studentData, $data['password']);

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
     * Show forgot password request page.
     */
    public function showForgotPassword(): void
    {
        if (Auth::check()) {
            $this->redirectToDashboard();
        }

        Response::view('auth/forgot-password', [
            'title' => 'Forgot Password — Tamboli Samaj Portal',
        ]);
    }

    /**
     * Process forgot password request.
     */
    public function forgotPassword(): void
    {
        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token. Please try again.');
            Response::redirect('/forgot-password');
        }

        $email = Input::post('email', '');

        $v = Validator::make(['email' => $email]);
        $v->required('email', 'Email')->email('email', 'Email');

        if ($v->fails()) {
            Flash::set('error', $v->first('email'));
            Response::redirect('/forgot-password');
        }

        try {
            Auth::getAuth()->forgotPassword($email, function ($selector, $token) use ($email) {
                $resetUrl = \APP_URL . '/reset-password?selector=' . urlencode($selector) . '&token=' . urlencode($token);

                $subject = 'तम्बोली समाज पोर्टल — पासवर्ड रीसेट / Password Reset Request';
                $htmlBody = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                        <h2 style='color: #0f6b3c; text-align: center;'>तम्बोली समाज विकास संस्था, राजस्थान</h2>
                        <hr style='border: none; border-top: 1px solid #e2e8f0;' />
                        <p>नमस्ते / Hello,</p>
                        <p>हमें आपके खाते के लिए पासवर्ड रीसेट का अनुरोध प्राप्त हुआ है। नीचे दिए गए बटन पर क्लिक करके अपना पासवर्ड रीसेट करें:</p>
                        <p>We received a request to reset your password. Click the button below to reset it:</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$resetUrl}' style='background-color: #f57c00; color: white; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 4px; display: inline-block;'>पासवर्ड रीसेट करें / Reset Password</a>
                        </div>
                        <p style='color: #64748b; font-size: 13px;'>यदि बटन काम नहीं करता है, तो निम्नलिखित लिंक को अपने ब्राउज़र में कॉपी और पेस्ट करें:</p>
                        <p style='word-break: break-all; font-size: 13px;'><a href='{$resetUrl}'>{$resetUrl}</a></p>
                        <hr style='border: none; border-top: 1px solid #e2e8f0;' />
                        <p style='font-size: 12px; color: #94a3b8; text-align: center;'>यह एक स्वचालित ईमेल है, कृपया इसका उत्तर न दें।</p>
                    </div>
                ";

                \App\Core\Mailer::send($email, $subject, $htmlBody);
            });

            Flash::set('success', 'यदि यह ईमेल पंजीकृत है, तो पासवर्ड रीसेट लिंक भेज दिया गया है। / If this email is registered, a reset link has been sent.');
            Response::redirect('/login');

        } catch (\Delight\Auth\InvalidEmailException $e) {
            Flash::set('success', 'यदि यह ईमेल पंजीकृत है, तो पासवर्ड रीसेट लिंक भेज दिया गया है। / If this email is registered, a reset link has been sent.');
            Response::redirect('/login');
        } catch (\Delight\Auth\EmailNotVerifiedException $e) {
            Flash::set('error', 'आपका ईमेल सत्यापित नहीं है। / Email is not verified.');
            Response::redirect('/forgot-password');
        } catch (\Delight\Auth\ResetDisabledException $e) {
            Flash::set('error', 'पासवर्ड रीसेट करने की सुविधा अभी अक्षम है। / Password reset is disabled.');
            Response::redirect('/forgot-password');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            Flash::set('error', 'बहुत सारे अनुरोध। कृपया बाद में पुनः प्रयास करें। / Too many requests. Try again later.');
            Response::redirect('/forgot-password');
        } catch (\Throwable $e) {
            Logger::error('Forgot password error: ' . $e->getMessage());
            Flash::set('error', 'अनुरोध संसाधित करने में त्रुटि। / Error processing request.');
            Response::redirect('/forgot-password');
        }
    }

    /**
     * Show reset password page (after clicking link).
     */
    public function showResetPassword(): void
    {
        $selector = Input::get('selector', '');
        $token    = Input::get('token', '');

        if (empty($selector) || empty($token)) {
            Flash::set('error', 'अमान्य पासवर्ड रीसेट टोकन। / Invalid password reset token.');
            Response::redirect('/login');
        }

        try {
            Auth::getAuth()->canResetPasswordOrThrow($selector, $token);

            Response::view('auth/reset-password', [
                'title'    => 'Reset Password — Tamboli Samaj Portal',
                'selector' => $selector,
                'token'    => $token,
            ]);
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            Flash::set('error', 'अमान्य पासवर्ड रीसेट लिंक। / Invalid reset link.');
            Response::redirect('/login');
        } catch (\Delight\Auth\TokenExpiredException $e) {
            Flash::set('error', 'पासवर्ड रीसेट लिंक की अवधि समाप्त हो चुकी है। / Reset link has expired.');
            Response::redirect('/login');
        } catch (\Delight\Auth\ResetDisabledException $e) {
            Flash::set('error', 'पासवर्ड रीसेट अक्षम है। / Resets are disabled.');
            Response::redirect('/login');
        } catch (\Throwable $e) {
            Flash::set('error', 'अमान्य या पुराना रीसेट टोकन। / Invalid or stale token.');
            Response::redirect('/login');
        }
    }

    /**
     * Process password reset update.
     */
    public function resetPassword(): void
    {
        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/login');
        }

        $selector         = Input::post('selector', '');
        $token            = Input::post('token', '');
        $password         = Input::post('password', '');
        $passwordConfirm  = Input::post('password_confirm', '');

        if (empty($selector) || empty($token)) {
            Flash::set('error', 'Missing reset parameters.');
            Response::redirect('/login');
        }

        $v = Validator::make([
            'password' => $password,
            'password_confirm' => $passwordConfirm,
        ]);

        $v->required('password', 'Password')
          ->min('password', 6, 'Password')
          ->matches('password_confirm', 'password', 'Password confirmation');

        if ($v->fails()) {
            Flash::set('error', $v->first('password') ?? $v->first('password_confirm'));
            Response::redirect('/reset-password?selector=' . urlencode($selector) . '&token=' . urlencode($token));
        }

        try {
            Auth::getAuth()->resetPassword($selector, $token, $password);

            Flash::set('success', 'पासवर्ड सफलतापूर्वक बदल दिया गया है! अब लॉगिन करें। / Password reset successful! Please log in.');
            Response::redirect('/login');

        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            Flash::set('error', 'अमान्य या पुराना लिंक। / Invalid or expired reset link.');
            Response::redirect('/login');
        } catch (\Delight\Auth\TokenExpiredException $e) {
            Flash::set('error', 'पासवर्ड रीसेट लिंक की अवधि समाप्त हो चुकी है। / Link expired.');
            Response::redirect('/login');
        } catch (\Delight\Auth\ResetDisabledException $e) {
            Flash::set('error', 'पासवर्ड रीसेट अक्षम है। / Resets are disabled.');
            Response::redirect('/login');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            Flash::set('error', 'बहुत सारे प्रयास। कृपया बाद में प्रयास करें। / Too many requests.');
            Response::redirect('/login');
        } catch (\Throwable $e) {
            Logger::error('Reset password error: ' . $e->getMessage());
            Flash::set('error', 'पासवर्ड रीसेट करने में त्रुटि। / Error resetting password.');
            Response::redirect('/login');
        }
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

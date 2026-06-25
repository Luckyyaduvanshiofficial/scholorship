<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\FileUploader;
use App\Core\Flash;
use App\Core\Helpers;
use App\Core\Input;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Student;

class ProfileController
{
    /**
     * Show the student's profile.
     */
    public function show(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $studentModel = new Student();
        $student = $studentModel->find((int) Auth::id());

        if (!$student) {
            Flash::set('error', 'Profile not found.');
            Response::redirect('/dashboard');
        }

        Response::view('profile/show', [
            'title'   => 'My Profile — Tamboli Samaj Portal',
            'student' => $student,
        ]);
    }

    /**
     * Show the profile edit form.
     */
    public function edit(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $studentModel = new Student();
        $student = $studentModel->find((int) Auth::id());

        if (!$student) {
            Flash::set('error', 'Profile not found.');
            Response::redirect('/dashboard');
        }

        Response::view('profile/edit', [
            'title'   => 'Edit Profile — Tamboli Samaj Portal',
            'student' => $student,
        ]);
    }

    /**
     * Update profile (personal details only — no password change here).
     */
    public function update(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/profile/edit');
        }

        $studentModel = new Student();
        $studentId = (int) Auth::id();

        $data = [
            'first_name'  => Input::post('first_name', ''),
            'last_name'   => Input::post('last_name', ''),
            'gender'      => Input::post('gender', ''),
            'dob'         => Input::post('dob', ''),
            'mobile'      => Input::post('mobile', ''),
            'father_name' => Input::post('father_name', ''),
            'mother_name' => Input::post('mother_name', ''),
            'address'     => Input::post('address', ''),
            'city'        => Input::post('city', ''),
            'district'    => Input::post('district', ''),
            'state'       => Input::post('state', ''),
            'pincode'     => Input::post('pincode', ''),
        ];

        $v = Validator::make($data);
        $v->required('first_name', 'First name')
          ->required('last_name', 'Last name')
          ->required('mobile', 'Mobile')
          ->mobile('mobile', 'Mobile');

        if (!empty($data['dob'])) {
            $v->date('dob', 'Date of birth');
        }

        if (!empty($data['pincode']) && !preg_match('/^\d{6}$/', $data['pincode'])) {
            Flash::set('error', 'Pincode must be a valid 6-digit number.');
            Flash::set('old', $data);
            Response::redirect('/profile/edit');
        }

        if ($v->fails()) {
            Flash::set('error', $v->first('first_name') ?? $v->first('last_name') ?? $v->first('mobile'));
            Flash::set('old', $data);
            Response::redirect('/profile/edit');
        }

        // Check mobile uniqueness (ignore self)
        $existing = $studentModel->findByMobile($data['mobile']);
        if ($existing && (int) $existing['id'] !== $studentId) {
            Flash::set('error', 'This mobile number is already registered.');
            Flash::set('old', $data);
            Response::redirect('/profile/edit');
        }

        $updateData = [
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'gender'      => $data['gender'] ?: null,
            'dob'         => $data['dob'] ?: null,
            'mobile'      => $data['mobile'],
            'father_name' => $data['father_name'] ?: null,
            'mother_name' => $data['mother_name'] ?: null,
            'address'     => $data['address'] ?: null,
            'city'        => $data['city'] ?: null,
            'district'    => $data['district'] ?: null,
            'state'       => $data['state'] ?: null,
            'pincode'     => $data['pincode'] ?: null,
        ];

        $studentModel->update($studentId, $updateData);

        // Update session name
        Auth::updateSessionName($data['first_name'] . ' ' . $data['last_name']);

        Flash::set('success', 'Profile updated successfully.');
        Response::redirect('/profile');
    }

    /**
     * Handle profile photo upload.
     */
    public function uploadPhoto(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/profile/edit');
        }

        try {
            // Check if cropped base64 image is submitted
            $croppedData = Input::post('cropped_image', '');
            if (!empty($croppedData) && str_starts_with($croppedData, 'data:image/')) {
                $parts = explode(',', $croppedData);
                if (count($parts) === 2) {
                    $base64 = $parts[1];
                    $decoded = base64_decode($base64);
                    if ($decoded !== false) {
                        $finfo = new \finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer($decoded);
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!in_array($mimeType, $allowedMimes, true)) {
                            Flash::set('error', 'Invalid image format. Only JPEG and PNG are allowed.');
                            Response::redirect('/profile/edit');
                        }

                        $ext = ($mimeType === 'image/png') ? 'png' : 'jpg';
                        $storedName = 'profile_' . Auth::id() . '_' . time() . '.' . $ext;
                        $uploadDir = PUBLIC_PATH . '/uploads/profiles';
                        if (!is_dir($uploadDir)) {
                            if (!@mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                                Flash::set('error', 'Could not create upload directory. Please contact support.');
                                Response::redirect('/profile/edit');
                            }
                        }
                        $filePath = $uploadDir . '/' . $storedName;
                        if (file_put_contents($filePath, $decoded) === false) {
                            Flash::set('error', 'Failed to save photo. Please try again.');
                            Response::redirect('/profile/edit');
                        }

                        $studentModel = new Student();
                        $studentModel->update((int) Auth::id(), [
                            'profile_photo' => $storedName,
                        ]);
                        \App\Core\Session::set('profile_photo', '/uploads/profiles/' . $storedName);

                        Flash::set('success', 'Profile photo updated successfully.');
                        Response::redirect('/profile');
                        return;
                    }
                }
            }

            $uploader = new FileUploader();
            $file = Input::file('profile_photo');

            if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
                Flash::set('error', 'Please select a photo to upload.');
                Response::redirect('/profile/edit');
            }

            if (!$uploader->validate($file)) {
                Flash::set('error', $uploader->firstError());
                Response::redirect('/profile/edit');
            }

            $uploadDir = PUBLIC_PATH . '/uploads/profiles';
            $storedName = $uploader->upload($file, $uploadDir);

            if ($storedName === false) {
                Flash::set('error', $uploader->firstError());
                Response::redirect('/profile/edit');
            }

            $studentModel = new Student();
            $studentModel->update((int) Auth::id(), [
                'profile_photo' => $storedName,
            ]);
            \App\Core\Session::set('profile_photo', '/uploads/profiles/' . $storedName);

            Flash::set('success', 'Profile photo updated successfully.');
            Response::redirect('/profile');

        } catch (\Throwable $e) {
            Logger::error('Profile photo upload failed', [
                'student_id' => Auth::id(),
                'error'      => $e->getMessage(),
            ]);
            Flash::set('error', 'A temporary error occurred while uploading. Please try again.');
            Response::redirect('/profile/edit');
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\FileUploader;
use App\Core\Helpers;
use App\Core\Input;
use App\Core\Logger;
use App\Core\Response;
use App\Core\Validator;
use App\Models\AcademicSession;
use App\Models\Application;
use App\Models\ApplicationType;

class ApplicationController
{
    /**
     * List all applications for the logged-in student.
     */
    public function index(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $appModel = new Application();
        $applications = $appModel->allByStudent((int) Auth::id());

        Response::view('applications/index', [
            'title'        => 'My Applications — Tamboli Samaj Portal',
            'applications' => $applications,
        ]);
    }

    /**
     * Application type selection page.
     */
    public function create(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $sessionModel = new AcademicSession();
        $activeSession = $sessionModel->active();

        if (!$activeSession) {
            Flash::set('error', 'No active academic session. Applications are closed.');
            Response::redirect('/applications');
        }

        $appModel = new Application();
        $typeModel = new ApplicationType();
        $types = $typeModel->all();

        // Check if student already has applications for each type in this session
        $existing = [];
        foreach ($types as $type) {
            $existing[$type['id']] = $appModel->findByStudent(
                (int) Auth::id(),
                (int) $activeSession['id'],
                (int) $type['id']
            );
        }

        Response::view('applications/create', [
            'title'         => 'New Application — Tamboli Samaj Portal',
            'types'         => $types,
            'activeSession' => $activeSession,
            'existing'      => $existing,
        ]);
    }

    /**
     * Show scholarship application form.
     */
    public function scholarship(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $sessionModel = new AcademicSession();
        $activeSession = $sessionModel->active();

        if (!$activeSession) {
            Flash::set('error', 'Applications are currently closed.');
            Response::redirect('/applications');
        }

        // Check for duplicate or load/create draft
        $appModel = new Application();
        $typeModel = new ApplicationType();
        $scholarshipType = $typeModel->findByName('Scholarship');
        $application = [];

        if ($scholarshipType) {
            $existing = $appModel->findByStudent(
                (int) Auth::id(),
                (int) $activeSession['id'],
                (int) $scholarshipType['id']
            );

            if ($existing) {
                if ($existing['submitted_at'] !== null) {
                    Flash::set('error', 'You have already applied for Scholarship in this session.');
                    Response::redirect('/applications');
                } else {
                    $application = $appModel->find((int) $existing['id']);
                }
            } else {
                // Auto-create a draft application immediately
                $appId = $appModel->create([
                    'student_id'          => (int) Auth::id(),
                    'session_id'          => (int) $activeSession['id'],
                    'application_type_id' => (int) $scholarshipType['id'],
                    'status_id'           => 1, // Pending
                    'type'                => 'scholarship',
                    'submitted_at'        => null,
                ]);
                $application = $appModel->find((int) $appId);
            }
        }

        $studentModel = new \App\Models\Student();
        $student = $studentModel->find((int) Auth::id());

        Response::view('applications/scholarship', [
            'title'         => 'Scholarship Application — Tamboli Samaj Portal',
            'activeSession' => $activeSession,
            'student'       => $student ?: [],
            'application'   => $application,
        ]);
    }

    /**
     * Show Pratibha Samman application form.
     */
    public function pratibha(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $sessionModel = new AcademicSession();
        $activeSession = $sessionModel->active();

        if (!$activeSession) {
            Flash::set('error', 'Applications are currently closed.');
            Response::redirect('/applications');
        }

        $appModel = new Application();
        $typeModel = new ApplicationType();
        $pratibhaType = $typeModel->findByName('Pratibha Samman');
        $application = [];

        if ($pratibhaType) {
            $existing = $appModel->findByStudent(
                (int) Auth::id(),
                (int) $activeSession['id'],
                (int) $pratibhaType['id']
            );

            if ($existing) {
                if ($existing['submitted_at'] !== null) {
                    Flash::set('error', 'You have already registered for Pratibha Samman in this session.');
                    Response::redirect('/applications');
                } else {
                    $application = $appModel->find((int) $existing['id']);
                }
            } else {
                // Auto-create a draft application immediately
                $appId = $appModel->create([
                    'student_id'          => (int) Auth::id(),
                    'session_id'          => (int) $activeSession['id'],
                    'application_type_id' => (int) $pratibhaType['id'],
                    'status_id'           => 1, // Pending
                    'type'                => 'pratibha',
                    'submitted_at'        => null,
                ]);
                $application = $appModel->find((int) $appId);
            }
        }

        $studentModel = new \App\Models\Student();
        $student = $studentModel->find((int) Auth::id());

        Response::view('applications/pratibha', [
            'title'         => 'Pratibha Samman Application — Tamboli Samaj Portal',
            'activeSession' => $activeSession,
            'student'       => $student ?: [],
            'application'   => $application,
        ]);
    }

    /**
     * Store a scholarship application.
     */
    public function storeScholarship(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/applications/create');
        }

        $sessionModel = new AcademicSession();
        $activeSession = $sessionModel->active();

        if (!$activeSession) {
            Flash::set('error', 'Applications are currently closed.');
            Response::redirect('/applications');
        }

        $typeModel = new ApplicationType();
        $scholarshipType = $typeModel->findByName('Scholarship');

        if (!$scholarshipType) {
            Flash::set('error', 'Application type not found.');
            Response::redirect('/applications');
        }

        // Retrieve and update student profile information if changed in form
        $studentModel = new \App\Models\Student();
        $studentModel->update((int) Auth::id(), [
            'first_name'  => Input::post('first_name', ''),
            'last_name'   => Input::post('last_name', ''),
            'father_name' => Input::post('father_name', ''),
            'mother_name' => Input::post('mother_name', ''),
            'dob'         => Input::post('dob', null) ?: null,
            'gender'      => Input::post('gender', ''),
            'address'     => Input::post('address', ''),
            'city'        => Input::post('city', ''),
            'district'    => Input::post('district', ''),
            'state'       => Input::post('state', ''),
            'pincode'     => Input::post('pincode', ''),
        ]);

        // Validate application inputs
        $data = [
            'class_year'                => Input::post('class_year', ''),
            'college_name'              => Input::post('college_name', ''),
            'board_university'          => Input::post('board_university', ''),
            'marks_obtained'            => Input::post('marks_obtained', ''),
            'max_marks'                 => Input::post('max_marks', ''),
            'percentage'                => Input::post('percentage', ''),
            'family_income'             => Input::post('family_income', ''),
            'bank_name'                 => Input::post('bank_name', ''),
            'account_number'            => Input::post('account_number', ''),
            'ifsc_code'                 => Input::post('ifsc_code', ''),
            'family_occupation'         => Input::post('family_occupation', ''),
            'family_members_count'      => Input::post('family_members_count', ''),
            'earning_members_count'     => Input::post('earning_members_count', ''),
            'current_class'             => Input::post('current_class', ''),
            'current_college'           => Input::post('current_college', ''),
            'prev_scholarship_received' => Input::post('prev_scholarship_received', ''),
            'scholarship_amt_2023_24'   => Input::post('scholarship_amt_2023_24', ''),
            'scholarship_amt_2024_25'   => Input::post('scholarship_amt_2024_25', ''),
            'scholarship_amt_2025_26'   => Input::post('scholarship_amt_2025_26', ''),
            'account_holder_name'       => Input::post('account_holder_name', ''),
            'career_goal'               => Input::post('career_goal', ''),
        ];

        $v = Validator::make($data);
        $v->required('class_year', 'Class/Year')
          ->required('percentage', 'Percentage')
          ->numeric('percentage', 'Percentage')
          ->required('bank_name', 'Bank name')
          ->required('account_number', 'Account number')
          ->required('ifsc_code', 'IFSC code')
          ->required('family_occupation', 'Family occupation')
          ->required('family_members_count', 'Family members count')
          ->required('earning_members_count', 'Earning members count')
          ->required('current_class', 'Current class')
          ->required('current_college', 'Current college')
          ->required('account_holder_name', 'Account holder name')
          ->required('career_goal', 'Career goal');

        if ($v->fails()) {
            Flash::set('error', $v->first('class_year') ?? $v->first('percentage') ?? $v->first('bank_name') ?? $v->first('family_occupation') ?? $v->first('current_class'));
            Flash::set('old', $data);
            Response::redirect('/applications/scholarship');
        }

        $ifsc = strtoupper(trim($data['ifsc_code']));
        if (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc)) {
            Flash::set('error', 'IFSC code must be in the format: 4 uppercase letters, a zero, and 6 alphanumeric characters (e.g., SBIN0012345).');
            Flash::set('old', $data);
            Response::redirect('/applications/scholarship');
        }
        $data['ifsc_code'] = $ifsc;

        $pct = (float) $data['percentage'];
        if ($pct < 0 || $pct > 100) {
            Flash::set('error', 'Percentage must be between 0 and 100.');
            Flash::set('old', $data);
            Response::redirect('/applications/scholarship');
        }

        // Get draft application
        $appModel = new Application();
        $existingDraft = $appModel->findByStudent((int)Auth::id(), (int)$activeSession['id'], (int)$scholarshipType['id']);
        if (!$existingDraft) {
            $appId = $appModel->create([
                'student_id'          => (int) Auth::id(),
                'session_id'          => (int) $activeSession['id'],
                'application_type_id' => (int) $scholarshipType['id'],
                'status_id'           => 1,
                'type'                => 'scholarship',
            ]);
        } else {
            $appId = (int)$existingDraft['id'];
        }

        // Check already uploaded documents
        $dbDocs = $appModel->documents($appId);
        $dbDocTypes = array_column($dbDocs, 'document_type');

        // Validate uploads
        $requiredDocs = [
            'marksheet' => 'Marksheet',
            'passbook'  => 'Passbook',
            'photo'     => 'Photo',
            'signature' => 'Signature',
        ];
        $validatedUploads = [];
        $uploader = new FileUploader();

        foreach ($requiredDocs as $field => $documentType) {
            $file = $_FILES[$field] ?? null;
            $hasInDb = in_array($documentType, $dbDocTypes, true);

            if (!$hasInDb && (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
                Flash::set('error', $documentType . ' upload is required.');
                Flash::set('old', $data);
                Response::redirect('/applications/scholarship');
            }

            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                if (!$uploader->validate($file)) {
                    Flash::set('error', $documentType . ': ' . $uploader->firstError());
                    Flash::set('old', $data);
                    Response::redirect('/applications/scholarship');
                }
                $validatedUploads[$field] = [
                    'type' => $documentType,
                    'file' => $file,
                ];
            }
        }

        // Update application
        $appModel->update($appId, [
            'family_income'             => $data['family_income'] !== '' ? $data['family_income'] : null,
            'bank_name'                 => $data['bank_name'],
            'account_number'            => $data['account_number'],
            'ifsc_code'                 => $data['ifsc_code'],
            'family_occupation'         => $data['family_occupation'] ?: null,
            'family_members_count'      => $data['family_members_count'] !== '' ? (int)$data['family_members_count'] : null,
            'earning_members_count'     => $data['earning_members_count'] !== '' ? (int)$data['earning_members_count'] : null,
            'current_class'             => $data['current_class'] ?: null,
            'current_college'           => $data['current_college'] ?: null,
            'prev_scholarship_received' => $data['prev_scholarship_received'] ?: null,
            'scholarship_amt_2023_24'   => $data['scholarship_amt_2023_24'] !== '' ? $data['scholarship_amt_2023_24'] : null,
            'scholarship_amt_2024_25'   => $data['scholarship_amt_2024_25'] !== '' ? $data['scholarship_amt_2024_25'] : null,
            'scholarship_amt_2025_26'   => $data['scholarship_amt_2025_26'] !== '' ? $data['scholarship_amt_2025_26'] : null,
            'account_holder_name'       => $data['account_holder_name'] ?: null,
            'career_goal'               => $data['career_goal'] ?: null,
            'submitted_at'              => date('Y-m-d H:i:s'),
        ]);

        if ($appId) {
            // Store academic record
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO student_academics (student_id, session_id, class_year, college_name, board_university, marks_obtained, max_marks, percentage, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE class_year=VALUES(class_year), college_name=VALUES(college_name), board_university=VALUES(board_university), marks_obtained=VALUES(marks_obtained), max_marks=VALUES(max_marks), percentage=VALUES(percentage)"
            );
            $stmt->execute([
                (int) Auth::id(),
                (int) $activeSession['id'],
                $data['class_year'],
                $data['college_name'],
                $data['board_university'],
                $data['marks_obtained'] ?: null,
                $data['max_marks'] ?: null,
                $data['percentage'] ?: null,
            ]);

            // Store files and check if student profile photo should be updated
            $storedFiles = $this->storeUploads($appModel, $appId, $validatedUploads);
            if (isset($storedFiles['photo'])) {
                $profilePhotoPath = '/uploads/applications/' . $appId . '/' . $storedFiles['photo'];
                $studentModel->update((int) Auth::id(), ['profile_photo' => $profilePhotoPath]);
            }

            Flash::set('success', 'Scholarship application submitted! Your Application ID is TSVS-' . date('Y') . '-' . str_pad((string) $appId, 6, '0', STR_PAD_LEFT));
            Response::redirect('/applications');
        }

        Flash::set('error', 'Failed to submit application. Please try again.');
        Response::redirect('/applications/scholarship');
    }

    /**
     * Store a Pratibha Samman application.
     */
    public function storePratibha(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/applications/create');
        }

        $sessionModel = new AcademicSession();
        $activeSession = $sessionModel->active();

        if (!$activeSession) {
            Flash::set('error', 'Applications are currently closed.');
            Response::redirect('/applications');
        }

        $typeModel = new ApplicationType();
        $pratibhaType = $typeModel->findByName('Pratibha Samman');

        if (!$pratibhaType) {
            Flash::set('error', 'Application type not found.');
            Response::redirect('/applications');
        }

        // Retrieve and update student profile information if changed in form
        $studentModel = new \App\Models\Student();
        $studentModel->update((int) Auth::id(), [
            'first_name'  => Input::post('first_name', ''),
            'last_name'   => Input::post('last_name', ''),
            'father_name' => Input::post('father_name', ''),
            'mother_name' => Input::post('mother_name', ''),
            'dob'         => Input::post('dob', null) ?: null,
            'gender'      => Input::post('gender', ''),
            'address'     => Input::post('address', ''),
            'city'        => Input::post('city', ''),
            'district'    => Input::post('district', ''),
            'state'       => Input::post('state', ''),
            'pincode'     => Input::post('pincode', ''),
        ]);

        $data = [
            'class_year'            => Input::post('class_year', ''),
            'college_name'          => Input::post('college_name', ''),
            'board_university'      => Input::post('board_university', ''),
            'marks_obtained'        => Input::post('marks_obtained', ''),
            'max_marks'             => Input::post('max_marks', ''),
            'percentage'            => Input::post('percentage', ''),
            'achievement_title'     => Input::post('achievement_title', ''),
            'achievement_category'  => Input::post('achievement_category', ''),
            'achievement_level'     => Input::post('achievement_level', ''),
            'rank_position'         => Input::post('rank_position', ''),
        ];

        $v = Validator::make($data);
        $v->required('class_year', 'Class/Year')
          ->required('percentage', 'Percentage')
          ->numeric('percentage', 'Percentage')
          ->required('achievement_title', 'Achievement title');

        if ($v->fails()) {
            Flash::set('error', $v->first('class_year') ?? $v->first('percentage') ?? $v->first('achievement_title'));
            Flash::set('old', $data);
            Response::redirect('/applications/pratibha');
        }

        $pct = (float) $data['percentage'];
        if ($pct < 0 || $pct > 100) {
            Flash::set('error', 'Percentage must be between 0 and 100.');
            Flash::set('old', $data);
            Response::redirect('/applications/pratibha');
        }

        // Get draft application
        $appModel = new Application();
        $existingDraft = $appModel->findByStudent((int)Auth::id(), (int)$activeSession['id'], (int)$pratibhaType['id']);
        if (!$existingDraft) {
            $appId = $appModel->create([
                'student_id'          => (int) Auth::id(),
                'session_id'          => (int) $activeSession['id'],
                'application_type_id' => (int) $pratibhaType['id'],
                'status_id'           => 1,
                'type'                => 'pratibha',
            ]);
        } else {
            $appId = (int)$existingDraft['id'];
        }

        // Check already uploaded documents
        $dbDocs = $appModel->documents($appId);
        $dbDocTypes = array_column($dbDocs, 'document_type');

        // Validate uploads
        $requiredDocs = [
            'marksheet'   => 'Marksheet',
            'certificate' => 'Certificate',
            'photo'       => 'Photo',
            'signature'   => 'Signature',
        ];
        $validatedUploads = [];
        $uploader = new FileUploader();

        foreach ($requiredDocs as $field => $documentType) {
            $file = $_FILES[$field] ?? null;
            $hasInDb = in_array($documentType, $dbDocTypes, true);

            if (!$hasInDb && (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
                Flash::set('error', $documentType . ' upload is required.');
                Flash::set('old', $data);
                Response::redirect('/applications/pratibha');
            }

            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                if (!$uploader->validate($file)) {
                    Flash::set('error', $documentType . ': ' . $uploader->firstError());
                    Flash::set('old', $data);
                    Response::redirect('/applications/pratibha');
                }
                $validatedUploads[$field] = [
                    'type' => $documentType,
                    'file' => $file,
                ];
            }
        }

        // Update application
        $appModel->update($appId, [
            'achievement_title'   => $data['achievement_title'],
            'achievement_category'=> $data['achievement_category'] ?: null,
            'achievement_level'   => $data['achievement_level'] ?: null,
            'rank_position'       => $data['rank_position'] ?: null,
            'submitted_at'        => date('Y-m-d H:i:s'),
        ]);

        if ($appId) {
            // Store academic record
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO student_academics (student_id, session_id, class_year, college_name, board_university, marks_obtained, max_marks, percentage, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE class_year=VALUES(class_year), college_name=VALUES(college_name), board_university=VALUES(board_university), marks_obtained=VALUES(marks_obtained), max_marks=VALUES(max_marks), percentage=VALUES(percentage)"
            );
            $stmt->execute([
                (int) Auth::id(),
                (int) $activeSession['id'],
                $data['class_year'],
                $data['college_name'],
                $data['board_university'],
                $data['marks_obtained'] ?: null,
                $data['max_marks'] ?: null,
                $data['percentage'] ?: null,
            ]);

            $storedFiles = $this->storeUploads($appModel, $appId, $validatedUploads);
            if (isset($storedFiles['photo'])) {
                $profilePhotoPath = '/uploads/applications/' . $appId . '/' . $storedFiles['photo'];
                $studentModel->update((int) Auth::id(), ['profile_photo' => $profilePhotoPath]);
            }

            Flash::set('success', 'Pratibha Samman application submitted! Your Application ID is TSVS-' . date('Y') . '-' . str_pad((string) $appId, 6, '0', STR_PAD_LEFT));
            Response::redirect('/applications');
        }

        Flash::set('error', 'Failed to submit application. Please try again.');
        Response::redirect('/applications/pratibha');
    }

    /**
     * Show a single application detail.
     */
    public function show(int $id): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $appModel = new Application();
        $app = $appModel->find($id);

        if (!$app || (int) $app['student_id'] !== (int) Auth::id()) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/applications');
        }

        Response::view('applications/show', [
            'title'       => 'Application #' . $id . ' — Tamboli Samaj Portal',
            'application' => $app,
        ]);
    }

    private function validateUploads(array $requiredUploads, string $redirectTo, array $oldData): array
    {
        $uploader = new FileUploader();
        $validated = [];

        foreach ($requiredUploads as $field => $documentType) {
            $file = $_FILES[$field] ?? null;

            if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                Flash::set('error', $documentType . ' upload is required.');
                Flash::set('old', $oldData);
                Response::redirect($redirectTo);
            }

            if (!$uploader->validate($file)) {
                Flash::set('error', $documentType . ': ' . $uploader->firstError());
                Flash::set('old', $oldData);
                Response::redirect($redirectTo);
            }

            $validated[$field] = [
                'type' => $documentType,
                'file' => $file,
            ];
        }

        return $validated;
    }

    private function storeUploads(Application $appModel, int $applicationId, array $uploads): array
    {
        $uploader = new FileUploader();
        $directory = UPLOAD_PATH . '/applications/' . $applicationId;
        $stored = [];

        foreach ($uploads as $field => $upload) {
            $storedName = $uploader->upload($upload['file'], $directory);
            $appModel->addDocument($applicationId, $upload['type'], $upload['file'], $storedName);
            $stored[$field] = $storedName;
        }

        return $stored;
    }

    /**
     * Resubmit a disputed application with corrected documents.
     */
    public function resubmit(int $id): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/applications/' . $id);
        }

        $appModel = new Application();
        $app = $appModel->find($id);

        if (!$app || (int) $app['student_id'] !== (int) Auth::id()) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/applications');
        }

        if (($app['status_name'] ?? '') !== 'Disputed') {
            Flash::set('error', 'Only disputed applications can be resubmitted.');
            Response::redirect('/applications/' . $id);
        }

        $uploader = new FileUploader();
        $uploadedSomething = false;
        $validatedUploads = [];

        // Check for Marksheet, Passbook, Certificate files
        $possibleUploads = [
            'marksheet'   => 'Marksheet',
            'passbook'    => 'Passbook',
            'certificate' => 'Certificate',
        ];

        foreach ($possibleUploads as $field => $documentType) {
            $file = $_FILES[$field] ?? null;
            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                if (!$uploader->validate($file)) {
                    Flash::set('error', $documentType . ': ' . $uploader->firstError());
                    Response::redirect('/applications/' . $id);
                }
                $validatedUploads[$field] = [
                    'type' => $documentType,
                    'file' => $file,
                ];
                $uploadedSomething = true;
            }
        }

        if (!$uploadedSomething) {
            Flash::set('error', 'Please select at least one document to re-upload / resubmit.');
            Response::redirect('/applications/' . $id);
        }

        $directory = UPLOAD_PATH . '/applications/' . $id;
        $db = \App\Core\Database::getInstance();

        foreach ($validatedUploads as $upload) {
            $documentTypeId = $appModel->documentTypeId($upload['type']);
            if ($documentTypeId !== null) {
                // Find and delete existing physical files for this document type
                $stmt = $db->prepare("SELECT stored_name FROM application_documents WHERE application_id = ? AND document_type_id = ?");
                $stmt->execute([$id, $documentTypeId]);
                $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($existing as $doc) {
                    $oldPath = $directory . '/' . $doc['stored_name'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                // Delete records from database
                $stmt = $db->prepare("DELETE FROM application_documents WHERE application_id = ? AND document_type_id = ?");
                $stmt->execute([$id, $documentTypeId]);
            }

            // Upload and insert the new document
            $storedName = $uploader->upload($upload['file'], $directory);
            $appModel->addDocument($id, $upload['type'], $upload['file'], $storedName);
        }

        // Reset application status to Pending (status_id = 1) and clear dispute remarks
        $stmt = $db->prepare("UPDATE applications SET status_id = 1, dispute_message = NULL WHERE id = ?");
        $stmt->execute([$id]);
        Flash::set('success', 'Application has been successfully resubmitted. It will be reviewed again.');
        Response::redirect('/applications/' . $id);
    }

    /**
     * View and stream an uploaded file inline.
     */
    public function viewUpload(string $id, string $filename): void
    {
        if (!Auth::check()) {
            Response::redirect('/login');
        }

        $appModel = new Application();
        $app = $appModel->find((int) $id);

        if (!$app) {
            Response::abort(404, 'Application not found');
        }

        // Students can only view their own uploads, admins/representatives can view all
        if (!Auth::isAdmin() && !Auth::isRepresentative() && (int) $app['student_id'] !== (int) Auth::id()) {
            Response::abort(403, 'Unauthorized access to this document');
        }

        $filename = basename($filename);
        $filePath = UPLOAD_PATH . '/applications/' . $id . '/' . $filename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            Response::abort(404, 'File not found');
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        
        // Clear output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=604800');
        readfile($filePath);
        exit;
    }

    /**
     * Show the application edit form.
     */
    public function edit(string $id): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $appModel = new Application();
        $app = $appModel->find((int) $id);

        if (!$app || (int) $app['student_id'] !== (int) Auth::id()) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/applications');
        }

        $statusName = $app['status_name'] ?? '';
        if (!in_array($statusName, ['Pending', 'Disputed'], true)) {
            Flash::set('error', 'You can only edit applications in Pending or Disputed status.');
            Response::redirect('/applications/' . $id);
        }

        $sessionModel = new AcademicSession();
        $session = $sessionModel->find((int) $app['session_id']);

        $studentModel = new \App\Models\Student();
        $student = $studentModel->find((int) Auth::id());

        if ($app['type'] === 'scholarship') {
            Response::view('applications/scholarship', [
                'title'         => 'Edit Scholarship Application — Tamboli Samaj Portal',
                'activeSession' => $session ?: [],
                'student'       => $student ?: [],
                'application'   => $app,
                'isEdit'        => true
            ]);
        } else {
            Response::view('applications/pratibha', [
                'title'         => 'Edit Pratibha Samman Application — Tamboli Samaj Portal',
                'activeSession' => $session ?: [],
                'student'       => $student ?: [],
                'application'   => $app,
                'isEdit'        => true
            ]);
        }
    }

    /**
     * Process application details update.
     */
    public function update(string $id): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/applications/' . $id . '/edit');
        }

        $appModel = new Application();
        $app = $appModel->find((int) $id);

        if (!$app || (int) $app['student_id'] !== (int) Auth::id()) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/applications');
        }

        $statusName = $app['status_name'] ?? '';
        if (!in_array($statusName, ['Pending', 'Disputed'], true)) {
            Flash::set('error', 'You can only update applications in Pending or Disputed status.');
            Response::redirect('/applications/' . $id);
        }

        try {

        // Retrieve and update student profile information if changed in form
        $studentModel = new \App\Models\Student();
        $studentModel->update((int) Auth::id(), [
            'first_name'  => Input::post('first_name', ''),
            'last_name'   => Input::post('last_name', ''),
            'father_name' => Input::post('father_name', ''),
            'mother_name' => Input::post('mother_name', ''),
            'dob'         => Input::post('dob', null) ?: null,
            'gender'      => Input::post('gender', ''),
            'address'     => Input::post('address', ''),
            'city'        => Input::post('city', ''),
            'district'    => Input::post('district', ''),
            'state'       => Input::post('state', ''),
            'pincode'     => Input::post('pincode', ''),
        ]);

        if ($app['type'] === 'scholarship') {
            $data = [
                'class_year'                => Input::post('class_year', ''),
                'college_name'              => Input::post('college_name', ''),
                'board_university'          => Input::post('board_university', ''),
                'marks_obtained'            => Input::post('marks_obtained', ''),
                'max_marks'                 => Input::post('max_marks', ''),
                'percentage'                => Input::post('percentage', ''),
                'family_income'             => Input::post('family_income', ''),
                'bank_name'                 => Input::post('bank_name', ''),
                'account_number'            => Input::post('account_number', ''),
                'ifsc_code'                 => Input::post('ifsc_code', ''),
                'family_occupation'         => Input::post('family_occupation', ''),
                'family_members_count'      => Input::post('family_members_count', ''),
                'earning_members_count'     => Input::post('earning_members_count', ''),
                'current_class'             => Input::post('current_class', ''),
                'current_college'           => Input::post('current_college', ''),
                'prev_scholarship_received' => Input::post('prev_scholarship_received', ''),
                'scholarship_amt_2023_24'   => Input::post('scholarship_amt_2023_24', ''),
                'scholarship_amt_2024_25'   => Input::post('scholarship_amt_2024_25', ''),
                'scholarship_amt_2025_26'   => Input::post('scholarship_amt_2025_26', ''),
                'account_holder_name'       => Input::post('account_holder_name', ''),
                'career_goal'               => Input::post('career_goal', ''),
            ];

            $v = Validator::make($data);
            $v->required('class_year', 'Class/Year')
              ->required('percentage', 'Percentage')
              ->numeric('percentage', 'Percentage')
              ->required('bank_name', 'Bank name')
              ->required('account_number', 'Account number')
              ->required('ifsc_code', 'IFSC code')
              ->required('family_occupation', 'Family occupation')
              ->required('family_members_count', 'Family members count')
              ->required('earning_members_count', 'Earning members count')
              ->required('current_class', 'Current class')
              ->required('current_college', 'Current college')
              ->required('account_holder_name', 'Account holder name')
              ->required('career_goal', 'Career goal');

            if ($v->fails()) {
                Flash::set('error', $v->first('class_year') ?? $v->first('percentage') ?? $v->first('bank_name') ?? $v->first('family_occupation') ?? $v->first('current_class'));
                Response::redirect('/applications/' . $id . '/edit');
            }

            $ifsc = strtoupper(trim($data['ifsc_code']));
            if (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc)) {
                Flash::set('error', 'IFSC code must be in the format: 4 uppercase letters, a zero, and 6 alphanumeric characters (e.g., SBIN0012345).');
                Response::redirect('/applications/' . $id . '/edit');
            }
            $data['ifsc_code'] = $ifsc;

            $pct = (float) $data['percentage'];
            if ($pct < 0 || $pct > 100) {
                Flash::set('error', 'Percentage must be between 0 and 100.');
                Response::redirect('/applications/' . $id . '/edit');
            }

            // Update application fields
            $appModel->update((int) $id, [
                'family_income'             => $data['family_income'] !== '' ? $data['family_income'] : null,
                'bank_name'                 => $data['bank_name'],
                'account_number'            => $data['account_number'],
                'ifsc_code'                 => $data['ifsc_code'],
                'family_occupation'         => $data['family_occupation'] ?: null,
                'family_members_count'      => $data['family_members_count'] !== '' ? (int)$data['family_members_count'] : null,
                'earning_members_count'     => $data['earning_members_count'] !== '' ? (int)$data['earning_members_count'] : null,
                'current_class'             => $data['current_class'] ?: null,
                'current_college'           => $data['current_college'] ?: null,
                'prev_scholarship_received' => $data['prev_scholarship_received'] ?: null,
                'scholarship_amt_2023_24'   => $data['scholarship_amt_2023_24'] !== '' ? $data['scholarship_amt_2023_24'] : null,
                'scholarship_amt_2024_25'   => $data['scholarship_amt_2024_25'] !== '' ? $data['scholarship_amt_2024_25'] : null,
                'scholarship_amt_2025_26'   => $data['scholarship_amt_2025_26'] !== '' ? $data['scholarship_amt_2025_26'] : null,
                'account_holder_name'       => $data['account_holder_name'] ?: null,
                'career_goal'               => $data['career_goal'] ?: null,
                'status_id'                 => 1, // reset to pending
                'dispute_message'           => null, // clear dispute message
                'updated_at'                => date('Y-m-d H:i:s'),
            ]);

            // Update academics
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO student_academics (student_id, session_id, class_year, college_name, board_university, marks_obtained, max_marks, percentage, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE class_year=VALUES(class_year), college_name=VALUES(college_name), board_university=VALUES(board_university), marks_obtained=VALUES(marks_obtained), max_marks=VALUES(max_marks), percentage=VALUES(percentage)"
            );
            $stmt->execute([
                (int) Auth::id(),
                (int) $app['session_id'],
                $data['class_year'],
                $data['college_name'],
                $data['board_university'],
                $data['marks_obtained'] ?: null,
                $data['max_marks'] ?: null,
                $data['percentage'] ?: null,
            ]);

            // Check files and update
            $uploader = new FileUploader();
            $possibleUploads = [
                'marksheet' => 'Marksheet',
                'passbook'  => 'Passbook',
                'photo'     => 'Photo',
                'signature' => 'Signature',
            ];
            $validatedUploads = [];
            foreach ($possibleUploads as $field => $documentType) {
                $file = $_FILES[$field] ?? null;
                if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    if (!$uploader->validate($file)) {
                        Flash::set('error', $documentType . ': ' . $uploader->firstError());
                        Response::redirect('/applications/' . $id . '/edit');
                    }
                    $validatedUploads[$field] = [
                        'type' => $documentType,
                        'file' => $file,
                    ];
                }
            }

            if (!empty($validatedUploads)) {
                $directory = UPLOAD_PATH . '/applications/' . $id;
                foreach ($validatedUploads as $field => $upload) {
                    $documentTypeId = $appModel->documentTypeId($upload['type']);
                    if ($documentTypeId !== null) {
                        // Find and delete existing physical files for this document type
                        $stmt = $db->prepare("SELECT stored_name FROM application_documents WHERE application_id = ? AND document_type_id = ?");
                        $stmt->execute([$id, $documentTypeId]);
                        $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($existing as $doc) {
                            $oldPath = $directory . '/' . $doc['stored_name'];
                            if (file_exists($oldPath)) {
                                @unlink($oldPath);
                            }
                        }

                        // Delete records from database
                        $stmt = $db->prepare("DELETE FROM application_documents WHERE application_id = ? AND document_type_id = ?");
                        $stmt->execute([$id, $documentTypeId]);
                    }

                    // Upload and insert the new document
                    $storedName = $uploader->upload($upload['file'], $directory);
                    $appModel->addDocument((int) $id, $upload['type'], $upload['file'], $storedName);

                    // Update student profile photo if updated photo
                    if ($upload['type'] === 'Photo') {
                        $profilePhotoPath = '/uploads/applications/' . $id . '/' . $storedName;
                        $studentModel->update((int) Auth::id(), ['profile_photo' => $profilePhotoPath]);
                    }
                }
            }

        } else {
            // Pratibha update
            $data = [
                'class_year'            => Input::post('class_year', ''),
                'college_name'          => Input::post('college_name', ''),
                'board_university'      => Input::post('board_university', ''),
                'marks_obtained'        => Input::post('marks_obtained', ''),
                'max_marks'             => Input::post('max_marks', ''),
                'percentage'            => Input::post('percentage', ''),
                'achievement_title'     => Input::post('achievement_title', ''),
                'achievement_category'  => Input::post('achievement_category', ''),
                'achievement_level'     => Input::post('achievement_level', ''),
                'rank_position'         => Input::post('rank_position', ''),
            ];

            $v = Validator::make($data);
            $v->required('class_year', 'Class/Year')
              ->required('percentage', 'Percentage')
              ->numeric('percentage', 'Percentage')
              ->required('achievement_title', 'Achievement title');

            if ($v->fails()) {
                Flash::set('error', $v->first('class_year') ?? $v->first('percentage') ?? $v->first('achievement_title'));
                Response::redirect('/applications/' . $id . '/edit');
            }

            $pct = (float) $data['percentage'];
            if ($pct < 0 || $pct > 100) {
                Flash::set('error', 'Percentage must be between 0 and 100.');
                Response::redirect('/applications/' . $id . '/edit');
            }

            // Update application fields
            $appModel->update((int) $id, [
                'achievement_title'   => $data['achievement_title'],
                'achievement_category'=> $data['achievement_category'] ?: null,
                'achievement_level'   => $data['achievement_level'] ?: null,
                'rank_position'       => $data['rank_position'] ?: null,
                'status_id'           => 1, // reset to pending
                'dispute_message'     => null, // clear dispute message
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);

            // Update academics
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO student_academics (student_id, session_id, class_year, college_name, board_university, marks_obtained, max_marks, percentage, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE class_year=VALUES(class_year), college_name=VALUES(college_name), board_university=VALUES(board_university), marks_obtained=VALUES(marks_obtained), max_marks=VALUES(max_marks), percentage=VALUES(percentage)"
            );
            $stmt->execute([
                (int) Auth::id(),
                (int) $app['session_id'],
                $data['class_year'],
                $data['college_name'],
                $data['board_university'],
                $data['marks_obtained'] ?: null,
                $data['max_marks'] ?: null,
                $data['percentage'] ?: null,
            ]);

            // Check files and update
            $uploader = new FileUploader();
            $possibleUploads = [
                'marksheet'   => 'Marksheet',
                'certificate' => 'Certificate',
                'photo'       => 'Photo',
                'signature'   => 'Signature',
            ];
            $validatedUploads = [];
            foreach ($possibleUploads as $field => $documentType) {
                $file = $_FILES[$field] ?? null;
                if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    if (!$uploader->validate($file)) {
                        Flash::set('error', $documentType . ': ' . $uploader->firstError());
                        Response::redirect('/applications/' . $id . '/edit');
                    }
                    $validatedUploads[$field] = [
                        'type' => $documentType,
                        'file' => $file,
                    ];
                }
            }

            if (!empty($validatedUploads)) {
                $directory = UPLOAD_PATH . '/applications/' . $id;
                foreach ($validatedUploads as $field => $upload) {
                    $documentTypeId = $appModel->documentTypeId($upload['type']);
                    if ($documentTypeId !== null) {
                        // Find and delete existing physical files for this document type
                        $stmt = $db->prepare("SELECT stored_name FROM application_documents WHERE application_id = ? AND document_type_id = ?");
                        $stmt->execute([$id, $documentTypeId]);
                        $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($existing as $doc) {
                            $oldPath = $directory . '/' . $doc['stored_name'];
                            if (file_exists($oldPath)) {
                                @unlink($oldPath);
                            }
                        }

                        // Delete records from database
                        $stmt = $db->prepare("DELETE FROM application_documents WHERE application_id = ? AND document_type_id = ?");
                        $stmt->execute([$id, $documentTypeId]);
                    }

                    // Upload and insert the new document
                    $storedName = $uploader->upload($upload['file'], $directory);

                    if ($storedName === false) {
                        Flash::set('error', $upload['type'] . ': ' . $uploader->firstError());
                        Response::redirect('/applications/' . $id . '/edit');
                    }

                    $appModel->addDocument((int) $id, $upload['type'], $upload['file'], $storedName);

                    // Update student profile photo if updated photo
                    if ($upload['type'] === 'Photo') {
                        $profilePhotoPath = '/uploads/applications/' . $id . '/' . $storedName;
                        $studentModel->update((int) Auth::id(), ['profile_photo' => $profilePhotoPath]);
                    }
                }
            }
        }

        Flash::set('success', 'Application details updated successfully.');
        Response::redirect('/applications/' . $id);

        } catch (\Throwable $e) {
            Logger::error('Application update failed', [
                'application_id' => $id,
                'student_id'     => Auth::id(),
                'error'          => $e->getMessage(),
            ]);
            Flash::set('error', 'A temporary error occurred while updating. Please try again.');
            Response::redirect('/applications/' . $id . '/edit');
        }
    }

    /**
     * AJAX Document Upload.
     */
    public function uploadDocumentAjax(string $id): void
    {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
            exit;
        }

        $appModel = new Application();
        $app = $appModel->find((int)$id);

        if (!$app || (!Auth::isAdmin() && !Auth::isRepresentative() && (int)$app['student_id'] !== (int)Auth::id())) {
            echo json_encode(['success' => false, 'error' => 'Application not found or unauthorized']);
            exit;
        }

        $documentType = Input::post('document_type', '');
        if (!in_array($documentType, ['Photo', 'Marksheet', 'Passbook', 'Certificate', 'Signature'], true)) {
            echo json_encode(['success' => false, 'error' => 'Invalid document type: ' . $documentType]);
            exit;
        }

        $file = $_FILES['file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error.']);
            exit;
        }

        $uploader = new FileUploader();
        if (!$uploader->validate($file)) {
            echo json_encode(['success' => false, 'error' => $uploader->firstError()]);
            exit;
        }

        $db = \App\Core\Database::getInstance();
        $documentTypeId = $appModel->documentTypeId($documentType);

        if ($documentTypeId === null) {
            echo json_encode(['success' => false, 'error' => 'Invalid document type.']);
            exit;
        }

        $directory = UPLOAD_PATH . '/applications/' . $id;

        // Find and delete existing physical files for this document type
        $stmt = $db->prepare("SELECT stored_name FROM application_documents WHERE application_id = ? AND document_type_id = ?");
        $stmt->execute([$id, $documentTypeId]);
        $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($existing as $doc) {
            $oldPath = $directory . '/' . $doc['stored_name'];
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Delete records from database
        $stmt = $db->prepare("DELETE FROM application_documents WHERE application_id = ? AND document_type_id = ?");
        $stmt->execute([$id, $documentTypeId]);

        // Upload and insert the new document
        $storedName = $uploader->upload($file, $directory);
        if ($storedName === false) {
            echo json_encode(['success' => false, 'error' => $uploader->firstError()]);
            exit;
        }

        $appModel->addDocument((int)$id, $documentType, $file, $storedName);

        // Update student profile photo if updated photo
        if ($documentType === 'Photo') {
            $profilePhotoPath = '/uploads/applications/' . $id . '/' . $storedName;
            $studentModel = new \App\Models\Student();
            $studentModel->update((int)$app['student_id'], ['profile_photo' => $profilePhotoPath]);
        }

        echo json_encode([
            'success' => true,
            'stored_name' => $storedName,
            'original_name' => $file['name'],
            'url' => '/uploads/applications/' . $id . '/' . $storedName
        ]);
        exit;
    }

    /**
     * AJAX Document Deletion.
     */
    public function deleteDocumentAjax(string $id): void
    {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
            exit;
        }

        $appModel = new Application();
        $app = $appModel->find((int)$id);

        if (!$app || (!Auth::isAdmin() && !Auth::isRepresentative() && (int)$app['student_id'] !== (int)Auth::id())) {
            echo json_encode(['success' => false, 'error' => 'Application not found or unauthorized']);
            exit;
        }

        $documentType = Input::post('document_type', '');
        if (!in_array($documentType, ['Photo', 'Marksheet', 'Passbook', 'Certificate', 'Signature'], true)) {
            echo json_encode(['success' => false, 'error' => 'Invalid document type']);
            exit;
        }

        $db = \App\Core\Database::getInstance();
        $documentTypeId = $appModel->documentTypeId($documentType);

        if ($documentTypeId === null) {
            echo json_encode(['success' => false, 'error' => 'Invalid document type.']);
            exit;
        }

        $directory = UPLOAD_PATH . '/applications/' . $id;

        // Find and delete physical files
        $stmt = $db->prepare("SELECT stored_name FROM application_documents WHERE application_id = ? AND document_type_id = ?");
        $stmt->execute([$id, $documentTypeId]);
        $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($existing as $doc) {
            $oldPath = $directory . '/' . $doc['stored_name'];
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Delete records from database
        $stmt = $db->prepare("DELETE FROM application_documents WHERE application_id = ? AND document_type_id = ?");
        $stmt->execute([$id, $documentTypeId]);

        // Reset profile photo if student photo deleted
        if ($documentType === 'Photo') {
            $studentModel = new \App\Models\Student();
            $studentModel->update((int)$app['student_id'], ['profile_photo' => null]);
        }

        echo json_encode(['success' => true]);
        exit;
    }
}

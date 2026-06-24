<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\FileUploader;
use App\Core\Helpers;
use App\Core\Input;
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

        // Check for duplicate
        $appModel = new Application();
        $typeModel = new ApplicationType();
        $scholarshipType = $typeModel->findByName('Scholarship');

        if ($scholarshipType) {
            $existing = $appModel->findByStudent(
                (int) Auth::id(),
                (int) $activeSession['id'],
                (int) $scholarshipType['id']
            );

            // Check if already has a scholarship application
            foreach ([$existing] as $row) {
                if ($row && (int) $row['application_type_id'] === (int) $scholarshipType['id']) {
                    Flash::set('error', 'You have already applied for Scholarship in this session.');
                    Response::redirect('/applications');
                }
            }
        }

        Response::view('applications/scholarship', [
            'title'         => 'Scholarship Application — Tamboli Samaj Portal',
            'activeSession' => $activeSession,
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

        if ($pratibhaType) {
            $existing = $appModel->findByStudent(
                (int) Auth::id(),
                (int) $activeSession['id'],
                (int) $pratibhaType['id']
            );

            foreach ([$existing] as $row) {
                if ($row && (int) $row['application_type_id'] === (int) $pratibhaType['id']) {
                    Flash::set('error', 'You have already registered for Pratibha Samman in this session.');
                    Response::redirect('/applications');
                }
            }
        }

        Response::view('applications/pratibha', [
            'title'         => 'Pratibha Samman Application — Tamboli Samaj Portal',
            'activeSession' => $activeSession,
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

        // Validate
        $data = [
            'class_year'       => Input::post('class_year', ''),
            'college_name'     => Input::post('college_name', ''),
            'board_university' => Input::post('board_university', ''),
            'marks_obtained'   => Input::post('marks_obtained', ''),
            'max_marks'        => Input::post('max_marks', ''),
            'percentage'       => Input::post('percentage', ''),
            'family_income'    => Input::post('family_income', ''),
            'bank_name'        => Input::post('bank_name', ''),
            'account_number'   => Input::post('account_number', ''),
            'ifsc_code'        => Input::post('ifsc_code', ''),
        ];

        $v = Validator::make($data);
        $v->required('class_year', 'Class/Year')
          ->required('percentage', 'Percentage')
          ->numeric('percentage', 'Percentage')
          ->required('bank_name', 'Bank name')
          ->required('account_number', 'Account number')
          ->required('ifsc_code', 'IFSC code');

        if ($v->fails()) {
            Flash::set('error', $v->first('class_year') ?? $v->first('percentage') ?? $v->first('bank_name'));
            Flash::set('old', $data);
            Response::redirect('/applications/scholarship');
        }

        $pct = (float) $data['percentage'];
        if ($pct < 0 || $pct > 100) {
            Flash::set('error', 'Percentage must be between 0 and 100.');
            Flash::set('old', $data);
            Response::redirect('/applications/scholarship');
        }

        $validatedUploads = $this->validateUploads([
            'marksheet' => 'Marksheet',
            'passbook'  => 'Passbook',
        ], '/applications/scholarship', $data);

        // Store application — we also store academic marks in student_academics
        $appModel = new Application();
        $appId = $appModel->create([
            'student_id'          => (int) Auth::id(),
            'session_id'          => (int) $activeSession['id'],
            'application_type_id' => (int) $scholarshipType['id'],
            'status_id'           => 1,
            'type'                => 'scholarship',
            'family_income'       => $data['family_income'] ?: null,
            'bank_name'           => $data['bank_name'],
            'account_number'      => $data['account_number'],
            'ifsc_code'           => $data['ifsc_code'],
            'submitted_at'        => date('Y-m-d H:i:s'),
        ]);

        if ($appId) {
            // Store academic record
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO student_academics (student_id, session_id, course_name, class_year, college_name, board_university, marks_obtained, max_marks, percentage, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE class_year=VALUES(class_year), college_name=VALUES(college_name), board_university=VALUES(board_university), marks_obtained=VALUES(marks_obtained), max_marks=VALUES(max_marks), percentage=VALUES(percentage)"
            );
            $stmt->execute([
                (int) Auth::id(),
                (int) $activeSession['id'],
                $data['class_year'],
                $data['class_year'],
                $data['college_name'],
                $data['board_university'],
                $data['marks_obtained'] ?: null,
                $data['max_marks'] ?: null,
                $data['percentage'] ?: null,
            ]);

            $this->storeUploads($appModel, $appId, $validatedUploads);

            Flash::set('success', 'Scholarship application submitted! Your application number is TSVS-' . date('Y') . '-' . str_pad((string) $appId, 6, '0', STR_PAD_LEFT));
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

        $validatedUploads = $this->validateUploads([
            'marksheet'   => 'Marksheet',
            'certificate' => 'Certificate',
        ], '/applications/pratibha', $data);

        $appModel = new Application();
        $appId = $appModel->create([
            'student_id'          => (int) Auth::id(),
            'session_id'          => (int) $activeSession['id'],
            'application_type_id' => (int) $pratibhaType['id'],
            'status_id'           => 1,
            'type'                => 'pratibha',
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
                "INSERT INTO student_academics (student_id, session_id, course_name, class_year, college_name, board_university, marks_obtained, max_marks, percentage, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE class_year=VALUES(class_year), college_name=VALUES(college_name), board_university=VALUES(board_university), marks_obtained=VALUES(marks_obtained), max_marks=VALUES(max_marks), percentage=VALUES(percentage)"
            );
            $stmt->execute([
                (int) Auth::id(),
                (int) $activeSession['id'],
                $data['class_year'],
                $data['class_year'],
                $data['college_name'],
                $data['board_university'],
                $data['marks_obtained'] ?: null,
                $data['max_marks'] ?: null,
                $data['percentage'] ?: null,
            ]);

            $this->storeUploads($appModel, $appId, $validatedUploads);

            Flash::set('success', 'Pratibha Samman application submitted! Your application number is TSVS-' . date('Y') . '-' . str_pad((string) $appId, 6, '0', STR_PAD_LEFT));
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

    private function storeUploads(Application $appModel, int $applicationId, array $uploads): void
    {
        $uploader = new FileUploader();
        $directory = UPLOAD_PATH . '/applications/' . $applicationId;

        foreach ($uploads as $upload) {
            $storedName = $uploader->upload($upload['file'], $directory);
            $appModel->addDocument($applicationId, $upload['type'], $upload['file'], $storedName);
        }
    }
}

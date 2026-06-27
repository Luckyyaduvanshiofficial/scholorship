<?php

declare(strict_types=1);

namespace App\Controllers\Student;

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
use PDO;

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
            Response::redirect('/dashboard/applications');
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
            Response::redirect('/dashboard/applications');
        }

        $step = (int) Input::get('step', 1);
        if ($step < 1 || $step > 4) {
            $step = 1;
        }

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
                    Response::redirect('/dashboard/applications');
                } else {
                    $application = $appModel->find((int) $existing['id']);
                }
            } else {
                // Auto-create a draft application immediately
                $appId = $appModel->create([
                    'student_id'          => (int) Auth::id(),
                    'session_id'          => (int) $activeSession['id'],
                    'application_type_id' => (int) $scholarshipType['id'],
                    'status_id'           => 1, // Draft (old schema)
                    'status'              => 'draft', // Draft (new schema)
                    'type'                => 'scholarship',
                    'submitted_at'        => null,
                ]);
                $application = $appModel->find((int) $appId);
                $appModel->logHistory((int) $appId, 'draft_saved', (int) Auth::id(), null, ['step' => 1]);
            }
        }

        if ($application) {
            \App\Core\Session::set('draft_id', $application['id']);
        }

        $studentModel = new \App\Models\Student();
        $student = $studentModel->find((int) Auth::id());

        Response::view('applications/scholarship', [
            'title'         => 'Scholarship Application — Tamboli Samaj Portal',
            'activeSession' => $activeSession,
            'student'       => $student ?: [],
            'application'   => $application,
            'step'          => $step,
        ]);
    }

    public function pratibha(): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $sessionModel = new AcademicSession();
        $activeSession = $sessionModel->active();

        if (!$activeSession) {
            Flash::set('error', 'Applications are currently closed.');
            Response::redirect('/dashboard/applications');
        }

        $step = (int) Input::get('step', 1);
        if ($step < 1 || $step > 4) {
            $step = 1;
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
                    Response::redirect('/dashboard/applications');
                } else {
                    $application = $appModel->find((int) $existing['id']);
                }
            } else {
                // Auto-create a draft application immediately
                $appId = $appModel->create([
                    'student_id'          => (int) Auth::id(),
                    'session_id'          => (int) $activeSession['id'],
                    'application_type_id' => (int) $pratibhaType['id'],
                    'status_id'           => 1, // Draft (old schema)
                    'status'              => 'draft', // Draft (new schema)
                    'type'                => 'pratibha',
                    'submitted_at'        => null,
                ]);
                $application = $appModel->find((int) $appId);
                $appModel->logHistory((int) $appId, 'draft_saved', (int) Auth::id(), null, ['step' => 1]);
            }
        }

        if ($application) {
            \App\Core\Session::set('draft_id', $application['id']);
        }

        $studentModel = new \App\Models\Student();
        $student = $studentModel->find((int) Auth::id());

        Response::view('applications/pratibha', [
            'title'         => 'Pratibha Samman Application — Tamboli Samaj Portal',
            'activeSession' => $activeSession,
            'student'       => $student ?: [],
            'application'   => $application,
            'step'          => $step,
        ]);
    }

    public function storeStep(int $step): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/dashboard/applications');
        }

        $appModel = new Application();
        $draftId = \App\Core\Session::get('draft_id');
        if (!$draftId) {
            $draftId = Input::post('application_id');
        }
        
        $app = $draftId ? $appModel->find((int) $draftId) : null;

        if (!$app || (int) $app['student_id'] !== (int) Auth::id()) {
            Flash::set('error', 'Application not found or unauthorized.');
            Response::redirect('/dashboard/applications');
        }

        $appId = (int) $app['id'];
        $type = $app['type']; // 'scholarship' or 'pratibha'
        $redirectUrl = ($type === 'scholarship') ? '/dashboard/applications/scholarship' : '/dashboard/applications/pratibha';

        $action = Input::post('action'); // 'save_draft', 'next', 'final_submit'

        if ($action === 'save_draft') {
            if ($step === 1) {
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

                $appModel->update($appId, [
                    'family_occupation'     => Input::post('family_occupation', ''),
                    'career_goal'           => Input::post('career_goal', ''),
                    'family_members_count'  => Input::post('family_members_count', '') !== '' ? (int) Input::post('family_members_count') : null,
                    'earning_members_count' => Input::post('earning_members_count', '') !== '' ? (int) Input::post('earning_members_count') : null,
                ]);
            } elseif ($step === 2) {
                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare(
                    "INSERT INTO student_academics (student_id, session_id, class_year, college_name, board_university, marks_obtained, max_marks, percentage, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                     ON DUPLICATE KEY UPDATE class_year=VALUES(class_year), college_name=VALUES(college_name), board_university=VALUES(board_university), marks_obtained=VALUES(marks_obtained), max_marks=VALUES(max_marks), percentage=VALUES(percentage)"
                );
                $stmt->execute([
                    (int) Auth::id(),
                    (int) $app['session_id'],
                    Input::post('class_year', ''),
                    Input::post('college_name', ''),
                    Input::post('board_university', ''),
                    Input::post('marks_obtained', '') !== '' ? Input::post('marks_obtained') : null,
                    Input::post('max_marks', '') !== '' ? Input::post('max_marks') : null,
                    Input::post('percentage', '') !== '' ? Input::post('percentage') : null,
                ]);

                if ($type === 'scholarship') {
                    $appModel->update($appId, [
                        'current_class'             => Input::post('current_class', ''),
                        'current_college'           => Input::post('current_college', ''),
                        'prev_scholarship_received' => Input::post('prev_scholarship_received', 'नहीं'),
                        'scholarship_amt_2023_24'   => Input::post('scholarship_amt_2023_24', '') !== '' ? Input::post('scholarship_amt_2023_24') : null,
                        'scholarship_amt_2024_25'   => Input::post('scholarship_amt_2024_25', '') !== '' ? Input::post('scholarship_amt_2024_25') : null,
                        'scholarship_amt_2025_26'   => Input::post('scholarship_amt_2025_26', '') !== '' ? Input::post('scholarship_amt_2025_26') : null,
                        'bank_name'                 => Input::post('bank_name', ''),
                        'account_number'            => Input::post('account_number', ''),
                        'account_holder_name'       => Input::post('account_holder_name', ''),
                        'ifsc_code'                 => Input::post('ifsc_code', ''),
                        'family_income'             => Input::post('family_income', '') !== '' ? Input::post('family_income') : null,
                    ]);
                } else {
                    $appModel->update($appId, [
                        'achievement_title'    => Input::post('achievement_title', ''),
                        'achievement_category' => Input::post('achievement_category', ''),
                        'achievement_level'    => Input::post('achievement_level', ''),
                        'rank_position'        => Input::post('rank_position', ''),
                    ]);
                }
            }

            $appModel->logHistory($appId, 'draft_saved', (int) Auth::id(), null, ['step' => $step]);
            Flash::set('success', 'प्रारूप सहेज लिया गया है / Draft saved successfully.');
            Response::redirect($redirectUrl . '?step=' . $step);
        }

        if ($action === 'next') {
            if ($step === 1) {
                $v = Validator::make($_POST);
                $v->required('first_name', 'First Name')
                  ->required('last_name', 'Last Name')
                  ->required('father_name', 'Father Name')
                  ->required('mother_name', 'Mother Name')
                  ->required('dob', 'Date of Birth')
                  ->required('gender', 'Gender')
                  ->required('address', 'Permanent Address')
                  ->required('city', 'City')
                  ->required('district', 'District')
                  ->required('pincode', 'Pincode')
                  ->required('family_occupation', 'Family Occupation')
                  ->required('family_members_count', 'Family Members')
                  ->required('earning_members_count', 'Earning Members')
                  ->required('career_goal', 'Career Goal');

                if ($v->fails()) {
                    Flash::set('error', 'Validation Error: ' . $this->getFirstError($v));
                    Flash::set('old', $_POST);
                    Response::redirect($redirectUrl . '?step=' . $step);
                }

                $pincode = trim((string) Input::post('pincode', ''));
                if (!preg_match('/^\d{6}$/', $pincode)) {
                    Flash::set('error', 'Pincode must be exactly 6 digits.');
                    Flash::set('old', $_POST);
                    Response::redirect($redirectUrl . '?step=' . $step);
                }

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

                $appModel->update($appId, [
                    'family_occupation'     => Input::post('family_occupation', ''),
                    'career_goal'           => Input::post('career_goal', ''),
                    'family_members_count'  => (int) Input::post('family_members_count'),
                    'earning_members_count' => (int) Input::post('earning_members_count'),
                ]);

                $appModel->logHistory($appId, 'step_1_completed', (int) Auth::id());
                Response::redirect($redirectUrl . '?step=2');

            } elseif ($step === 2) {
                $v = Validator::make($_POST);
                $v->required('class_year', 'Class/Year')
                  ->required('percentage', 'Percentage')
                  ->numeric('percentage', 'Percentage');

                if ($type === 'scholarship') {
                    $v->required('current_class', 'Current Class')
                      ->required('current_college', 'Current College')
                      ->required('bank_name', 'Bank Name')
                      ->required('account_number', 'Account Number')
                      ->required('confirm_account_number', 'Confirm Account Number')
                      ->required('account_holder_name', 'Account Holder Name')
                      ->required('ifsc_code', 'IFSC Code')
                      ->required('family_income', 'Family Income')
                      ->numeric('family_income', 'Family Income');
                } else {
                    $v->required('achievement_title', 'Achievement Title');
                }

                if ($v->fails()) {
                    Flash::set('error', 'Validation Error: ' . $this->getFirstError($v));
                    Flash::set('old', $_POST);
                    Response::redirect($redirectUrl . '?step=' . $step);
                }

                $pct = (float) Input::post('percentage');
                if ($pct < 0 || $pct > 100) {
                    Flash::set('error', 'Percentage must be between 0 and 100.');
                    Flash::set('old', $_POST);
                    Response::redirect($redirectUrl . '?step=' . $step);
                }

                if ($type === 'scholarship') {
                    if (Input::post('account_number') !== Input::post('confirm_account_number')) {
                        Flash::set('error', 'Account number confirmation does not match.');
                        Flash::set('old', $_POST);
                        Response::redirect($redirectUrl . '?step=' . $step);
                    }

                    $ifsc = strtoupper(trim((string) Input::post('ifsc_code', '')));
                    if (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc)) {
                        Flash::set('error', 'IFSC code format is invalid.');
                        Flash::set('old', $_POST);
                        Response::redirect($redirectUrl . '?step=' . $step);
                    }
                }

                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare(
                    "INSERT INTO student_academics (student_id, session_id, class_year, college_name, board_university, marks_obtained, max_marks, percentage, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                     ON DUPLICATE KEY UPDATE class_year=VALUES(class_year), college_name=VALUES(college_name), board_university=VALUES(board_university), marks_obtained=VALUES(marks_obtained), max_marks=VALUES(max_marks), percentage=VALUES(percentage)"
                );
                $stmt->execute([
                    (int) Auth::id(),
                    (int) $app['session_id'],
                    Input::post('class_year', ''),
                    Input::post('college_name', ''),
                    Input::post('board_university', ''),
                    Input::post('marks_obtained', '') !== '' ? Input::post('marks_obtained') : null,
                    Input::post('max_marks', '') !== '' ? Input::post('max_marks') : null,
                    $pct,
                ]);

                if ($type === 'scholarship') {
                    $appModel->update($appId, [
                        'current_class'             => Input::post('current_class', ''),
                        'current_college'           => Input::post('current_college', ''),
                        'prev_scholarship_received' => Input::post('prev_scholarship_received', 'नहीं'),
                        'scholarship_amt_2023_24'   => Input::post('scholarship_amt_2023_24', '') !== '' ? Input::post('scholarship_amt_2023_24') : null,
                        'scholarship_amt_2024_25'   => Input::post('scholarship_amt_2024_25', '') !== '' ? Input::post('scholarship_amt_2024_25') : null,
                        'scholarship_amt_2025_26'   => Input::post('scholarship_amt_2025_26', '') !== '' ? Input::post('scholarship_amt_2025_26') : null,
                        'bank_name'                 => Input::post('bank_name', ''),
                        'account_number'            => Input::post('account_number', ''),
                        'account_holder_name'       => Input::post('account_holder_name', ''),
                        'ifsc_code'                 => $ifsc,
                        'family_income'             => Input::post('family_income', '') !== '' ? Input::post('family_income') : null,
                    ]);
                } else {
                    $appModel->update($appId, [
                        'achievement_title'    => Input::post('achievement_title', ''),
                        'achievement_category' => Input::post('achievement_category', ''),
                        'achievement_level'    => Input::post('achievement_level', ''),
                        'rank_position'        => Input::post('rank_position', ''),
                    ]);
                }

                $appModel->logHistory($appId, 'step_2_completed', (int) Auth::id());
                Response::redirect($redirectUrl . '?step=3');

            } elseif ($step === 3) {
                $docs = $appModel->documents($appId);
                $uploadedTypes = array_column($docs, 'document_type');
                
                $required = ($type === 'scholarship') 
                    ? ['Photo', 'Signature', 'Marksheet', 'Passbook']
                    : ['Photo', 'Signature', 'Marksheet', 'Certificate'];

                foreach ($required as $req) {
                    if (!in_array($req, $uploadedTypes, true)) {
                        Flash::set('error', "दस्तावेज़ आवश्यक है: {$req} / Document required: {$req}");
                        Response::redirect($redirectUrl . '?step=3');
                    }
                }

                $appModel->logHistory($appId, 'step_3_completed', (int) Auth::id());
                Response::redirect($redirectUrl . '?step=4');
            }
        }

        if ($action === 'final_submit' && $step === 4) {
            if ((int) Input::post('self_declared') !== 1) {
                Flash::set('error', 'कृपया स्व-घोषणा बॉक्स को चेक करें / Please check the self-declaration box.');
                Response::redirect($redirectUrl . '?step=4');
            }

            $fullApp = $appModel->find($appId);
            $errors = [];
            $failedStep = 4;

            $personalFields = ['first_name', 'last_name', 'father_name', 'mother_name', 'dob', 'gender', 'address', 'city', 'district', 'pincode', 'family_occupation', 'family_members_count', 'earning_members_count', 'career_goal'];
            foreach ($personalFields as $f) {
                if (empty($fullApp[$f])) {
                    $errors[] = "Missing personal field: " . $f;
                    if ($failedStep > 1) {
                        $failedStep = 1;
                    }
                }
            }

            $academicFields = ['class_year', 'percentage', 'college_name', 'board_university'];
            foreach ($academicFields as $f) {
                if (empty($fullApp[$f])) {
                    $errors[] = "Missing academic field: " . $f;
                    if ($failedStep > 2) {
                        $failedStep = 2;
                    }
                }
            }

            if ($type === 'scholarship') {
                $schFields = ['current_class', 'current_college', 'bank_name', 'account_number', 'ifsc_code', 'account_holder_name', 'family_income'];
                foreach ($schFields as $f) {
                    if (empty($fullApp[$f])) {
                        $errors[] = "Missing scholarship field: " . $f;
                        if ($failedStep > 2) {
                            $failedStep = 2;
                        }
                    }
                }
            } else {
                $pratFields = ['achievement_title'];
                foreach ($pratFields as $f) {
                    if (empty($fullApp[$f])) {
                        $errors[] = "Missing Pratibha field: " . $f;
                        if ($failedStep > 2) {
                            $failedStep = 2;
                        }
                    }
                }
            }

            $docs = $appModel->documents($appId);
            $uploadedTypes = array_column($docs, 'document_type');
            $requiredDocs = ($type === 'scholarship') 
                ? ['Photo', 'Signature', 'Marksheet', 'Passbook']
                : ['Photo', 'Signature', 'Marksheet', 'Certificate'];

            foreach ($requiredDocs as $req) {
                if (!in_array($req, $uploadedTypes, true)) {
                    $errors[] = "Missing document: " . $req;
                    if ($failedStep > 3) {
                        $failedStep = 3;
                    }
                }
            }

            if (!empty($errors)) {
                Logger::warning("Application {$appId} final submit blocked: incomplete data on step {$failedStep}.", ['errors' => $errors]);
                Flash::set('error', "आवेदन में कुछ जानकारी अधूरी है। कृपया इस चरण को पूरा करें। / Step {$failedStep} contains incomplete information. Please complete it.");
                Response::redirect($redirectUrl . '?step=' . $failedStep);
            }

            $db = \App\Core\Database::getInstance();
            $db->beginTransaction();

            try {
                // Try old schema first (status_id), fall back to new (status VARCHAR)
                $useOldSchema = true;
                try {
                    $stmt = $db->prepare("SELECT status_id, application_no, correction_count FROM applications WHERE id = ? FOR UPDATE");
                    $stmt->execute([$appId]);
                    $currentApp = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $currentStatusId = (int) $currentApp['status_id'];
                } catch (\PDOException $e) {
                    // status_id column doesn't exist — use new schema
                    $useOldSchema = false;
                    $stmt = $db->prepare("SELECT status, application_no, correction_count FROM applications WHERE id = ? FOR UPDATE");
                    $stmt->execute([$appId]);
                    $currentApp = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $currentStatusId = $currentApp['status'] === 'pending_correction' ? 6 : ($currentApp['status'] === 'draft' ? 1 : 2);
                }

                $isResubmit = ($currentStatusId === 6); 
                $finalStatusId = $isResubmit ? 7 : 2; 
                $finalStatusName = $isResubmit ? 'Resubmitted' : 'Submitted';

                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;

                $appNo = $currentApp['application_no'];
                if (empty($appNo)) {
                    $appNo = \App\Core\ApplicationNumberGenerator::format($appId, date('Y'));
                }

                $updateData = [
                    'application_no'   => $appNo,
                    'self_declared'    => 1,
                    'self_declared_at' => date('Y-m-d H:i:s'),
                    'self_declared_ip' => $ipAddress,
                    'dispute_message'  => null, 
                ];
                // Status update: use whichever column exists
                if ($useOldSchema) {
                    $updateData['status_id'] = $finalStatusId;
                } else {
                    $updateData['status'] = $isResubmit ? 'resubmitted' : 'submitted';
                }

                if ($isResubmit) {
                    $updateData['resubmitted_at'] = date('Y-m-d H:i:s');
                } else {
                    $updateData['submitted_at'] = date('Y-m-d H:i:s');
                    $updateData['submitted_ip'] = $ipAddress;
                }

                $appModel->update($appId, $updateData);

                $actionType = $isResubmit ? 'resubmitted' : 'final_submitted';
                $appModel->logHistory($appId, $actionType, (int) Auth::id());

                $db->commit();
            } catch (\Throwable $e) {
                $db->rollBack();
                Logger::error("Final submit transaction failed: " . $e->getMessage());
                Flash::set('error', 'आवेदन जमा करने में असमर्थ। कृपया पुनः प्रयास करें। / Failed to submit application. Please try again.');
                Response::redirect($redirectUrl . '?step=4');
            }

            $studentEmail = $fullApp['email'] ?: Auth::userEmail();
            $studentName = $fullApp['first_name'] . ' ' . $fullApp['last_name'];
            $appTypeName = ($type === 'scholarship') ? 'Scholarship' : 'Pratibha Samman';

            $studentSubject = "Application Submitted Successfully — Tamboli Samaj Portal";
            $studentBody = "
                <h3>नमस्ते {$studentName},</h3>
                <p>आपका {$appTypeName} आवेदन सफलतापूर्वक जमा कर दिया गया है।</p>
                <p><strong>आवेदन संदर्भ संख्या (Application No):</strong> {$appNo}</p>
                <p><strong>स्थिति (Status):</strong> {$finalStatusName}</p>
                <p>आप अपने डैशबोर्ड पर लॉग इन करके अपने आवेदन की स्थिति को ट्रैक कर सकते हैं।</p>
                <br>
                <p>धन्यवाद,</p>
                <p>तम्बोली समाज विकास संस्था</p>
            ";
            \App\Core\Mailer::send($studentEmail, $studentSubject, $studentBody);

            $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'contact_email' LIMIT 1");
            $adminEmail = $stmt->fetchColumn() ?: 'admin@tamoli.org';
            $adminSubject = "New Application Submitted: {$appNo}";
            $adminBody = "
                <h3>नया आवेदन प्राप्त हुआ (New Application Received)</h3>
                <p><strong>आवेदन प्रकार (Type):</strong> {$appTypeName}</p>
                <p><strong>आवेदन संख्या (No):</strong> {$appNo}</p>
                <p><strong>छात्र का नाम (Student):</strong> {$studentName}</p>
                <p>कृपया आवेदन की समीक्षा करने के लिए एडमिन डैशबोर्ड में लॉग इन करें।</p>
            ";
            \App\Core\Mailer::send($adminEmail, $adminSubject, $adminBody);

            \App\Core\Session::remove('draft_id');

            Flash::set('success', 'आवेदन सफलतापूर्वक सबमिट कर दिया गया है! / Application submitted successfully!');
            Response::redirect("/dashboard/applications/{$appId}/acknowledgment");
        }
    }

    private function getFirstError($v): string
    {
        $errors = $v->errors();
        if (!empty($errors)) {
            $firstField = array_key_first($errors);
            if (!empty($errors[$firstField])) {
                return $errors[$firstField][0] ?? 'Validation failed';
            }
        }
        return 'Validation failed';
    }

    public function acknowledgment(int $id): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $appModel = new Application();
        $app = $appModel->find($id);

        if (!$app || (int) $app['student_id'] !== (int) Auth::id()) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/dashboard/applications');
        }

        if ($app['submitted_at'] === null) {
            Flash::set('error', 'Please submit the application first.');
            Response::redirect('/dashboard/applications');
        }

        Response::view('applications/acknowledgment', [
            'title'       => 'Acknowledgment — Tamboli Samaj Portal',
            'application' => $app,
        ]);
    }

    public function show(int $id): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $appModel = new Application();
        $app = $appModel->find($id);

        if (!$app || (int) $app['student_id'] !== (int) Auth::id()) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/dashboard/applications');
        }

        Response::view('applications/show', [
            'title'       => 'Application #' . $id . ' — Tamboli Samaj Portal',
            'application' => $app,
        ]);
    }

    public function resubmit(int $id): void
    {
        Response::redirect("/dashboard/applications/{$id}/edit");
    }

    public function edit(string $id): void
    {
        if (!Auth::isStudent()) {
            Response::redirect('/login');
        }

        $appModel = new Application();
        $app = $appModel->find((int) $id);

        if (!$app || (int) $app['student_id'] !== (int) Auth::id()) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/dashboard/applications');
        }

        $statusName = $app['status_name'] ?? '';
        
        if (in_array($statusName, ['Submitted', 'Under Review', 'Approved', 'Resubmitted'], true)) {
            Flash::set('error', 'Submitted applications cannot be edited.');
            Response::redirect('/dashboard/applications/' . $id);
        }

        if ($statusName === 'Rejected') {
            $deadline = $app['correction_deadline'] ? strtotime($app['correction_deadline']) : 0;
            $count = (int) ($app['correction_count'] ?? 0);
            
            if ($count > 1 || $deadline < time()) {
                Flash::set('error', 'Correction deadline has passed or limit exceeded.');
                Response::redirect('/dashboard/applications/' . $id);
            }

            // Transition to Pending Correction (support both old & new schema)
            try {
                $appModel->update((int) $id, ['status_id' => 6]);
            } catch (\InvalidArgumentException $e) {
                // status_id column likely dropped by update_schema.php — use VARCHAR status instead
                $appModel->update((int) $id, ['status' => 'pending_correction']);
            }
            $appModel->logHistory((int) $id, 'edited', (int) Auth::id());
        }

        \App\Core\Session::set('draft_id', $app['id']);
        $url = ($app['type'] === 'scholarship') ? '/dashboard/applications/scholarship' : '/dashboard/applications/pratibha';
        Response::redirect($url . '?step=1');
    }

    public function update(string $id): void
    {
        Response::redirect("/dashboard/applications/{$id}/edit");
    }


    /**
     * AJAX Document Upload.
     */
    public function uploadDocumentAjax(string $id): void
    {
        header('Content-Type: application/json');

        try {
            if (!Auth::check()) {
                echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
                exit;
            }

            if (!\App\Core\Csrf::validate()) {
                echo json_encode(['success' => false, 'error' => 'Invalid security token.']);
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
                \App\Core\Session::set('profile_photo', $profilePhotoPath);
            }

            echo json_encode([
                'success' => true,
                'stored_name' => $storedName,
                'original_name' => $file['name'],
                'url' => '/uploads/applications/' . $id . '/' . $storedName
            ]);
            exit;
        } catch (\Throwable $e) {
            Logger::error('AJAX Document upload error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * AJAX Document Deletion.
     */
    public function deleteDocumentAjax(string $id): void
    {
        header('Content-Type: application/json');

        try {
            if (!Auth::check()) {
                echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
                exit;
            }

            if (!\App\Core\Csrf::validate()) {
                echo json_encode(['success' => false, 'error' => 'Invalid security token.']);
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
                \App\Core\Session::remove('profile_photo');
            }

            echo json_encode(['success' => true]);
            exit;
        } catch (\Throwable $e) {
            Logger::error('AJAX Document delete error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
            exit;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Input;
use App\Core\Response;
use App\Models\Application;

class ApplicationController
{
    /**
     * List all applications.
     */
    public function index(): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $appModel = new Application();
        $applications = $appModel->all();

        Response::view('admin/applications/index', [
            'title'        => 'Applications — Admin Dashboard',
            'applications' => $applications,
        ]);
    }

    /**
     * View a single application detail.
     */
    public function show(int $id): void
    {
        if (!Auth::isAdmin()) {
            Flash::set('error', 'Access denied.');
            Response::redirect('/');
        }

        $appModel = new Application();
        $app = $appModel->find($id);

        if (!$app) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/admin/applications');
        }

        // Automatic transition from Submitted or Resubmitted to Under Review
        $statusName = $app['status_name'] ?? '';
        if ($statusName === 'Submitted' || $statusName === 'Resubmitted') {
            $appModel->updateStatus($id, 3, (int) Auth::id()); // 3 = Under Review
            $appModel->logHistory($id, 'under_review', (int) Auth::id());
            // Reload application data with the updated status
            $app = $appModel->find($id);
        }

        // Expose completeness check
        $isIncomplete = false;
        $missing = [];
        
        $personalFields = ['first_name', 'last_name', 'father_name', 'mother_name', 'dob', 'gender', 'address', 'city', 'district', 'pincode', 'family_occupation', 'family_members_count', 'earning_members_count', 'career_goal'];
        foreach ($personalFields as $f) {
            if (empty($app[$f])) {
                $missing[] = "Personal: " . $f;
            }
        }

        $academicFields = ['class_year', 'percentage', 'college_name', 'board_university'];
        foreach ($academicFields as $f) {
            if (empty($app[$f])) {
                $missing[] = "Academic: " . $f;
            }
        }

        if (($app['type'] ?? '') === 'scholarship') {
            $schFields = ['current_class', 'current_college', 'bank_name', 'account_number', 'ifsc_code', 'account_holder_name', 'family_income'];
            foreach ($schFields as $f) {
                if (empty($app[$f])) {
                    $missing[] = "Scholarship: " . $f;
                }
            }
        } else {
            if (empty($app['achievement_title'])) {
                $missing[] = "Pratibha: achievement_title";
            }
        }

        $uploadedTypes = array_column($app['documents'] ?? [], 'document_type');
        $requiredDocs = (($app['type'] ?? '') === 'scholarship') 
            ? ['Photo', 'Signature', 'Marksheet', 'Passbook']
            : ['Photo', 'Signature', 'Marksheet', 'Certificate'];

        foreach ($requiredDocs as $req) {
            if (!in_array($req, $uploadedTypes, true)) {
                $missing[] = "Document: " . $req;
            }
        }

        if (!empty($missing)) {
            $isIncomplete = true;
        }

        Response::view('admin/applications/show', [
            'title'        => 'Review Application — Admin Dashboard',
            'application'  => $app,
            'isIncomplete' => $isIncomplete,
            'missingData'  => $missing,
        ]);
    }

    /**
     * Approve an application.
     */
    public function approve(int $id): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/admin/applications/' . $id);
        }

        $appModel = new Application();
        $app = $appModel->find($id);

        if (!$app) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/admin/applications');
        }

        $db = \App\Core\Database::getInstance();
        $db->beginTransaction();

        try {
            // Lock the row to prevent race conditions
            $lockStmt = $db->prepare("SELECT id, status_id FROM applications WHERE id = ? FOR UPDATE");
            $lockStmt->execute([$id]);
            $lockedApp = $lockStmt->fetch(\PDO::FETCH_ASSOC);
            if (!$lockedApp) {
                $db->rollBack();
                Flash::set('error', 'Application not found.');
                Response::redirect('/admin/applications');
            }

            // Transition status to Approved (4)
            $appModel->updateStatus($id, 4, (int) Auth::id());
            $appModel->logHistory($id, 'approved', (int) Auth::id());
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            Flash::set('error', 'Failed to approve application.');
            Response::redirect('/admin/applications/' . $id);
        }

        // Send email notification to Student
        $studentEmail = $app['email'];
        $studentName = $app['first_name'] . ' ' . $app['last_name'];
        $appTypeName = ($app['type'] === 'scholarship') ? 'Scholarship' : 'Pratibha Samman';

        $subject = "Application Approved! — Tamboli Samaj Portal";
        $body = "
            <h3>नमस्ते {$studentName},</h3>
            <p>बधाई हो! आपका {$appTypeName} आवेदन स्वीकार कर लिया गया है।</p>
            <p><strong>आवेदन संदर्भ संख्या (Application No):</strong> {$app['application_no']}</p>
            <p>आप अपने पोर्टल पर लॉग इन करके अपना पावती पत्र या प्रमाण पत्र देख सकते हैं।</p>
            <br>
            <p>धन्यवाद,</p>
            <p>तम्बोली समाज विकास संस्था</p>
        ";
        \App\Core\Mailer::send($studentEmail, $subject, $body);

        Flash::set('success', 'Application approved.');
        Response::redirect('/admin/applications');
    }

    /**
     * Reject an application with correction deadline.
     */
    public function reject(int $id): void
    {
        if (!Auth::isAdmin()) {
            Response::redirect('/');
        }

        if (!Csrf::validate()) {
            Flash::set('error', 'Invalid security token.');
            Response::redirect('/admin/applications/' . $id);
        }

        $reason = Input::post('rejection_reason') ?: Input::post('dispute_message') ?: '';

        if (trim($reason) === '') {
            Flash::set('error', 'Please provide a reason for rejection/correction.');
            Response::redirect('/admin/applications/' . $id);
        }

        $appModel = new Application();
        $app = $appModel->find($id);

        if (!$app) {
            Flash::set('error', 'Application not found.');
            Response::redirect('/admin/applications');
        }

        $db = \App\Core\Database::getInstance();
        $db->beginTransaction();

        try {
            // Lock the row to prevent race conditions
            $lockStmt = $db->prepare("SELECT id, status_id FROM applications WHERE id = ? FOR UPDATE");
            $lockStmt->execute([$id]);
            $lockedApp = $lockStmt->fetch(\PDO::FETCH_ASSOC);
            if (!$lockedApp) {
                $db->rollBack();
                Flash::set('error', 'Application not found.');
                Response::redirect('/admin/applications');
            }

            $newCount = (int) ($app['correction_count'] ?? 0) + 1;
            $deadline = date('Y-m-d H:i:s', strtotime('+7 days'));

            // Transition status to Rejected (5)
            $appModel->update($id, [
                'status_id'           => 5,
                'reviewed_by'         => (int) Auth::id(),
                'dispute_message'     => $reason,
                'correction_count'    => $newCount,
                'correction_deadline' => $deadline,
            ]);

            $appModel->logHistory($id, 'rejected', (int) Auth::id(), null, ['reason' => $reason, 'deadline' => $deadline]);
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            Flash::set('error', 'Failed to reject application.');
            Response::redirect('/admin/applications/' . $id);
        }

        // Send email notification to Student
        $studentEmail = $app['email'];
        $studentName = $app['first_name'] . ' ' . $app['last_name'];
        $appTypeName = ($app['type'] === 'scholarship') ? 'Scholarship' : 'Pratibha Samman';

        $subject = "Application Correction Required — Tamboli Samaj Portal";
        $body = "
            <h3>नमस्ते {$studentName},</h3>
            <p>आपके {$appTypeName} आवेदन की समीक्षा की गई है और इसमें कुछ सुधार की आवश्यकता है।</p>
            <p><strong>कारण (Reason):</strong> " . htmlspecialchars($reason) . "</p>
            <p><strong>सुधार की अंतिम तिथि (Deadline):</strong> " . date('d M Y, h:i A', strtotime($deadline)) . "</p>
            <p>कृपया एडमिन द्वारा बताई गई त्रुटियों को सुधारने के लिए अपने पोर्टल पर लॉग इन करें और आवश्यक दस्तावेज या जानकारी को अपडेट करें।</p>
            <br>
            <p>धन्यवाद,</p>
            <p>तम्बोली समाज विकास संस्था</p>
        ";
        \App\Core\Mailer::send($studentEmail, $subject, $body);

        Flash::set('success', 'Application rejected. Correction window opened.');
        Response::redirect('/admin/applications');
    }

    /**
     * Mark as disputed (mapped to reject in the new system).
     */
    public function dispute(int $id): void
    {
        $this->reject($id);
    }
}

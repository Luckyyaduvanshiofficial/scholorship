<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Logger;
use App\Core\Response;
use App\Models\Application;

/**
 * Secure file serving for application documents (stored outside public docroot).
 */
class UploadController
{
    public function applicationDocument(string $id, string $file): void
    {
        $applicationId = (int) $id;
        $file          = basename($file);

        if ($applicationId <= 0 || $file === '') {
            Response::abort(404);
        }

        if (!Auth::check()) {
            Response::abort(401);
        }

        $applicationModel = new Application();
        $application      = $applicationModel->find($applicationId);

        if (!$application) {
            Response::abort(404);
        }

        $canAccess = Auth::isAdmin()
            || Auth::isRepresentative()
            || ((int) ($application['student_id'] ?? 0) === (int) Auth::id() && Auth::isStudent());

        if (!$canAccess) {
            Response::abort(403);
        }

        $documents = $applicationModel->documents($applicationId);
        $storedMatch = false;
        foreach ($documents as $doc) {
            if (($doc['stored_name'] ?? '') === $file) {
                $storedMatch = true;
                break;
            }
        }

        if (!$storedMatch) {
            Response::abort(404);
        }

        $path = UPLOAD_PATH . '/applications/' . $applicationId . '/' . $file;

        if (!is_file($path)) {
            Response::abort(404);
        }

        $this->streamFile($path, $file);
    }

    private function streamFile(string $path, string $downloadName): void
    {
        $mime = mime_content_type($path) ?: 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string) filesize($path));
        header('Content-Disposition: inline; filename="' . rawurlencode($downloadName) . '"');
        header('X-Content-Type-Options: nosniff');

        readfile($path);
        exit;
    }
}
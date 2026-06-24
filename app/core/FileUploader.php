<?php

declare(strict_types=1);

namespace App\Core;

class FileUploader
{
    private const ALLOWED_EXTENSIONS = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'pdf'  => 'application/pdf',
    ];

    private const MAX_SIZE_IMAGE = 2 * 1024 * 1024;  // 2 MB
    private const MAX_SIZE_PDF   = 5 * 1024 * 1024;  // 5 MB

    private array $errors = [];

    /**
     * Validate an uploaded file. Returns true if valid, false otherwise.
     */
    public function validate(array $file): bool
    {
        $this->errors = [];

        if (!isset($file['error']) || is_array($file['error'])) {
            $this->errors[] = 'Invalid file upload.';

            return false;
        }

        // Check upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = match ($file['error']) {
                UPLOAD_ERR_INI_SIZE,
                UPLOAD_ERR_FORM_SIZE   => 'File exceeds the maximum allowed size.',
                UPLOAD_ERR_PARTIAL     => 'File was only partially uploaded.',
                UPLOAD_ERR_NO_FILE     => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR  => 'Missing temporary folder.',
                UPLOAD_ERR_CANT_WRITE  => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION   => 'File upload stopped by extension.',
                default                => 'Unknown upload error.',
            };

            return false;
        }

        // Check size
        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $maxSize   = ($extension === 'pdf') ? self::MAX_SIZE_PDF : self::MAX_SIZE_IMAGE;

        if ($file['size'] > $maxSize) {
            $maxHuman = Helpers::formatBytes($maxSize);
            $this->errors[] = "File size exceeds the maximum of {$maxHuman}.";

            return false;
        }

        // Check extension
        if (!array_key_exists($extension, self::ALLOWED_EXTENSIONS)) {
            $this->errors[] = 'File type not allowed. Only JPG, PNG, and PDF are accepted.';

            return false;
        }

        // Check MIME type
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $expected = self::ALLOWED_EXTENSIONS[$extension];

        if ($mimeType !== $expected) {
            $this->errors[] = 'File content does not match the expected file type.';

            return false;
        }

        return true;
    }

    /**
     * Move the uploaded file to the destination directory.
     * Generates a unique stored filename. Returns the stored filename.
     */
    public function upload(array $file, string $directory): string
    {
        if (!is_dir($directory)) {
            @mkdir($directory, 0755, true);
        }

        $extension    = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $storedName   = time() . '_' . Helpers::random(8) . '.' . $extension;
        $destination  = rtrim($directory, '/') . '/' . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException("Failed to move uploaded file to {$destination}");
        }

        return $storedName;
    }

    /**
     * Get validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error message.
     */
    public function firstError(): string
    {
        return $this->errors[0] ?? 'Unknown upload error.';
    }
}

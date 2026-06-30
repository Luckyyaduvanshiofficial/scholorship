<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    /**
     * Generate or retrieve the current CSRF token.
     */
    public static function token(): string
    {
        Session::start();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = Helpers::random(64);
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Generate a hidden <input> field containing the CSRF token.
     * Usage in forms: <?= Csrf::field() ?>
     */
    public static function field(): string
    {
        return sprintf(
            '<input type="hidden" name="csrf_token" value="%s">',
            Helpers::esc(self::token())
        );
    }

    /**
     * Validate the CSRF token from POST data.
     * Returns true if valid, false otherwise.
     */
    public static function validate(?string $token = null): bool
    {
        $token ??= $_POST['csrf_token'] ?? '';

        if ($token === '') {
            Logger::warning('CSRF token missing in request');

            return false;
        }

        $stored = self::token();

        if (!hash_equals($stored, $token)) {
            Logger::warning('CSRF token mismatch');

            return false;
        }

        return true;
    }

    /**
     * Validate CSRF or abort with flash message and redirect.
     */
    public static function validateOrAbort(string $redirectTo = ''): void
    {
        if (self::validate()) {
            return;
        }

        Flash::set('error', 'Invalid security token. Please try again.');

        if ($redirectTo !== '') {
            Response::redirect($redirectTo);
        }

        Response::back();
    }
}

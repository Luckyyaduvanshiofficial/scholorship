<?php

declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * Send an email using SMTP configured in the environment.
     * Returns true on success, false on failure.
     */
    public static function send(string $toEmail, string $subject, string $htmlBody, string $altBody = ''): bool
    {
        $host = getenv('SMTP_HOST') ?: '';
        $user = getenv('SMTP_USER') ?: '';
        $pass = getenv('SMTP_PASS') ?: '';
        
        $isDebug = (getenv('APP_DEBUG') === 'true');
        $hasSmtp = (!empty($host) && !empty($user) && !empty($pass));

        // Always log locally so developer/user can verify the reset token on local machines without active SMTP
        Logger::info("Sending email to {$toEmail}", [
            'subject' => $subject,
            'body' => ($isDebug || !$hasSmtp) ? $htmlBody : '[REDACTED IN PRODUCTION]'
        ]);

        // Write directly to a temporary text log file for easy local development verification
        if ($isDebug || !$hasSmtp) {
            $resetLogFile = ROOT_PATH . '/storage/logs/mail_resets.log';
            $logDir = dirname($resetLogFile);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            $logContent = "[" . date('Y-m-d H:i:s') . "] TO: {$toEmail}\nSUBJECT: {$subject}\nBODY: {$htmlBody}\n----------------------------------------\n";
            file_put_contents($resetLogFile, $logContent, FILE_APPEND);
        }

        // If no SMTP credentials configured at all, log only and return mock success
        if (empty($host) || empty($user) || empty($pass)) {
            Logger::info("SMTP credentials not configured. Email logged to local file: {$resetLogFile}");
            return true; // Return true as mock success for development
        }

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $user;
            $mail->Password   = $pass;
            
            // Encryption
            $port = (int)(getenv('SMTP_PORT') ?: 465);
            $mail->Port = $port;
            if ($port === 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($port === 587) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            // Recipients
            $fromEmail = getenv('SMTP_FROM_EMAIL') ?: $user;
            $fromName  = getenv('SMTP_FROM_NAME') ?: 'Tamboli Samaj Portal';
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $altBody ?: strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (Exception $e) {
            Logger::error("Mailer Error: " . $mail->ErrorInfo);
            return false;
        } catch (\Throwable $t) {
            Logger::error("Mailer Exception: " . $t->getMessage());
            return false;
        }
    }
}

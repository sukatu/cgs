<?php
/**
 * Helper to send Zoom meeting link email via SMTP.
 * Requires: composer autoload (PHPMailer), email-config.php
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!defined('EMAIL_CONFIG_LOADED')) {
    if (file_exists(__DIR__ . '/email-config.php')) {
        require_once __DIR__ . '/email-config.php';
    } else {
        define('EMAIL_CONFIG_LOADED', true);
        define('EMAIL_HOST', '');
        define('EMAIL_SMTP_PORT', 465);
        define('EMAIL_USERNAME', '');
        define('EMAIL_PASSWORD', '');
        define('EMAIL_FROM_ADDRESS', '');
        define('EMAIL_FROM_NAME', 'CGS');
        define('EMAIL_SMTP_SECURE', 'ssl');
    }
}

/**
 * Send Zoom meeting link to a registrant.
 * @param string $toEmail Recipient email
 * @param string $toName Recipient name
 * @param string $eventTitle Event title
 * @param string $eventDate Formatted event date/time
 * @return array ['success' => bool, 'message' => string]
 */
function sendZoomLinkEmail($toEmail, $toName, $eventTitle = 'Corporate Governance as a tool in ensuring effective AML/CFT Regulatory Compliance', $eventDate = 'Mar 19, 2026 03:00 PM London') {
    $zoomLink = 'https://us06web.zoom.us/j/86995408931?pwd=F9a59pGIoE9Kez4dtmrOhgNy7bs1Ka.1';
    $meetingId = '869 9540 8931';
    $passcode = '038545';
    $joinInstructions = 'https://us06web.zoom.us/meetings/86995408931/invitations?signature=WoTv2yAvWEe0kX1YTmxBipUTYWNPFTaHNn84UZ4C0AI';

    $subject = 'Your Zoom Link: ' . $eventTitle;
    $bodyHtml = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333;} .box{background:#f5f5f5;padding:1rem;border-radius:8px;margin:1rem 0;} a.btn{display:inline-block;background:#B8860B;color:#0A2463;padding:12px 24px;text-decoration:none;border-radius:4px;font-weight:600;margin:1rem 0;} h1{color:#0A2463;}</style></head><body>';
    $bodyHtml .= '<h1>CGS – Corporate Governance Series</h1>';
    $bodyHtml .= '<p>Hello ' . htmlspecialchars($toName) . ',</p>';
    $bodyHtml .= '<p>Thank you for registering for <strong>' . htmlspecialchars($eventTitle) . '</strong>.</p>';
    $bodyHtml .= '<p><strong>Date &amp; Time:</strong> ' . htmlspecialchars($eventDate) . '</p>';
    $bodyHtml .= '<div class="box"><strong>Join Zoom Meeting</strong><br>Meeting ID: ' . htmlspecialchars($meetingId) . '<br>Passcode: ' . htmlspecialchars($passcode) . '</div>';
    $bodyHtml .= '<p><a href="' . htmlspecialchars($zoomLink) . '" class="btn">Join Zoom Meeting →</a></p>';
    $bodyHtml .= '<p><a href="' . htmlspecialchars($joinInstructions) . '">View detailed join instructions</a></p>';
    $bodyHtml .= '<p>We look forward to seeing you online.</p>';
    $bodyHtml .= '<p>— CGS Team</p></body></html>';

    try {
        if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
            return ['success' => false, 'message' => 'PHPMailer not installed. Run: composer install'];
        }
        require __DIR__ . '/vendor/autoload.php';

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = EMAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = EMAIL_USERNAME;
        $mail->Password   = EMAIL_PASSWORD;
        $mail->SMTPSecure = defined('EMAIL_SMTP_SECURE') ? constant('EMAIL_SMTP_SECURE') : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = EMAIL_SMTP_PORT;
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];
        $mail->setFrom(EMAIL_FROM_ADDRESS, defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : 'CGS');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHtml;
        $mail->AltBody = "Hello $toName,\n\nThank you for registering. Join the Zoom meeting: $zoomLink\nMeeting ID: $meetingId\nPasscode: $passcode\n\n— CGS Team";

        $mail->send();
        return ['success' => true, 'message' => 'Email sent'];
    } catch (Exception $e) {
        $err = isset($mail) ? $mail->ErrorInfo : $e->getMessage();
        error_log('Zoom email error: ' . $err);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

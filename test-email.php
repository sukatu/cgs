<?php
/**
 * Test email (SMTP) sending. Run once to verify Zoom emails will work.
 * Usage: https://cgs.cstsghana.com/test-email.php
 * Remove or protect this file in production.
 */
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$testEmail = isset($_GET['to']) ? trim($_GET['to']) : '';
$result = ['success' => false, 'message' => '', 'detail' => ''];

if (!file_exists(__DIR__ . '/email-config.php')) {
    $result['message'] = 'email-config.php not found. Copy from email-config.example.php and set SMTP credentials.';
} elseif (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    $result['message'] = 'Composer vendor folder not found. Run: composer install';
} else {
    require_once __DIR__ . '/email-config.php';
    require_once __DIR__ . '/vendor/autoload.php';

    $to = $testEmail ?: EMAIL_FROM_ADDRESS;
    if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $result['message'] = 'Add ?to=your@email.com to the URL to send a test email, or set a valid FROM in email-config.php.';
    } else {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $debug = [];
            $mail->Debugoutput = function ($str) use (&$debug) {
                $debug[] = $str;
            };
            $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_CLIENT;
            $mail->isSMTP();
            $mail->Host       = EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = EMAIL_USERNAME;
            $mail->Password   = EMAIL_PASSWORD;
            $mail->SMTPSecure = defined('EMAIL_SMTP_SECURE') ? constant('EMAIL_SMTP_SECURE') : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
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
            $mail->addAddress($to);
            $mail->Subject = 'CGS Test Email – Zoom link test';
            $mail->Body    = '<p>If you receive this, SMTP is working. Zoom meeting links will be sent to registrants.</p>';
            $mail->AltBody = 'If you receive this, SMTP is working.';
            $mail->send();
            $result['success'] = true;
            $result['message'] = 'Test email sent to ' . htmlspecialchars($to) . '. Check inbox and spam.';
            $result['detail'] = implode("\n", $debug);
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $result['message'] = 'Send failed: ' . $e->getMessage();
            $result['detail'] = isset($mail) ? $mail->ErrorInfo : '';
            if (!empty($debug)) {
                $result['detail'] .= "\n\nDebug:\n" . implode("\n", $debug);
            }
        }
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Test – CGS</title>
    <style>body{font-family:sans-serif;max-width:600px;margin:2rem auto;padding:1rem;} .ok{color:green;} .err{color:#c00;} pre{background:#f5f5f5;padding:1rem;overflow:auto;font-size:12px;}</style>
</head>
<body>
    <h1>Email (SMTP) test</h1>
    <?php if ($result['success']): ?>
        <p class="ok"><?php echo htmlspecialchars($result['message']); ?></p>
    <?php else: ?>
        <p class="err"><?php echo htmlspecialchars($result['message']); ?></p>
    <?php endif; ?>
    <?php if (!empty($result['detail'])): ?>
        <h2>Details</h2>
        <pre><?php echo htmlspecialchars($result['detail']); ?></pre>
    <?php endif; ?>
    <p><small>Use <code>?to=your@email.com</code> to send a test to that address. Remove this file (test-email.php) when done.</small></p>
</body>
</html>

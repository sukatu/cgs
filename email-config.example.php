<?php
/**
 * Copy this file to email-config.php and set your SMTP credentials.
 * Used to send Zoom meeting links and notifications.
 * Do not commit email-config.php with real credentials to public repos.
 */
if (!defined('EMAIL_CONFIG_LOADED')) {
    define('EMAIL_CONFIG_LOADED', true);
    define('EMAIL_HOST', 'admin.marryrightgh.com');
    define('EMAIL_SMTP_PORT', 465);
    define('EMAIL_USERNAME', 'cgs@admin.marryrightgh.com');
    define('EMAIL_PASSWORD', 'your-password');
    define('EMAIL_FROM_ADDRESS', 'cgs@admin.marryrightgh.com');
    define('EMAIL_FROM_NAME', 'CGS - Corporate Governance Series');
    define('EMAIL_SMTP_SECURE', 'ssl'); // ssl for port 465
}

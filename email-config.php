<?php
/**
 * Email (SMTP) configuration for CGS.
 * Used to send Zoom meeting links and notifications.
 * Keep this file secure and do not commit real credentials to public repos.
 */
if (!defined('EMAIL_CONFIG_LOADED')) {
    define('EMAIL_CONFIG_LOADED', true);
    define('EMAIL_HOST', 'cgs.cstsghana.com');
    define('EMAIL_SMTP_PORT', 465);
    define('EMAIL_USERNAME', 'admin@cgs.cstsghana.com');
    define('EMAIL_PASSWORD', 'efu;7DWd5QGa(fi9');
    define('EMAIL_FROM_ADDRESS', 'admin@cgs.cstsghana.com');
    define('EMAIL_FROM_NAME', 'CGS - Corporate Governance Series');
    define('EMAIL_SMTP_SECURE', 'ssl'); // ssl for port 465
}

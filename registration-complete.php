<?php
/**
 * Registration completed / thank-you page.
 * Shown after successful online or in-person registration.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = isset($_SESSION['registration_success']) ? $_SESSION['registration_success'] : '';
$showZoomLink = !empty($_SESSION['registration_show_zoom_link']);
$type = isset($_SESSION['registration_type']) ? $_SESSION['registration_type'] : '';

// Clear session vars so refresh doesn't repeat
unset($_SESSION['registration_success'], $_SESSION['registration_show_zoom_link'], $_SESSION['registration_type']);

$hasMessage = $message !== '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Complete | CGS</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .complete-page { max-width: 700px; margin: 0 auto; padding: 3rem 2rem; text-align: center; }
        .complete-icon { font-size: 4rem; margin-bottom: 1rem; }
        .complete-title { font-size: 2rem; color: var(--primary-navy); margin-bottom: 1.5rem; }
        .complete-message { font-size: 1.1rem; color: var(--text-charcoal); line-height: 1.7; margin-bottom: 2rem; }
        .zoom-box { background: var(--bg-offwhite); padding: 1.5rem; border-radius: 8px; border: 2px solid var(--accent-gold); margin: 2rem 0; text-align: left; }
        .zoom-box h3 { color: var(--primary-navy); margin-bottom: 0.75rem; font-size: 1.1rem; }
        .complete-actions { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; margin-top: 2rem; }
        .complete-actions a { padding: 0.75rem 1.5rem; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .btn-home { background: var(--primary-navy); color: white; }
        .btn-home:hover { background: #081c4f; }
        .btn-register { background: var(--accent-gold); color: var(--primary-navy); }
        .btn-register:hover { background: #d4a017; }
        .btn-zoom-link { display: inline-block; background: var(--accent-gold); color: var(--primary-navy); padding: 1rem 1.5rem; font-weight: 600; border-radius: 4px; text-decoration: none; margin-top: 0.5rem; }
        .btn-zoom-link:hover { background: #d4a017; }
    </style>
</head>
<body>
    <?php include 'header-main.php'; ?>

    <main class="section" style="padding: 4rem 0;">
        <div class="container">
            <div class="complete-page">
                <?php if ($hasMessage): ?>
                    <div class="complete-icon" aria-hidden="true" style="color: var(--accent-gold);">✓</div>
                    <h1 class="complete-title">Registration completed</h1>
                    <p class="complete-message"><?php echo htmlspecialchars($message); ?></p>

                    <?php if ($showZoomLink): ?>
                        <div class="zoom-box">
                            <h3>Your Zoom meeting details (save this)</h3>
                            <p style="margin-bottom: 0.5rem; color: var(--text-charcoal);"><strong>Meeting ID:</strong> 885 0243 0789 &nbsp;|&nbsp; <strong>Passcode:</strong> 822412</p>
                            <a href="https://us06web.zoom.us/j/88502430789?pwd=e3a79VijbjKZTolGnhZDoaN4s7OIug.1" target="_blank" rel="noopener" class="btn-zoom-link">Join Zoom Meeting →</a>
                            <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-light);">We have also sent the link to your email. Please check your inbox and spam folder.</p>
                        </div>
                    <?php endif; ?>

                    <div class="complete-actions">
                        <a href="index.php" class="btn-home">Back to Home</a>
                        <a href="events.php" class="btn-register">View all events</a>
                    </div>
                <?php else: ?>
                    <p class="complete-message">There is no registration in progress. If you just registered, your submission was successful and you can close this page.</p>
                    <div class="complete-actions">
                        <a href="index.php" class="btn-home">Back to Home</a>
                        <a href="register-cgs-ii.php" class="btn-register">Register for CGS II</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CGS</h3>
                    <p>Corporate Governance Series<br>Transforming governance standards across Africa</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About CGS</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="training.php">Training</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="governance-codes.php">Governance Codes</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="media.php">Media</a></li>
                        <li><a href="network.php">Network</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Corporate Governance Series (CGS) by CSTS Ghana. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

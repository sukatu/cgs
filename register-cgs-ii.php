<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for CGS Webinar | Corporate Governance Series</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .registration-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 4rem;
        }
        @media (max-width: 768px) {
            .registration-container {
                padding: 2rem;
            }
        }
        @media (max-width: 600px) {
            .registration-container {
                padding: 1.5rem;
            }
        }
        .registration-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-navy) 0%, #0d2f7a 100%);
            color: white;
            border-radius: 8px;
        }
        .registration-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: white;
        }
        .registration-header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        .registration-form-section {
            display: block;
            background: var(--white);
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-charcoal);
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--divider-grey);
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-navy);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-register {
            background-color: var(--accent-gold);
            color: var(--primary-navy);
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-register:hover {
            background-color: #d4a017;
        }
        .btn-zoom {
            background-color: var(--accent-gold);
            color: var(--primary-navy);
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            border-radius: 4px;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .btn-zoom:hover {
            background-color: #d4a017;
        }
        .event-details {
            background: var(--bg-offwhite);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .event-details h3 {
            color: var(--primary-navy);
            margin-bottom: 1rem;
        }
        .event-details-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-charcoal);
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .registration-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'header-main.php'; ?>

    <main>
        <section class="section" style="padding: 4rem 0;">
            <div class="container">
                <div class="registration-container">
                    <!-- Event Image -->
                    <div style="width: 100%; margin-bottom: 2rem; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow);">
                        <img src="new meeting flyer March 2026.jpeg" alt="CGS Webinar: Corporate Governance as a Tool in Ensuring Effective AML/CFT Regulatory Compliance" style="width: 100%; height: auto; display: block; object-fit: cover;">
                    </div>

                    <!-- Header -->
                    <div class="registration-header">
                        <h1>CGS Webinar Registration</h1>
                        <p>Corporate Governance as a Tool in Ensuring Effective AML/CFT Regulatory Compliance</p>
                    </div>

                    <!-- Event Details -->
                    <div class="event-details">
                        <h3>Event Information</h3>
                        <div class="event-details-item">
                            <span>📅</span>
                            <span><strong>Date:</strong> Thursday, 19th March 2026</span>
                        </div>
                        <div class="event-details-item">
                            <span>🕐</span>
                            <span><strong>Time:</strong> 3:00 PM (London)</span>
                        </div>
                        <div class="event-details-item">
                            <span>💻</span>
                            <span><strong>Format:</strong> Online (Zoom)</span>
                        </div>
                        <div class="event-details-item">
                            <span>📍</span>
                            <span><strong>Location:</strong> Zoom Meeting</span>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if (isset($_SESSION['registration_success'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_SESSION['registration_success']); unset($_SESSION['registration_success']); ?>
                        </div>
                        <?php if (!empty($_SESSION['registration_show_zoom_link'])): unset($_SESSION['registration_show_zoom_link']); ?>
                        <div style="background: var(--bg-offwhite); padding: 1.5rem; border-radius: 8px; margin-top: 1rem; border: 2px solid var(--accent-gold);">
                            <h3 style="color: var(--primary-navy); margin-bottom: 0.75rem; font-size: 1.1rem;">Your Zoom link (save this – use if email doesn’t arrive)</h3>
                            <p style="margin-bottom: 0.5rem; color: var(--text-charcoal);"><strong>Meeting ID:</strong> 869 9540 8931 &nbsp;|&nbsp; <strong>Passcode:</strong> 038545</p>
                            <a href="https://us06web.zoom.us/j/86995408931?pwd=F9a59pGIoE9Kez4dtmrOhgNy7bs1Ka.1" target="_blank" class="btn-zoom" style="display: inline-block; margin-top: 0.5rem;">Join Zoom Meeting →</a>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['registration_error'])): ?>
                        <div class="alert alert-error">
                            <?php echo htmlspecialchars($_SESSION['registration_error']); unset($_SESSION['registration_error']); ?>
                            <?php if (isset($_SESSION['registration_error_detail'])): ?>
                                <p style="margin-top: 0.75rem; font-size: 0.9rem; opacity: 0.9;">Technical detail: <?php echo htmlspecialchars($_SESSION['registration_error_detail']); unset($_SESSION['registration_error_detail']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Online Registration Section -->
                    <div class="registration-form-section" id="section-online">
                        <h2 style="color: var(--primary-navy); margin-bottom: 1rem;">Join via Zoom</h2>
                        <p style="color: var(--text-charcoal); margin-bottom: 1.5rem;">
                            Register below with your name and email. We will save your registration and send the Zoom meeting link to your email so you can join on the day.
                        </p>
                        <form method="POST" action="register-online.php" id="onlineRegistrationForm">
                            <input type="hidden" name="event_id" value="999">
                            <input type="hidden" name="event_title" value="Corporate Governance as a tool in ensuring effective AML/CFT Regulatory Compliance">
                            <input type="hidden" name="event_date" value="Mar 19, 2026 03:00 PM London">
                            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="online_full_name">Full Name *</label>
                                    <input type="text" id="online_full_name" name="full_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="online_email">Email Address *</label>
                                    <input type="email" id="online_email" name="email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="online_phone">Phone (optional)</label>
                                <input type="tel" id="online_phone" name="phone">
                            </div>
                            <button type="submit" class="btn-register">Register &amp; Get Zoom Link by Email</button>
                        </form>
                        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--divider-grey);">
                            <h3 style="color: var(--primary-navy); margin-bottom: 1rem; font-size: 1.1rem;">Zoom Meeting Details</h3>
                            <div class="event-details-item"><strong>Topic:</strong> Corporate Governance as a tool in ensuring effective AML/CFT Regulatory Compliance</div>
                            <div class="event-details-item"><strong>Meeting ID:</strong> 869 9540 8931</div>
                            <div class="event-details-item"><strong>Passcode:</strong> 038545</div>
                            <a href="https://us06web.zoom.us/j/86995408931?pwd=F9a59pGIoE9Kez4dtmrOhgNy7bs1Ka.1" target="_blank" class="btn-zoom" style="margin-top: 1rem;">
                                Join Zoom Meeting →
                            </a>
                            <p style="text-align: center; margin-top: 1rem; color: var(--text-light); font-size: 0.9rem;">
                                <a href="https://us06web.zoom.us/meetings/86995408931/invitations?signature=WoTv2yAvWEe0kX1YTmxBipUTYWNPFTaHNn84UZ4C0AI" target="_blank" style="color: var(--primary-navy);">View detailed join instructions</a>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>
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

    <script src="script.js"></script>
</body>
</html>

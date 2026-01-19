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
    <title>Register for CGS II | Corporate Governance Series</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .registration-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
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
        .registration-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .registration-option {
            background: var(--white);
            border: 3px solid var(--divider-grey);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .registration-option:hover {
            border-color: var(--accent-gold);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .registration-option.active {
            border-color: var(--accent-gold);
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
        }
        .registration-option-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .registration-option h2 {
            font-size: 1.5rem;
            color: var(--primary-navy);
            margin-bottom: 1rem;
        }
        .registration-option p {
            color: var(--text-charcoal);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .registration-form-section {
            display: none;
            background: var(--white);
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
        }
        .registration-form-section.active {
            display: block;
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
            .registration-options {
                grid-template-columns: 1fr;
            }
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
                    <!-- Header -->
                    <div class="registration-header">
                        <h1>CGS II Registration</h1>
                        <p>Bank Corporate Governance and Financial Stability: The Role of Bank Boards</p>
                    </div>

                    <!-- Event Details -->
                    <div class="event-details">
                        <h3>Event Information</h3>
                        <div class="event-details-item">
                            <span>📅</span>
                            <span><strong>Date:</strong> Thursday, February 12, 2026</span>
                        </div>
                        <div class="event-details-item">
                            <span>🕐</span>
                            <span><strong>Time:</strong> 5:00 PM (Africa/Accra / GMT)</span>
                        </div>
                        <div class="event-details-item">
                            <span>💻</span>
                            <span><strong>Format:</strong> Online via Zoom</span>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if (isset($_SESSION['registration_success'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_SESSION['registration_success']); unset($_SESSION['registration_success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['registration_error'])): ?>
                        <div class="alert alert-error">
                            <?php echo htmlspecialchars($_SESSION['registration_error']); unset($_SESSION['registration_error']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Registration Options -->
                    <div class="registration-options">
                        <div class="registration-option active" onclick="selectOption('online')" id="option-online">
                            <div class="registration-option-icon">💻</div>
                            <h2>Online Attendance</h2>
                            <p>Join the event via Zoom from anywhere in the world. Perfect for remote participation.</p>
                        </div>
                        <div class="registration-option" onclick="selectOption('inperson')" id="option-inperson">
                            <div class="registration-option-icon">👥</div>
                            <h2>In-Person Attendance</h2>
                            <p>Register to attend the event in person. Fill out the form below to complete your registration.</p>
                        </div>
                    </div>

                    <!-- Online Registration Section -->
                    <div class="registration-form-section active" id="section-online">
                        <h2 style="color: var(--primary-navy); margin-bottom: 1rem;">Join via Zoom</h2>
                        <p style="color: var(--text-charcoal); margin-bottom: 1.5rem;">
                            Click the button below to register and join the Zoom meeting. You'll be redirected to Zoom where you can complete your registration.
                        </p>
                        <div style="background: var(--bg-offwhite); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <h3 style="color: var(--primary-navy); margin-bottom: 1rem; font-size: 1.1rem;">Zoom Meeting Details</h3>
                            <div class="event-details-item">
                                <strong>Meeting ID:</strong> 885 0243 0789
                            </div>
                            <div class="event-details-item">
                                <strong>Passcode:</strong> 822412
                            </div>
                        </div>
                        <a href="https://us06web.zoom.us/j/88502430789?pwd=e3a79VijbjKZTolGnhZDoaN4s7OIug.1" target="_blank" class="btn-zoom">
                            Register & Join Zoom Meeting →
                        </a>
                        <p style="text-align: center; margin-top: 1rem; color: var(--text-light); font-size: 0.9rem;">
                            <a href="https://us06web.zoom.us/meetings/88502430789/invitations?signature=jv3kLZCqPxnGY0kOXjKJ-j_yX8d2Rbww5hhLcVJeOWA" target="_blank" style="color: var(--primary-navy);">View detailed join instructions</a>
                        </p>
                    </div>

                    <!-- In-Person Registration Section -->
                    <div class="registration-form-section" id="section-inperson">
                        <h2 style="color: var(--primary-navy); margin-bottom: 1rem;">In-Person Registration Form</h2>
                        <p style="color: var(--text-charcoal); margin-bottom: 1.5rem;">
                            Please fill out the form below to register for in-person attendance. All fields are required.
                        </p>
                        <form method="POST" action="register-inperson.php" id="inPersonRegistrationForm">
                            <input type="hidden" name="event_id" value="999">
                            <input type="hidden" name="event_title" value="CGS II Bank Corporate Governance and Financial Stability: The Role of Bank Boards">
                            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="full_name">Full Name *</label>
                                    <input type="text" id="full_name" name="full_name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="institution_firm">Institution/Firm *</label>
                                    <input type="text" id="institution_firm" name="institution_firm" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address *</label>
                                <textarea id="address" name="address" required rows="3"></textarea>
                            </div>
                            
                            <button type="submit" class="btn-register">
                                Submit Registration
                            </button>
                        </form>
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

    <script>
        function selectOption(type) {
            // Update option buttons
            document.querySelectorAll('.registration-option').forEach(opt => {
                opt.classList.remove('active');
            });
            document.getElementById('option-' + type).classList.add('active');
            
            // Update form sections
            document.querySelectorAll('.registration-form-section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById('section-' + type).classList.add('active');
        }
    </script>
    <script src="script.js"></script>
</body>
</html>

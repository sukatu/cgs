<?php
require_once 'config.php';
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
    header('Location: user-dashboard.php');
    exit();
}

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_reset') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $_SESSION['reset_error'] = 'Please enter your email address.';
        header('Location: forgot-password.php');
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['reset_error'] = 'Please enter a valid email address.';
        header('Location: forgot-password.php');
        exit();
    }
    
    try {
        $conn = getDBConnection();
        
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Create password reset table if it doesn't exist
            $createTableSQL = "
            CREATE TABLE IF NOT EXISTS `password_resets` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `email` varchar(255) NOT NULL,
              `token` varchar(100) NOT NULL,
              `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
              `expires_at` datetime NOT NULL,
              `used` tinyint(1) DEFAULT 0,
              PRIMARY KEY (`id`),
              KEY `token` (`token`),
              KEY `user_id` (`user_id`),
              KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
            $conn->query($createTableSQL);
            
            // Generate unique reset token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
            
            // Delete any existing reset tokens for this user
            $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ? AND used = 0");
            $deleteStmt->bind_param("i", $user['id']);
            $deleteStmt->execute();
            $deleteStmt->close();
            
            // Insert new reset token
            $insertStmt = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("isss", $user['id'], $email, $token, $expiresAt);
            
            if ($insertStmt->execute()) {
                $resetId = $insertStmt->insert_id;
                $insertStmt->close();
                
                // Generate reset URL
                $resetUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
                           "://" . $_SERVER['HTTP_HOST'] . 
                           dirname($_SERVER['PHP_SELF']) . 
                           "/reset-password.php?token=" . $token;
                
                // TODO: In production, send email with reset link
                // For now, show the reset link to the user
                $_SESSION['reset_token'] = $token;
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_success'] = 'Password reset link has been generated. Please use the link below to reset your password.';
                $_SESSION['reset_url'] = $resetUrl;
                
                error_log("Password reset requested for email: $email, Token: $token");
            } else {
                $_SESSION['reset_error'] = 'Failed to generate reset token. Please try again.';
                error_log("Failed to insert reset token: " . $conn->error);
            }
            
            $stmt->close();
        } else {
            // For security, don't reveal if email exists
            $_SESSION['reset_success'] = 'If an account exists with that email, a password reset link has been sent.';
            error_log("Password reset requested for non-existent email: $email");
        }
        
        $conn->close();
        
    } catch (Exception $e) {
        $_SESSION['reset_error'] = 'An error occurred. Please try again later.';
        error_log("Password reset error: " . $e->getMessage());
    }
    
    header('Location: forgot-password.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | CGS Network</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-top">
            <div class="nav-top-container">
                <div class="logo">
                    <a href="index.html"><img src="logo-header.png" alt="Corporate Governance Series CGS Logo" class="logo-header"></a>
                </div>
                <div class="nav-utility">
                    <div class="header-search">
                        <input type="text" placeholder="Search..." id="headerSearchInput">
                        <button class="search-btn" id="searchBtn" style="display: none;">Search</button>
                    </div>
                    <a href="login-user.html" class="login-btn">Login</a>
                    <a href="network.php" class="join-btn">Join the Network</a>
                </div>
            </div>
        </div>
        <div class="nav-bottom">
            <div class="nav-bottom-container">
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About CGS</a></li>
                    <li class="dropdown">
                        <a href="events.html">Events</a>
                        <ul class="dropdown-menu">
                            <li><a href="webinar-diary.html">Webinar Diary</a></li>
                            <li><a href="series-diary.html">Series Diary</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="resources.html">Resources</a>
                        <ul class="dropdown-menu">
                            <li><a href="governance-codes.html">Governance Codes in Africa</a></li>
                            <li><a href="blog.html">Blog</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="media.html">Media</a>
                        <ul class="dropdown-menu">
                            <li><a href="videos.html">Videos</a></li>
                            <li><a href="pictures.html">Pictures</a></li>
                        </ul>
                    </li>
                    <li><a href="training.html">Training</a></li>
                    <li><a href="network.php">Network</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <main class="login-main">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Forgot Password?</h1>
                    <p>Enter your email address and we'll send you a link to reset your password.</p>
                </div>
                
                <?php if (isset($_SESSION['reset_error'])): ?>
                    <div style="background-color: #ffebee; color: #d32f2f; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #ffcdd2;">
                        <?php echo htmlspecialchars($_SESSION['reset_error']); unset($_SESSION['reset_error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['reset_success'])): ?>
                    <div style="background-color: #e8f5e9; color: #388e3c; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #c8e6c9;">
                        <?php echo htmlspecialchars($_SESSION['reset_success']); unset($_SESSION['reset_success']); ?>
                        
                        <?php if (isset($_SESSION['reset_url'])): ?>
                            <div style="margin-top: 1rem; padding: 1rem; background: var(--bg-offwhite); border-radius: 4px;">
                                <p style="margin-bottom: 0.5rem; font-weight: 600;">Password Reset Link:</p>
                                <a href="<?php echo htmlspecialchars($_SESSION['reset_url']); ?>" style="color: var(--primary-navy); word-break: break-all; text-decoration: underline;" target="_blank">
                                    <?php echo htmlspecialchars($_SESSION['reset_url']); ?>
                                </a>
                                <p style="margin-top: 0.75rem; font-size: 0.9rem; color: var(--text-charcoal);">
                                    <strong>Note:</strong> This link will expire in 1 hour. Click the link above or copy it to reset your password.
                                </p>
                            </div>
                            <?php unset($_SESSION['reset_url']); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <form class="login-form" method="POST" action="forgot-password.php">
                    <input type="hidden" name="action" value="request_reset">
                    <div class="form-group">
                        <label for="resetEmail">Email Address</label>
                        <input type="email" id="resetEmail" name="email" placeholder="Enter your email address" required value="<?php echo isset($_SESSION['reset_email']) ? htmlspecialchars($_SESSION['reset_email']) : ''; unset($_SESSION['reset_email']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Send Reset Link</button>
                </form>
                
                <div class="login-footer">
                    <p style="text-align: center; margin-top: 1.5rem;">
                        <a href="login-user.html" style="color: var(--primary-navy); text-decoration: none;">← Back to Login</a>
                    </p>
                    <p style="text-align: center; margin-top: 0.5rem;">
                        Don't have an account? <a href="network.php" style="color: var(--primary-navy); text-decoration: none;">Join the Network</a>
                    </p>
                </div>
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
                        <li><a href="index.html">Home</a></li>
                        <li><a href="about.html">About CGS</a></li>
                        <li><a href="events.html">Events</a></li>
                        <li><a href="network.php">Network</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="governance-codes.html">Governance Codes</a></li>
                        <li><a href="blog.html">Blog</a></li>
                        <li><a href="media.html">Media</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Stay Informed</h4>
                    <p>Subscribe to our newsletter for updates on events, resources, and governance insights.</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Corporate Governance Series (CGS) by CSTS Ghana. All rights reserved. | <a href="admin-login.php" style="color: var(--accent-gold);">Admin Login</a></p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-light);">Website by <a href="https://www.dennislaw.com" target="_blank" style="color: var(--accent-gold); font-weight: 600;">ADDENS TECHNOLOGY LIMITED</a> - Makers of DENNISLAW</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>

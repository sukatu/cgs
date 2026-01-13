<?php
require_once 'config.php';
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
    header('Location: user-dashboard.php');
    exit();
}

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$validToken = false;
$userEmail = '';

// Validate token
if (!empty($token)) {
    try {
        $conn = getDBConnection();
        
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
        
        // Check if token exists and is valid
        $stmt = $conn->prepare("
            SELECT pr.*, u.email as user_email 
            FROM password_resets pr 
            JOIN users u ON pr.user_id = u.id 
            WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW()
        ");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $reset = $result->fetch_assoc();
            $validToken = true;
            $userEmail = $reset['user_email'];
        } else {
            $error = 'Invalid or expired reset token. Please request a new password reset link.';
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        $error = 'An error occurred. Please try again later.';
        error_log("Password reset token validation error: " . $e->getMessage());
    }
} else {
    $error = 'No reset token provided. Please use the link from your email.';
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $resetToken = $_POST['token'] ?? '';
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($resetToken)) {
        $error = 'Invalid reset token.';
    } elseif (empty($newPassword)) {
        $error = 'Please enter a new password.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $conn = getDBConnection();
            
            // Verify token again
            $stmt = $conn->prepare("
                SELECT pr.*, u.id as user_id 
                FROM password_resets pr 
                JOIN users u ON pr.user_id = u.id 
                WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW()
            ");
            $stmt->bind_param("s", $resetToken);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $reset = $result->fetch_assoc();
                $userId = $reset['user_id'];
                
                // Hash new password
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update user password
                $updateStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $updateStmt->bind_param("si", $passwordHash, $userId);
                
                if ($updateStmt->execute()) {
                    // Mark token as used
                    $markStmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                    $markStmt->bind_param("s", $resetToken);
                    $markStmt->execute();
                    $markStmt->close();
                    
                    $updateStmt->close();
                    $stmt->close();
                    $conn->close();
                    
                    $_SESSION['reset_success'] = 'Your password has been reset successfully! You can now login with your new password.';
                    header('Location: login-user.html');
                    exit();
                } else {
                    $error = 'Failed to update password. Please try again.';
                    error_log("Password update failed: " . $conn->error);
                }
                
                $updateStmt->close();
            } else {
                $error = 'Invalid or expired reset token. Please request a new password reset link.';
            }
            
            $stmt->close();
            $conn->close();
            
        } catch (Exception $e) {
            $error = 'An error occurred. Please try again later.';
            error_log("Password reset error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | CGS Network</title>
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
                    <h1>Reset Password</h1>
                    <p><?php echo $validToken ? 'Enter your new password below.' : 'Use the reset link from your email to reset your password.'; ?></p>
                </div>
                
                <?php if ($error): ?>
                    <div style="background-color: #ffebee; color: #d32f2f; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #ffcdd2;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div style="background-color: #e8f5e9; color: #388e3c; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #c8e6c9;">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($validToken): ?>
                    <form class="login-form" method="POST" action="reset-password.php">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <div class="form-group">
                            <label for="resetPassword">New Password</label>
                            <input type="password" id="resetPassword" name="password" placeholder="Enter new password (min. 6 characters)" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="resetPasswordConfirm">Confirm New Password</label>
                            <input type="password" id="resetPasswordConfirm" name="confirm_password" placeholder="Confirm new password" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary btn-full">Reset Password</button>
                    </form>
                <?php else: ?>
                    <div style="text-align: center; padding: 2rem;">
                        <p style="color: var(--text-charcoal); margin-bottom: 1.5rem;"><?php echo htmlspecialchars($error ?: 'Invalid or missing reset token.'); ?></p>
                        <a href="forgot-password.php" class="btn btn-primary" style="text-decoration: none; display: inline-block;">Request New Reset Link</a>
                    </div>
                <?php endif; ?>
                
                <div class="login-footer">
                    <p style="text-align: center; margin-top: 1.5rem;">
                        <a href="login-user.html" style="color: var(--primary-navy); text-decoration: none;">← Back to Login</a>
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
    <script>
        // Password reset form validation
        document.addEventListener('DOMContentLoaded', function() {
            const resetForm = document.querySelector('form[action="reset-password.php"]');
            if (resetForm) {
                resetForm.addEventListener('submit', function(e) {
                    const password = document.getElementById('resetPassword')?.value;
                    const confirmPassword = document.getElementById('resetPasswordConfirm')?.value;
                    
                    if (!password || !confirmPassword) {
                        e.preventDefault();
                        alert('Please enter both password fields.');
                        return false;
                    }
                    
                    if (password.length < 6) {
                        e.preventDefault();
                        alert('Password must be at least 6 characters long.');
                        return false;
                    }
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Passwords do not match.');
                        return false;
                    }
                    
                    return true;
                });
            }
        });
    </script>
</body>
</html>

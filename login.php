<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CGS Network</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php $activePage = 'network'; include 'header-main.php'; ?>

    <main class="login-main">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to your CGS Network account</p>
                </div>
                
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div style="background-color: #fee; color: #c33; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #fcc;">
                        <?php echo htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?>
                    </div>
                <?php endif; ?>
                
                <form class="login-form" method="POST" action="user-auth.php?action=login<?php echo isset($_GET['redirect']) ? '&redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Sign In</button>
                </form>
                <div class="login-footer">
                    <p>Don't have an account? <a href="network.php">Create one here</a></p>
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
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About CGS</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="network.php">Network</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="governance-codes.php">Governance Codes</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="media.php">Media</a></li>
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

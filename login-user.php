<?php
require_once 'config.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
    $redirect = $_GET['redirect'] ?? 'user-dashboard.php';
    header('Location: ' . $redirect);
    exit();
}
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
                    <p>Sign in to your account to continue</p>
                </div>
                <?php
                require_once 'config.php';
                session_start();
                
                if (isset($_SESSION['login_error'])): ?>
                    <div style="background-color: #ffebee; color: #d32f2f; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #ffcdd2;">
                        <?php echo htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div style="background-color: #e8f5e9; color: #388e3c; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #c8e6c9;">
                        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <form class="login-form" method="POST" action="user-auth.php?action=login<?php echo isset($_GET['redirect']) ? '&redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
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
                    <p>Don't have an account? <a href="network.php">Join the Network</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CGS</h3>
                    <p>Transforming organizations worldwide</p>
                    <div class="social-links">
                        <a href="https://web.facebook.com/cstsghana/about" target="_blank" title="Facebook" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path>
                            </svg>
                        </a>
                        <a href="https://x.com/cstsgh" target="_blank" title="Twitter/X" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/cstsghana/#" target="_blank" title="Instagram" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.325 3.608 1.3.975.975 1.238 2.243 1.3 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.325 2.633-1.3 3.608-.975.975-2.243 1.238-3.608 1.3-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.325-3.608-1.3-.975-.975-1.238-2.243-1.3-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.325-2.633 1.3-3.608.975-.975 2.243-1.238 3.608-1.3 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.257 0-3.667.012-4.947.07-1.417.062-2.798.364-3.877 1.443-1.078 1.078-1.381 2.46-1.443 3.877-.058 1.28-.07 1.69-.07 4.947s.012 3.667.07 4.947c.062 1.417.364 2.798 1.443 3.877 1.078 1.078 2.46 1.381 3.877 1.443 1.28.058 1.69.07 4.947.07s3.667-.012 4.947-.07c1.417-.062 2.798-.364 3.877-1.443 1.078-1.078 1.381-2.46 1.443-3.877.058-1.28.07-1.69.07-4.947s-.012-3.667-.07-4.947c-.062-1.417-.364-2.798-1.443-3.877-1.078-1.078-2.46-1.381-3.877-1.443-1.28-.058-1.69-.07-4.947-.07zm0 5.838c-3.403 0-6.162 2.76-6.162 6.162s2.76 6.162 6.162 6.162 6.162-2.76 6.162-6.162-2.76-6.162-6.162-6.162zm0 10.002c-2.117 0-3.841-1.725-3.841-3.841s1.725-3.841 3.841-3.841 3.841 1.725 3.841 3.841-1.725 3.841-3.841 3.841zm6.406-10.846c-.796 0-1.441-.646-1.441-1.441s.646-1.441 1.441-1.441 1.441.646 1.441 1.441-.646 1.441-1.441 1.441z"></path>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/in/csts-ghana-398975174/" target="_blank" title="LinkedIn" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.225 0h-20.451c-.979 0-1.774.796-1.774 1.774v20.451c0 .979.796 1.774 1.774 1.774h20.451c.979 0 1.774-.796 1.774-1.774v-20.451c0-.979-.796-1.774-1.774-1.774zm-15.451 20.451h-3.605v-11.69h3.605v11.69zm-1.802-13.243c-1.148 0-2.082-.935-2.082-2.082s.935-2.082 2.082-2.082 2.082.935 2.082 2.082-.935 2.082-2.082 2.082zm14.053 13.243h-3.605v-5.991c0-1.428-.028-3.27-1.991-3.27-1.993 0-2.297 1.554-2.297 3.164v6.097h-3.605v-11.69h3.461v1.597h.049c.482-.911 1.66-1.872 3.417-1.872 3.649 0 4.323 2.403 4.323 5.525v6.439z"></path>
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/@cstsghana6166" target="_blank" title="YouTube" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.615 3.184c-2.096-.218-10.634-.218-12.73 0-2.536.263-4.5 2.18-4.772 4.748-.218 2.096-.218 6.466 0 8.562.263 2.536 2.18 4.5 4.748 4.772 2.096.218 10.634.218 12.73 0 2.536-.263 4.5-2.18 4.772-4.748.218-2.096.218-6.466 0-8.562-.263-2.536-2.18-4.5-4.748-4.772zm-9.615 11.816v-7l6 3.5-6 3.5z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Strategy</a></li>
                        <li><a href="#">Consulting</a></li>
                        <li><a href="#">Digital</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Corporate Governance Series (CGS) by CSTS Ghana. All rights reserved. | <a href="admin-login.php" style="color: var(--accent-gold);">Admin Login</a></p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>

<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network | Corporate Governance Series</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-top">
            <div class="nav-top-container">
                <div class="logo">
                    <a href="index.html"><img src="logo.png" alt="Corporate Governance Series CGS Logo"></a>
                </div>
                <div class="nav-utility">
                    <button class="search-btn" id="searchBtn">Search</button>
                    <a href="network.html" class="login-btn">Login</a>
                    <a href="network.html" class="join-btn">Join the Network</a>
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
                <li><a href="network.html" class="active">Network</a></li>
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

    <main>
        <section class="page-header">
            <div class="container">
                <h1>Connect with Africa's Governance Elite</h1>
                <p class="page-subtitle">A curated network of directors, lawyers, bankers, regulators, scholars, and governance professionals.</p>
            </div>
        </section>

        <section class="section content-section" id="loginSection">
            <div class="container">
                <div style="max-width: 500px; margin: 0 auto;">
                    <div class="card" style="padding: 3rem;">
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <h2 style="font-size: 2rem; margin-bottom: 0.5rem;">Sign In</h2>
                            <p style="color: var(--text-charcoal);">Access your member dashboard</p>
                        </div>
                        <?php
                        require_once 'config.php';
                        if (isset($_SESSION['login_error'])) {
                            echo '<div style="background-color: #fee; color: #c33; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                            unset($_SESSION['login_error']);
                        }
                        ?>
                        <form id="loginForm" method="POST" action="user-auth.php?action=login">
                            <input type="hidden" name="action" value="login">
                            <div class="form-group">
                                <label for="loginEmail">Email Address</label>
                                <input type="email" id="loginEmail" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="loginPassword">Password</label>
                                <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
                            </div>
                            <div style="margin-bottom: 1.5rem;">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="remember">
                                    <span>Remember me</span>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-full">Sign In</button>
                            <div style="text-align: center; margin-top: 1.5rem;">
                                <a href="#" id="showRegister" style="color: var(--primary-navy); text-decoration: none; font-weight: 600;">Don't have an account? Create one →</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="section content-section-alt" id="registerSection" style="display: none;">
            <div class="container">
                <div style="max-width: 700px; margin: 0 auto;">
                    <div class="card" style="padding: 3rem;">
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <h2 style="font-size: 2rem; margin-bottom: 0.5rem;">Create Account</h2>
                            <p style="color: var(--text-charcoal);">Join the CGS Network</p>
                        </div>
                        <?php
                        if (isset($_SESSION['register_error'])) {
                            echo '<div style="background-color: #fee; color: #c33; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
                            unset($_SESSION['register_error']);
                        }
                        ?>
                        <form id="registerForm" method="POST" action="user-auth.php?action=register">
                            <input type="hidden" name="action" value="register">
                            <div class="form-group">
                                <label for="registerName">Full Name</label>
                                <input type="text" id="registerName" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="registerEmail">Email Address</label>
                                <input type="email" id="registerEmail" name="email" required>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label for="registerCountry">Country</label>
                                    <input type="text" id="registerCountry" name="country" required>
                                </div>
                                <div class="form-group">
                                    <label for="registerCity">City</label>
                                    <input type="text" id="registerCity" name="city" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="registerOrganization">Organization / Institution</label>
                                <input type="text" id="registerOrganization" name="organization">
                            </div>
                            <div class="form-group">
                                <label for="registerRole">Role</label>
                                <select id="registerRole" name="role" required>
                                    <option value="">Select your role</option>
                                    <option value="lawyer">Lawyer</option>
                                    <option value="banker">Banker</option>
                                    <option value="board">Board Member/Director</option>
                                    <option value="student">Student</option>
                                    <option value="regulator">Regulator</option>
                                    <option value="consultant">Consultant</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Areas of Interest (select all that apply)</label>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem; margin-top: 0.5rem;">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="interests" value="board-effectiveness">
                                        <span>Board Effectiveness</span>
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="interests" value="compliance">
                                        <span>Compliance</span>
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="interests" value="esg">
                                        <span>ESG</span>
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="interests" value="risk">
                                        <span>Risk Governance</span>
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="interests" value="digital">
                                        <span>Digital Governance</span>
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="interests" value="soes">
                                        <span>State-Owned Entities</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="registerBio">Short Bio</label>
                                <textarea id="registerBio" name="bio" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="registerLinkedIn">LinkedIn URL (optional)</label>
                                <input type="url" id="registerLinkedIn" name="linkedin">
                            </div>
                            <div class="form-group">
                                <label for="registerPassword">Password</label>
                                <input type="password" id="registerPassword" name="password" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label for="registerPasswordConfirm">Confirm Password</label>
                                <input type="password" id="registerPasswordConfirm" name="confirm_password" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
                            <div style="text-align: center; margin-top: 1.5rem;">
                                <a href="#" id="showLogin" style="color: var(--primary-navy); text-decoration: none; font-weight: 600;">Already have an account? Sign in →</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="section content-section" id="benefitsSection">
            <div class="container">
                <h2 class="section-title">Member Benefits</h2>
                <div class="delivers-grid">
                    <div class="deliver-card">
                        <h3>Member Directory</h3>
                        <p>Connect with governance professionals across Africa. Search by country, sector, and expertise to build your network.</p>
                    </div>
                    <div class="deliver-card">
                        <h3>Exclusive Resources</h3>
                        <p>Access members-only whitepapers, briefs, and in-depth analysis unavailable to the general public.</p>
                    </div>
                    <div class="deliver-card">
                        <h3>First-to-Know Invitations</h3>
                        <p>Receive priority registration and early access to CGS events, webinars, and flagship sessions.</p>
                    </div>
                    <div class="deliver-card">
                        <h3>Private Discussions</h3>
                        <p>Participate in exclusive roundtables, member-only forums, and private networking events.</p>
                    </div>
                    <div class="deliver-card">
                        <h3>Profile Credibility</h3>
                        <p>Build your professional profile with badges, participation history, and publication credits.</p>
                    </div>
                    <div class="deliver-card">
                        <h3>Thought Leadership</h3>
                        <p>Opportunities to contribute to the CGS blog, speak at events, and shape governance discourse in Africa.</p>
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
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="about.html">About CGS</a></li>
                        <li><a href="events.html">Events</a></li>
                        <li><a href="training.html">Training</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="governance-codes.html">Governance Codes</a></li>
                        <li><a href="blog.html">Blog</a></li>
                        <li><a href="media.html">Media</a></li>
                        <li><a href="network.html">Network</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Stay Informed</h4>
                    <p>Subscribe to our newsletter for updates on events, resources, and governance insights.</p>
                    <form class="newsletter-form" id="newsletterForm">
                        <input type="email" placeholder="Your email address" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Corporate Governance Series (CGS) by CSTS Ghana. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a> | <a href="admin-login.php" style="color: var(--accent-gold);">Admin Login</a></p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Toggle between login and register forms
        document.getElementById('showRegister')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginSection').style.display = 'none';
            document.getElementById('registerSection').style.display = 'block';
        });

        document.getElementById('showLogin')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('registerSection').style.display = 'none';
            document.getElementById('loginSection').style.display = 'block';
        });

        // Form handlers
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            // Form will submit normally to PHP handler
        });

        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('registerPassword').value;
            const passwordConfirm = document.getElementById('registerPasswordConfirm').value;
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters.');
                return;
            }
            // Form will submit normally to PHP handler
        });
    </script>
</body>
</html>

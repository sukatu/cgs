<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | CGS Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-navy) 0%, #0d2f7a 100%);
            padding: 2rem;
        }
        .login-card {
            background: white;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-card h1 {
            color: var(--primary-navy);
            margin-bottom: 0.5rem;
        }
        .login-card p {
            color: var(--text-light);
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-charcoal);
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--divider-grey);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-navy);
        }
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--primary-navy);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #081c4f;
        }
        .error-message {
            background-color: #fee;
            color: #c33;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border: 1px solid #fcc;
        }
        .success-message {
            background-color: #efe;
            color: #3c3;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border: 1px solid #cfc;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1>Admin Login</h1>
            <p>Corporate Governance Series Dashboard</p>
            
            <?php
            if (isset($_GET['logout'])) {
                session_destroy();
                echo '<div class="success-message">You have been logged out successfully.</div>';
            }
            if (isset($_SESSION['login_error'])) {
                echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            ?>
            
            <form method="POST" action="admin-auth.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            
            <div style="margin-top: 2rem; text-align: center;">
                <a href="index.php" style="color: var(--text-light); text-decoration: none;">← Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>

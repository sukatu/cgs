<?php
require_once 'config.php';

// User login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter both email and password';
        $redirectUrl = $_GET['redirect'] ?? 'login-user.html';
        header('Location: ' . $redirectUrl);
        exit();
    }
    
    // Get database connection
    try {
        $conn = getDBConnection();
    } catch (Exception $e) {
        $_SESSION['login_error'] = 'Database connection error. Please try again later.';
        error_log("Database connection error: " . $e->getMessage());
        header('Location: login-user.html');
        exit();
    }
    
    // Check if users table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
    if ($tableCheck->num_rows === 0) {
        $_SESSION['login_error'] = 'Database not properly configured. Please contact administrator.';
        $conn->close();
        header('Location: login-user.html');
        exit();
    }
    
    // Get user from database
    $stmt = $conn->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
    if (!$stmt) {
        $_SESSION['login_error'] = 'Database error. Please try again later.';
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        header('Location: login-user.html');
        exit();
    }
    
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        $_SESSION['login_error'] = 'Database error. Please try again later.';
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        header('Location: login-user.html');
        exit();
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Handle "Remember me" functionality (optional - can be enhanced with cookies)
            if (isset($_POST['remember']) && $_POST['remember']) {
                // Set a longer session timeout or cookie (optional implementation)
                ini_set('session.gc_maxlifetime', 86400 * 30); // 30 days
            }
            
            $stmt->close();
            $conn->close();
            
            // Redirect to dashboard or return to previous page
            $redirect = $_GET['redirect'] ?? 'user-dashboard.php';
            // Sanitize redirect URL to prevent open redirects
            $redirect = filter_var($redirect, FILTER_SANITIZE_URL);
            if (!filter_var($redirect, FILTER_VALIDATE_URL) || parse_url($redirect, PHP_URL_HOST) === null) {
                // Only allow relative URLs
                header('Location: ' . $redirect);
            } else {
                header('Location: user-dashboard.php');
            }
            exit();
        } else {
            // Invalid password
            $_SESSION['login_error'] = 'Invalid email or password';
        }
    } else {
        // User not found
        $_SESSION['login_error'] = 'Invalid email or password';
    }
    
    $stmt->close();
    $conn->close();
    
    // Redirect back to login page with error
    $redirectUrl = $_GET['redirect'] ?? 'login-user.html';
    header('Location: ' . $redirectUrl);
    exit();
}

// User registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    // Sanitize and collect all form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $linkedin_url = trim($_POST['linkedin'] ?? '');
    
    // Handle interests (checkboxes come as array)
    $interests = [];
    if (isset($_POST['interests']) && is_array($_POST['interests'])) {
        $interests = $_POST['interests'];
    }
    $interests_string = !empty($interests) ? implode(', ', $interests) : '';
    
    // Validation
    if (empty($name)) {
        $_SESSION['register_error'] = 'Full name is required';
        header('Location: network.php');
        exit();
    }
    
    if (empty($email)) {
        $_SESSION['register_error'] = 'Email address is required';
        header('Location: network.php');
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = 'Please enter a valid email address';
        header('Location: network.php');
        exit();
    }
    
    if (empty($password)) {
        $_SESSION['register_error'] = 'Password is required';
        header('Location: network.php');
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['register_error'] = 'Password must be at least 6 characters long';
        header('Location: network.php');
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = 'Passwords do not match';
        header('Location: network.php');
        exit();
    }
    
    // Validate required fields
    if (empty($country)) {
        $_SESSION['register_error'] = 'Country is required';
        header('Location: network.php');
        exit();
    }
    
    if (empty($city)) {
        $_SESSION['register_error'] = 'City is required';
        header('Location: network.php');
        exit();
    }
    
    if (empty($role)) {
        $_SESSION['register_error'] = 'Please select your role';
        header('Location: network.php');
        exit();
    }
    
    // Validate LinkedIn URL if provided
    if (!empty($linkedin_url) && !filter_var($linkedin_url, FILTER_VALIDATE_URL)) {
        $_SESSION['register_error'] = 'Please enter a valid LinkedIn URL';
        header('Location: network.php');
        exit();
    }
    
    // Get database connection
    try {
        $conn = getDBConnection();
    } catch (Exception $e) {
        $_SESSION['register_error'] = 'Database connection error. Please try again later.';
        error_log("Database connection error: " . $e->getMessage());
        header('Location: network.php');
        exit();
    }
    
    // Check if users table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
    if ($tableCheck->num_rows === 0) {
        $_SESSION['register_error'] = 'Database not properly configured. Please contact administrator.';
        $conn->close();
        header('Location: network.php');
        exit();
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        $_SESSION['register_error'] = 'Database error. Please try again later.';
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        header('Location: network.php');
        exit();
    }
    
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        $_SESSION['register_error'] = 'Database error. Please try again later.';
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        header('Location: network.php');
        exit();
    }
    
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['register_error'] = 'This email is already registered. Please login instead.';
        $stmt->close();
        $conn->close();
        header('Location: network.php');
        exit();
    }
    $stmt->close();
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if ($password_hash === false) {
        $_SESSION['register_error'] = 'Password hashing failed. Please try again.';
        $conn->close();
        header('Location: network.php');
        exit();
    }
    
    // Create user with all fields
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, phone, country, city, organization, role, interests, bio, linkedin_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        $_SESSION['register_error'] = 'Database error. Please try again later.';
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        header('Location: network.php');
        exit();
    }
    
    $stmt->bind_param("sssssssssss", $name, $email, $password_hash, $phone, $country, $city, $organization, $role, $interests_string, $bio, $linkedin_url);
    
    if ($stmt->execute()) {
        $newUserId = $stmt->insert_id;
        $stmt->close();
        
        // Verify user was created
        if ($newUserId > 0) {
            // Auto-login after successful registration
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            // Set success message
            $_SESSION['success'] = 'Account created successfully! Welcome to the CGS Network.';
            
            $conn->close();
            
            // Redirect to dashboard
            header('Location: user-dashboard.php');
            exit();
        } else {
            $_SESSION['register_error'] = 'Registration failed. Could not create user account.';
            $conn->close();
            header('Location: network.php');
            exit();
        }
    } else {
        $_SESSION['register_error'] = 'Registration failed. Please try again.';
        error_log("Registration insert failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        header('Location: network.php');
        exit();
    }
}

// Logout
if (isset($_GET['logout']) || (isset($_POST['action']) && $_POST['action'] === 'logout')) {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to home page
    header('Location: index.html');
    exit();
}
?>

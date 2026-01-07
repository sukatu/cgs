<?php
require_once 'config.php';

// User login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Redirect to dashboard or return to previous page
            $redirect = $_GET['redirect'] ?? 'user-dashboard.php';
            header('Location: ' . $redirect);
            exit();
        }
    }
    
    $_SESSION['login_error'] = 'Invalid email or password';
    header('Location: login.html');
    exit();
}

// User registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';
    $organization = $_POST['organization'] ?? '';
    $role = $_POST['role'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['register_error'] = 'Name, email, and password are required';
        header('Location: network.html');
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = 'Passwords do not match';
        header('Location: network.html');
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['register_error'] = 'Password must be at least 6 characters';
        header('Location: network.html');
        exit();
    }
    
    $conn = getDBConnection();
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['register_error'] = 'Email already registered. Please login instead.';
        header('Location: network.html');
        exit();
    }
    
    // Create user
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, phone, country, city, organization, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $password_hash, $phone, $country, $city, $organization, $role);
    
    if ($stmt->execute()) {
        // Auto-login after registration
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        
        header('Location: user-dashboard.php');
        exit();
    } else {
        $_SESSION['register_error'] = 'Registration failed. Please try again.';
        header('Location: network.html');
        exit();
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.html');
    exit();
}
?>

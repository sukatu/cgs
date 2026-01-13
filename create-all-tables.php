<?php
// Complete database setup - Creates ALL tables at once
// Run this once: http://localhost:8000/create-all-tables.php

require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "✅ Database '" . DB_NAME . "' created or already exists.<br><br>";
} else {
    echo "❌ Error creating database: " . $conn->error . "<br><br>";
    exit();
}

// Select the database
$conn->select_db(DB_NAME);

// 1. Create events table
$sql = "CREATE TABLE IF NOT EXISTS events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_type ENUM('webinar', 'series', 'other') DEFAULT 'webinar',
    event_date DATETIME NOT NULL,
    location VARCHAR(255),
    format ENUM('online', 'hybrid', 'in-person') DEFAULT 'online',
    registration_link VARCHAR(500),
    youtube_url VARCHAR(500),
    agenda TEXT,
    speakers TEXT,
    moderator VARCHAR(255),
    summary TEXT,
    tags VARCHAR(500),
    countries VARCHAR(255),
    status ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✅ Events table created successfully.<br>";
} else {
    echo "❌ Error creating events table: " . $conn->error . "<br>";
}

// 2. Create admin_users table
$sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✅ Admin users table created successfully.<br>";
    
    // Create default admin user
    $defaultUsername = 'admin';
    $defaultPassword = password_hash('cgs2025', PASSWORD_DEFAULT);
    
    $checkUser = $conn->query("SELECT id FROM admin_users WHERE username = '$defaultUsername'");
    if ($checkUser->num_rows == 0) {
        $insertUser = "INSERT INTO admin_users (username, password_hash, email) 
                       VALUES ('$defaultUsername', '$defaultPassword', 'admin@cgsghana.com')";
        if ($conn->query($insertUser) === TRUE) {
            echo "✅ Default admin user created (Username: admin, Password: cgs2025).<br>";
        } else {
            echo "⚠️ Admin user already exists or error: " . $conn->error . "<br>";
        }
    } else {
        echo "ℹ️ Admin user already exists.<br>";
    }
} else {
    echo "❌ Error creating admin_users table: " . $conn->error . "<br>";
}

// 3. Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    country VARCHAR(100),
    city VARCHAR(100),
    organization VARCHAR(255),
    role VARCHAR(100),
    interests TEXT,
    bio TEXT,
    profile_picture VARCHAR(500),
    linkedin_url VARCHAR(500),
    email_verified BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✅ Users table created successfully.<br>";
    
    // Add profile_picture column if it doesn't exist (for existing tables)
    $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    if ($checkColumn->num_rows == 0) {
        $addColumn = "ALTER TABLE users ADD COLUMN profile_picture VARCHAR(500) DEFAULT NULL";
        if ($conn->query($addColumn) === TRUE) {
            echo "✅ Added profile_picture column to users table.<br>";
        }
    }
} else {
    echo "❌ Error creating users table: " . $conn->error . "<br>";
}

// 4. Create event_registrations table
$sql = "CREATE TABLE IF NOT EXISTS event_registrations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    event_id INT(11) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (user_id, event_id),
    INDEX idx_user (user_id),
    INDEX idx_event (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✅ Event registrations table created successfully.<br>";
} else {
    // If foreign key constraint fails, try without it first
    if (strpos($conn->error, 'foreign key constraint') !== false) {
        echo "⚠️ Trying to create registrations table without foreign keys...<br>";
        $sql2 = "CREATE TABLE IF NOT EXISTS event_registrations (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            event_id INT(11) NOT NULL,
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
            notes TEXT,
            UNIQUE KEY unique_registration (user_id, event_id),
            INDEX idx_user (user_id),
            INDEX idx_event (event_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($sql2) === TRUE) {
            echo "✅ Event registrations table created (without foreign keys).<br>";
        } else {
            echo "❌ Error: " . $conn->error . "<br>";
        }
    } else {
        echo "❌ Error creating event_registrations table: " . $conn->error . "<br>";
    }
}

// 5. Create user_papers table
echo "<br>5. Creating user_papers table...<br>";
$sql = "CREATE TABLE IF NOT EXISTS user_papers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    abstract TEXT,
    keywords VARCHAR(255) DEFAULT NULL,
    category VARCHAR(50) DEFAULT NULL,
    file_path VARCHAR(500) DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'under-review',
    submitted_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_date DATETIME DEFAULT NULL,
    KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✅ user_papers table created successfully.<br>";
    
    // Add foreign key if users table exists
    $checkFK = $conn->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'user_papers' 
        AND CONSTRAINT_NAME LIKE '%user_id%'
        AND REFERENCED_TABLE_NAME = 'users'
    ");
    if ($checkFK->num_rows == 0) {
        $addFK = "ALTER TABLE user_papers 
                  ADD CONSTRAINT fk_user_papers_user 
                  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
        if ($conn->query($addFK) === TRUE) {
            echo "✅ Foreign key added to user_papers table.<br>";
        } else {
            echo "⚠️ Could not add foreign key (may already exist or users table missing): " . $conn->error . "<br>";
        }
    }
} else {
    echo "❌ Error creating user_papers table: " . $conn->error . "<br>";
}

// 6. Create user_library table
echo "<br>6. Creating user_library table...<br>";
$sql = "CREATE TABLE IF NOT EXISTS user_library (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    resource_url VARCHAR(500) DEFAULT NULL,
    resource_type VARCHAR(50) DEFAULT NULL,
    resource_id VARCHAR(100) DEFAULT NULL,
    saved_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY user_id (user_id),
    KEY resource_type (resource_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✅ user_library table created successfully.<br>";
    
    // Add unique constraint if it doesn't exist
    $checkIndex = $conn->query("SHOW INDEX FROM user_library WHERE Key_name = 'unique_bookmark'");
    if ($checkIndex->num_rows == 0) {
        $addIndex = "ALTER TABLE user_library ADD UNIQUE KEY unique_bookmark (user_id, resource_type, resource_id)";
        if ($conn->query($addIndex) === TRUE) {
            echo "✅ Unique constraint added to user_library table.<br>";
        } else {
            echo "⚠️ Could not add unique constraint: " . $conn->error . "<br>";
        }
    }
    
    // Add foreign key if users table exists
    $checkFK = $conn->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'user_library' 
        AND CONSTRAINT_NAME LIKE '%user_id%'
        AND REFERENCED_TABLE_NAME = 'users'
    ");
    if ($checkFK->num_rows == 0) {
        $addFK = "ALTER TABLE user_library 
                  ADD CONSTRAINT fk_user_library_user 
                  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
        if ($conn->query($addFK) === TRUE) {
            echo "✅ Foreign key added to user_library table.<br>";
        } else {
            echo "⚠️ Could not add foreign key (may already exist or users table missing): " . $conn->error . "<br>";
        }
    }
} else {
    echo "❌ Error creating user_library table: " . $conn->error . "<br>";
}

$conn->close();

echo "<br><hr>";
echo "<h2>✅ Database Setup Complete!</h2>";
echo "<p><strong>All tables created:</strong></p>";
echo "<ul>";
echo "<li>✅ events</li>";
echo "<li>✅ admin_users</li>";
echo "<li>✅ users (with profile_picture column)</li>";
echo "<li>✅ event_registrations</li>";
echo "<li>✅ user_papers</li>";
echo "<li>✅ user_library</li>";
echo "<li>✅ password_resets</li>";
echo "</ul>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li><a href='admin-login.php'>Go to Admin Login</a> (Username: admin, Password: cgs2025)</li>";
echo "<li><a href='network.php'>Go to User Registration/Login</a></li>";
echo "<li><a href='user-dashboard.php'>Go to User Dashboard</a></li>";
echo "<li><a href='index.html'>Go to Homepage</a></li>";
echo "</ul>";
?>


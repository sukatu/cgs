<?php
// Database setup script for user dashboard functionality
// Run this once: http://localhost/cgs/create-user-tables.php
// This creates: user_papers, user_library tables and adds columns to users table

require_once 'config.php';

$conn = getDBConnection();

echo "<h2>Creating User Dashboard Tables</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .success { color: #388e3c; background: #e8f5e9; padding: 10px; border-radius: 4px; margin: 10px 0; }
    .error { color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 4px; margin: 10px 0; }
    .info { color: #1976d2; background: #e3f2fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
    h2 { color: #1976d2; }
</style>";

// 1. Create user_papers table
echo "<h3>1. Creating user_papers table...</h3>";
$sql = "CREATE TABLE IF NOT EXISTS `user_papers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `abstract` text,
  `keywords` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'under-review',
  `submitted_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `reviewed_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "<div class='success'>✅ user_papers table created successfully.</div>";
} else {
    // Check if table already exists
    if (strpos($conn->error, 'already exists') !== false) {
        echo "<div class='info'>ℹ️ user_papers table already exists.</div>";
    } else {
        echo "<div class='error'>❌ Error creating user_papers table: " . $conn->error . "</div>";
    }
}

// 2. Create user_library table
echo "<h3>2. Creating user_library table...</h3>";
$sql = "CREATE TABLE IF NOT EXISTS `user_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `resource_url` varchar(500) DEFAULT NULL,
  `resource_type` varchar(50) DEFAULT NULL,
  `resource_id` varchar(100) DEFAULT NULL,
  `saved_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `resource_type` (`resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "<div class='success'>✅ user_library table created successfully.</div>";
    
    // Check if unique constraint exists, if not add it
    $checkIndex = $conn->query("SHOW INDEX FROM user_library WHERE Key_name = 'unique_bookmark'");
    if ($checkIndex->num_rows == 0) {
        $addIndex = "ALTER TABLE `user_library` ADD UNIQUE KEY `unique_bookmark` (`user_id`, `resource_type`, `resource_id`)";
        if ($conn->query($addIndex) === TRUE) {
            echo "<div class='success'>✅ Added unique constraint to user_library table.</div>";
        } else {
            echo "<div class='error'>⚠️ Could not add unique constraint: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='info'>ℹ️ Unique constraint already exists on user_library table.</div>";
    }
} else {
    if (strpos($conn->error, 'already exists') !== false) {
        echo "<div class='info'>ℹ️ user_library table already exists.</div>";
    } else {
        echo "<div class='error'>❌ Error creating user_library table: " . $conn->error . "</div>";
    }
}

// 3. Check if users table exists
echo "<h3>3. Checking users table...</h3>";
$checkUsers = $conn->query("SHOW TABLES LIKE 'users'");
if ($checkUsers->num_rows > 0) {
    echo "<div class='success'>✅ users table exists.</div>";
    
    // 4. Add profile_picture column if it doesn't exist
    echo "<h3>4. Adding profile_picture column to users table...</h3>";
    $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    if ($checkColumn->num_rows == 0) {
        $sql = "ALTER TABLE `users` ADD COLUMN `profile_picture` varchar(500) DEFAULT NULL";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='success'>✅ profile_picture column added to users table.</div>";
        } else {
            echo "<div class='error'>❌ Error adding profile_picture column: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='info'>ℹ️ profile_picture column already exists in users table.</div>";
    }
    
    // 5. Add bio column if it doesn't exist
    echo "<h3>5. Adding bio column to users table...</h3>";
    $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'bio'");
    if ($checkColumn->num_rows == 0) {
        $sql = "ALTER TABLE `users` ADD COLUMN `bio` text DEFAULT NULL";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='success'>✅ bio column added to users table.</div>";
        } else {
            echo "<div class='error'>❌ Error adding bio column: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='info'>ℹ️ bio column already exists in users table.</div>";
    }
} else {
    echo "<div class='error'>❌ users table does not exist. Please run database_setup_users.php first.</div>";
}

// 6. Add foreign key constraints if they don't exist
echo "<h3>6. Checking foreign key constraints...</h3>";

// Check foreign key on user_papers
$checkFK = $conn->query("
    SELECT CONSTRAINT_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'user_papers' 
    AND CONSTRAINT_NAME LIKE '%user_id%'
    AND REFERENCED_TABLE_NAME = 'users'
");
if ($checkFK->num_rows == 0) {
    // Add foreign key constraint
    $sql = "ALTER TABLE `user_papers` 
            ADD CONSTRAINT `fk_user_papers_user` 
            FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✅ Foreign key constraint added to user_papers table.</div>";
    } else {
        echo "<div class='error'>⚠️ Could not add foreign key to user_papers: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='info'>ℹ️ Foreign key constraint already exists on user_papers table.</div>";
}

// Check foreign key on user_library
$checkFK = $conn->query("
    SELECT CONSTRAINT_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'user_library' 
    AND CONSTRAINT_NAME LIKE '%user_id%'
    AND REFERENCED_TABLE_NAME = 'users'
");
if ($checkFK->num_rows == 0) {
    // Add foreign key constraint
    $sql = "ALTER TABLE `user_library` 
            ADD CONSTRAINT `fk_user_library_user` 
            FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✅ Foreign key constraint added to user_library table.</div>";
    } else {
        echo "<div class='error'>⚠️ Could not add foreign key to user_library: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='info'>ℹ️ Foreign key constraint already exists on user_library table.</div>";
}

// 7. Create password_resets table
echo "<h3>7. Creating password_resets table...</h3>";
$sql = "CREATE TABLE IF NOT EXISTS `password_resets` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "<div class='success'>✅ password_resets table created successfully.</div>";
    
    // Check foreign key on password_resets
    $checkFK = $conn->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'password_resets' 
        AND CONSTRAINT_NAME LIKE '%user_id%'
        AND REFERENCED_TABLE_NAME = 'users'
    ");
    if ($checkFK->num_rows == 0) {
        // Add foreign key constraint
        $sql = "ALTER TABLE `password_resets` 
                ADD CONSTRAINT `fk_password_resets_user` 
                FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='success'>✅ Foreign key constraint added to password_resets table.</div>";
        } else {
            echo "<div class='error'>⚠️ Could not add foreign key to password_resets: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='info'>ℹ️ Foreign key constraint already exists on password_resets table.</div>";
    }
} else {
    if (strpos($conn->error, 'already exists') !== false) {
        echo "<div class='info'>ℹ️ password_resets table already exists.</div>";
    } else {
        echo "<div class='error'>❌ Error creating password_resets table: " . $conn->error . "</div>";
    }
}

$conn->close();

echo "<br><div class='success' style='font-size: 1.2em; font-weight: bold; padding: 20px;'>✅ Database setup complete! All necessary tables have been created.</div>";
echo "<p><a href='user-dashboard.php'>Go to User Dashboard</a> | <a href='index.html'>Go to Home</a></p>";
?>

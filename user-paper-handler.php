<?php
require_once 'config.php';
requireUserLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

// Create user_papers table if it doesn't exist
$createTableSQL = "
CREATE TABLE IF NOT EXISTS `user_papers` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($createTableSQL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (empty($_POST['title'])) {
        $_SESSION['error'] = 'Paper title is required.';
        header('Location: user-dashboard.php?section=papers');
        exit();
    }
    
    if (empty($_POST['abstract'])) {
        $_SESSION['error'] = 'Paper abstract is required.';
        header('Location: user-dashboard.php?section=papers');
        exit();
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['paper_file']) || $_FILES['paper_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Please upload a PDF file.';
        header('Location: user-dashboard.php?section=papers');
        exit();
    }
    
    $title = trim($_POST['title']);
    $abstract = trim($_POST['abstract']);
    $keywords = trim($_POST['keywords'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    // Handle file upload
    $uploadDir = 'uploads/papers/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            $_SESSION['error'] = 'Failed to create upload directory. Please contact administrator.';
            header('Location: user-dashboard.php?section=papers');
            exit();
        }
    }
    
    $file = $_FILES['paper_file'];
    
    // Validate file type
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['pdf'];
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error'] = 'Only PDF files are allowed.';
        header('Location: user-dashboard.php?section=papers');
        exit();
    }
    
    // Validate MIME type as well
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if ($mimeType !== 'application/pdf') {
        $_SESSION['error'] = 'Invalid file type. Only PDF files are allowed.';
        header('Location: user-dashboard.php?section=papers');
        exit();
    }
    
    // Validate file size (10MB max)
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        $_SESSION['error'] = 'File size must be less than 10MB. Your file is ' . round($file['size'] / 1024 / 1024, 2) . 'MB.';
        header('Location: user-dashboard.php?section=papers');
        exit();
    }
    
    // Generate unique filename
    $fileName = $userId . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
    $targetPath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $filePath = $targetPath;
        
        // Insert into database
        $stmt = $conn->prepare("
            INSERT INTO user_papers (user_id, title, abstract, keywords, category, file_path, status, submitted_date)
            VALUES (?, ?, ?, ?, ?, ?, 'under-review', NOW())
        ");
        
        if ($stmt) {
            $stmt->bind_param("isssss", $userId, $title, $abstract, $keywords, $category, $filePath);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Paper submitted successfully! Your paper is now under review.';
            } else {
                // Delete uploaded file if database insert fails
                unlink($targetPath);
                $_SESSION['error'] = 'Failed to submit paper. Please try again. Error: ' . $conn->error;
            }
            
            $stmt->close();
        } else {
            // Delete uploaded file if prepare fails
            unlink($targetPath);
            $_SESSION['error'] = 'Database error. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Failed to upload file. Please check file permissions or try again.';
    }
    
    $conn->close();
    
    header('Location: user-dashboard.php?section=papers');
    exit();
} else {
    header('Location: user-dashboard.php?section=papers');
    exit();
}
?>

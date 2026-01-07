<?php
require_once 'config.php';
requireAdminLogin();

$conn = getDBConnection();
$alert = '';
$images = [];

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = 'images/uploads/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $file = $_FILES['image'];
    $originalFilename = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    // Validate file
    if ($fileError === UPLOAD_ERR_OK) {
        // Get file extension
        $fileExt = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExt, $allowedExts)) {
            // Generate unique filename
            $newFilename = uniqid('gallery_', true) . '.' . $fileExt;
            $uploadPath = $uploadDir . $newFilename;
            
            // Move uploaded file
            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                // Save to database
                $altText = isset($_POST['alt_text']) ? trim($_POST['alt_text']) : '';
                $category = isset($_POST['category']) ? trim($_POST['category']) : '';
                $displayOrder = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
                
                $stmt = $conn->prepare("INSERT INTO gallery_images (filename, original_filename, alt_text, category, display_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $newFilename, $originalFilename, $altText, $category, $displayOrder);
                
                if ($stmt->execute()) {
                    $alert = '<div class="alert alert-success">Image uploaded successfully!</div>';
                } else {
                    $alert = '<div class="alert alert-error">Error saving image to database: ' . $conn->error . '</div>';
                    // Delete uploaded file if database insert failed
                    unlink($uploadPath);
                }
                $stmt->close();
            } else {
                $alert = '<div class="alert alert-error">Error uploading file.</div>';
            }
        } else {
            $alert = '<div class="alert alert-error">Invalid file type. Allowed: ' . implode(', ', $allowedExts) . '</div>';
        }
    } else {
        $alert = '<div class="alert alert-error">Upload error: ' . $fileError . '</div>';
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get filename before deleting
    $stmt = $conn->prepare("SELECT filename FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filename = $row['filename'];
        $filePath = 'images/uploads/' . $filename;
        
        // Delete from database
        $deleteStmt = $conn->prepare("DELETE FROM gallery_images WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        
        if ($deleteStmt->execute()) {
            // Delete file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $alert = '<div class="alert alert-success">Image deleted successfully!</div>';
        } else {
            $alert = '<div class="alert alert-error">Error deleting image.</div>';
        }
        $deleteStmt->close();
    }
    $stmt->close();
}

// Fetch all uploaded images
$result = $conn->query("SELECT * FROM gallery_images ORDER BY display_order ASC, upload_date DESC");
if ($result) {
    $images = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Images | Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-header {
            background-color: var(--primary-navy);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: var(--primary-navy);
            color: white;
        }
        .btn-primary:hover {
            background-color: #081c4f;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: var(--divider-grey);
            color: var(--text-charcoal);
        }
        .form-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-charcoal);
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--divider-grey);
            border-radius: 4px;
            font-size: 0.95rem;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .image-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .image-card-info {
            padding: 1rem;
        }
        .image-card-info h4 {
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
            color: var(--text-charcoal);
        }
        .image-card-info p {
            margin: 0.25rem 0;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        .image-actions {
            padding: 0.75rem;
            border-top: 1px solid var(--divider-grey);
            display: flex;
            gap: 0.5rem;
        }
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--divider-grey);
        }
        .tab {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-light);
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
        }
        .tab.active {
            color: var(--primary-navy);
            border-bottom-color: var(--primary-navy);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h2>Admin Dashboard - Image Management</h2>
        <div>
            <a href="admin-dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="admin-login.php?logout=1" class="btn btn-secondary">Logout</a>
        </div>
    </div>

    <div class="admin-content">
        <?php echo $alert; ?>
        
        <div class="dashboard-header">
            <h1>Gallery Image Management</h1>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="switchTab('upload')">Upload Image</button>
            <button class="tab" onclick="switchTab('manage')">Manage Images (<?php echo count($images); ?>)</button>
        </div>

        <div id="upload-tab" class="tab-content active">
            <div class="form-section">
                <h2>Upload New Image</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image">Image File *</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
                        <small style="color: var(--text-light);">Max file size: 10MB. Allowed formats: JPG, PNG, GIF, WEBP</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="alt_text">Alt Text (for accessibility)</label>
                        <input type="text" id="alt_text" name="alt_text" placeholder="Describe the image">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="">None</option>
                            <option value="Event Gallery">Event Gallery</option>
                            <option value="Panel Discussions">Panel Discussions</option>
                            <option value="Networking">Networking</option>
                            <option value="Keynote">Keynote</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" value="0" min="0">
                        <small style="color: var(--text-light);">Lower numbers appear first</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Upload Image</button>
                </form>
            </div>
        </div>

        <div id="manage-tab" class="tab-content">
            <?php if (empty($images)): ?>
                <div class="form-section">
                    <p>No images uploaded yet. Use the "Upload Image" tab to add images.</p>
                </div>
            <?php else: ?>
                <div class="images-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="image-card">
                            <img src="<?php echo htmlspecialchars('images/uploads/' . $image['filename']); ?>" alt="<?php echo htmlspecialchars($image['alt_text'] ?: 'Gallery image'); ?>">
                            <div class="image-card-info">
                                <h4><?php echo htmlspecialchars($image['original_filename']); ?></h4>
                                <?php if ($image['alt_text']): ?>
                                    <p><strong>Alt:</strong> <?php echo htmlspecialchars($image['alt_text']); ?></p>
                                <?php endif; ?>
                                <?php if ($image['category']): ?>
                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($image['category']); ?></p>
                                <?php endif; ?>
                                <p><strong>Order:</strong> <?php echo $image['display_order']; ?></p>
                                <p><strong>Uploaded:</strong> <?php echo date('M d, Y', strtotime($image['upload_date'])); ?></p>
                            </div>
                            <div class="image-actions">
                                <a href="?delete=<?php echo $image['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>


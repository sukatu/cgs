<?php
require_once 'config.php';
requireAdminLogin();

$conn = getDBConnection();
$events = [];
$images = [];
$alert = '';

// Handle delete event
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && !isset($_GET['type'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $alert = '<div class="alert alert-success">Event deleted successfully!</div>';
    } else {
        $alert = '<div class="alert alert-error">Error deleting event.</div>';
    }
}

// Handle confirm in-person registration
if (isset($_GET['confirm_registration']) && is_numeric($_GET['confirm_registration'])) {
    $id = intval($_GET['confirm_registration']);
    $tableCheck = $conn->query("SHOW TABLES LIKE 'in_person_registrations'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE in_person_registrations SET status = 'confirmed' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $alert = '<div class="alert alert-success">In-person registration confirmed successfully!</div>';
        } else {
            $alert = '<div class="alert alert-error">Error confirming registration.</div>';
        }
        $stmt->close();
    }
}

// Handle confirm online registration
if (isset($_GET['confirm_online_registration']) && is_numeric($_GET['confirm_online_registration'])) {
    $id = intval($_GET['confirm_online_registration']);
    $tableCheck = $conn->query("SHOW TABLES LIKE 'event_registrations'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE event_registrations SET status = 'confirmed' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $alert = '<div class="alert alert-success">Online registration confirmed successfully!</div>';
        } else {
            $alert = '<div class="alert alert-error">Error confirming registration.</div>';
        }
        $stmt->close();
    }
}

// Handle delete in-person registration
if (isset($_GET['delete_inperson_reg']) && is_numeric($_GET['delete_inperson_reg'])) {
    $id = intval($_GET['delete_inperson_reg']);
    $tableCheck = $conn->query("SHOW TABLES LIKE 'in_person_registrations'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM in_person_registrations WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $alert = '<div class="alert alert-success">In-person registration deleted successfully!</div>';
        } else {
            $alert = '<div class="alert alert-error">Error deleting registration.</div>';
        }
        $stmt->close();
    }
}

// Handle delete image
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && isset($_GET['type']) && $_GET['type'] === 'image') {
    $id = intval($_GET['delete']);
    
    $tableCheck = $conn->query("SHOW TABLES LIKE 'gallery_images'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
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
    } else {
        $alert = '<div class="alert alert-error">Gallery images table does not exist.</div>';
    }
}

// Fetch events
$result = $conn->query("SELECT * FROM events ORDER BY event_date DESC, created_at DESC");
if ($result) {
    $events = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch uploaded images from database
$images = [];
$tableCheck = $conn->query("SHOW TABLES LIKE 'gallery_images'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $imagesResult = $conn->query("SELECT * FROM gallery_images ORDER BY display_order ASC, upload_date DESC");
    if ($imagesResult) {
        $images = $imagesResult->fetch_all(MYSQLI_ASSOC);
    }
} else {
    // Create gallery_images table if it doesn't exist
    $createTableSql = "CREATE TABLE IF NOT EXISTS gallery_images (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        original_filename VARCHAR(255),
        alt_text VARCHAR(500),
        category VARCHAR(100),
        display_order INT(11) DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_category (category),
        INDEX idx_display_order (display_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($createTableSql)) {
        // Table created, images array remains empty
    } else {
        error_log("Error creating gallery_images table: " . $conn->error);
    }
}

// Fetch in-person registrations
$inPersonRegistrations = [];
$tableCheck = $conn->query("SHOW TABLES LIKE 'in_person_registrations'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $registrationsResult = $conn->query("SELECT * FROM in_person_registrations ORDER BY registration_date DESC");
    if ($registrationsResult) {
        $inPersonRegistrations = $registrationsResult->fetch_all(MYSQLI_ASSOC);
    }
}

// Fetch online registrations (from event_registrations table)
$onlineRegistrations = [];
$tableCheck = $conn->query("SHOW TABLES LIKE 'event_registrations'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $onlineRegQuery = "SELECT 
        er.id,
        er.registration_date,
        er.status,
        er.notes,
        u.id as user_id,
        u.name as user_name,
        u.email as user_email,
        u.phone as user_phone,
        u.organization as user_organization,
        e.id as event_id,
        e.title as event_title,
        e.event_date as event_date
    FROM event_registrations er
    LEFT JOIN users u ON er.user_id = u.id
    LEFT JOIN events e ON er.event_id = e.id
    ORDER BY er.registration_date DESC";
    
    $onlineRegResult = $conn->query($onlineRegQuery);
    if ($onlineRegResult) {
        $onlineRegistrations = $onlineRegResult->fetch_all(MYSQLI_ASSOC);
    }
}

// Get static gallery images from images/gallery/New folder
$staticImages = [];
$galleryPath = 'images/gallery/New/';
if (is_dir($galleryPath)) {
    $files = scandir($galleryPath);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $staticImages[] = [
                    'id' => 'static_' . $file,
                    'filename' => $file,
                    'original_filename' => $file,
                    'alt_text' => 'CGS Event Photo',
                    'category' => 'Static Gallery',
                    'upload_date' => date('Y-m-d H:i:s', filemtime($galleryPath . $file)),
                    'display_order' => 9999,
                    'is_active' => 1,
                    'is_static' => true
                ];
            }
        }
    }
    // Sort static images by filename number if they follow the pattern
    usort($staticImages, function($a, $b) {
        preg_match('/Fredan-(\d+)/i', $a['filename'], $matchesA);
        preg_match('/Fredan-(\d+)/i', $b['filename'], $matchesB);
        if (isset($matchesA[1]) && isset($matchesB[1])) {
            return intval($matchesA[1]) - intval($matchesB[1]);
        }
        return strcmp($a['filename'], $b['filename']);
    });
}

// Combine uploaded and static images (static images first to show them together)
$allImages = array_merge($staticImages, $images);
$totalImageCount = count($allImages);

if (isset($_GET['success'])) {
    $alert = '<div class="alert alert-success">Event saved successfully!</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CGS</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-header {
            background-color: var(--primary-navy);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .admin-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        .admin-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
            box-sizing: border-box;
        }
        #usersSection,
        #registrationsSection,
        #onlineRegistrationsSection {
            max-width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }
        #usersSection > div,
        #registrationsSection > div,
        #onlineRegistrationsSection > div {
            max-width: 100%;
            box-sizing: border-box;
        }
        #usersSection .card,
        #registrationsSection .card,
        #onlineRegistrationsSection .card {
            max-width: 100%;
            box-sizing: border-box;
        }
        #usersSection table,
        #registrationsSection table,
        #onlineRegistrationsSection table {
            max-width: 100%;
            box-sizing: border-box;
        }
        @media (max-width: 768px) {
            .admin-header {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
            }
            .admin-header h2 {
                font-size: 1.25rem;
            }
            .admin-content {
                padding: 1rem;
            }
        }
        @media (max-width: 600px) {
            .admin-header {
                padding: 0.75rem;
            }
            .admin-header h2 {
                font-size: 1.1rem;
            }
            .admin-content {
                padding: 0.75rem;
            }
        }
        
        .images-grid-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }
        
        @media (max-width: 768px) {
            .images-grid-container {
                padding: 0 15px;
            }
        }
        
        @media (max-width: 600px) {
            .images-grid-container {
                padding: 0 10px;
            }
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: stretch;
            }
            .dashboard-header > div {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .dashboard-header .btn {
                flex: 1;
                min-width: calc(50% - 0.25rem);
                font-size: 0.85rem;
                padding: 0.625rem 1rem;
            }
        }
        @media (max-width: 600px) {
            .dashboard-header .btn {
                min-width: 100%;
                font-size: 0.8rem;
                padding: 0.5rem 0.75rem;
            }
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
        .btn-secondary:hover {
            background-color: #d1d5db;
        }
        .events-table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: var(--shadow);
            border-radius: 8px;
            overflow: hidden;
        }
        .events-table th,
        .events-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--divider-grey);
        }
        .events-table th {
            background-color: var(--bg-offwhite);
            font-weight: 600;
            color: var(--primary-navy);
            white-space: nowrap;
        }
        .events-table td {
            white-space: normal;
        }
        .events-table td:first-child,
        .events-table th:first-child {
            white-space: nowrap;
        }
        .events-table tr:hover {
            background-color: var(--bg-offwhite);
        }
        @media (max-width: 768px) {
            .events-table {
                font-size: 0.9rem;
            }
            .events-table th,
            .events-table td {
                padding: 0.75rem 0.5rem;
            }
        }
        @media (max-width: 600px) {
            .events-table {
                font-size: 0.85rem;
            }
            .events-table th,
            .events-table td {
                padding: 0.5rem;
            }
        }
        /* Section headers responsive */
        #registrationsSection > div:first-child,
        #onlineRegistrationsSection > div:first-child {
            margin-bottom: 1.5rem;
        }
        #registrationsSection h2,
        #onlineRegistrationsSection h2 {
            font-size: 1.5rem;
        }
        @media (max-width: 768px) {
            #registrationsSection h2,
            #onlineRegistrationsSection h2 {
                font-size: 1.25rem;
            }
        }
        @media (max-width: 600px) {
            #registrationsSection h2,
            #onlineRegistrationsSection h2 {
                font-size: 1.1rem;
            }
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-upcoming {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .status-completed {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        .status-cancelled {
            background-color: #ffebee;
            color: #d32f2f;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-charcoal);
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--divider-grey);
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-navy);
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
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
    </style>
</head>
<body>
    
    <div class="admin-header">
        <h2>CGS Admin Dashboard</h2>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="admin-login.php?logout=1" class="btn btn-secondary" style="margin-left: 1rem; background: rgba(255,255,255,0.2); color: white;">Logout</a>
        </div>
    </div>
    
    <div class="admin-content">
        <?php echo $alert; ?>
        
        <div class="dashboard-header">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <button class="btn btn-primary" onclick="showSection('events')" id="btnEvents">Events</button>
                <button class="btn btn-secondary" onclick="showSection('users')" id="btnUsers">Users</button>
                <button class="btn btn-secondary" onclick="showSection('onlineRegistrations')" id="btnOnlineRegistrations">Online Registrations (<?php echo count($onlineRegistrations); ?>)</button>
                <button class="btn btn-secondary" onclick="showSection('registrations')" id="btnRegistrations">In-Person Registrations (<?php echo count($inPersonRegistrations); ?>)</button>
                <button class="btn btn-secondary" onclick="showSection('images')" id="btnImages">Images (<?php echo $totalImageCount; ?>)</button>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="admin-upload-image.php" class="btn btn-primary">+ Upload Image</a>
                <button class="btn btn-primary" onclick="openModal('eventModal')">+ Add New Event</button>
            </div>
        </div>
        
        <!-- Events Section -->
        <div id="eventsSection">
        
        <div style="overflow-x: auto;">
            <table class="events-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                No events found. Click "Add New Event" to create one.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo $event['id']; ?></td>
                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                <td><?php echo ucfirst($event['event_type']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($event['event_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $event['status']; ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?edit=<?php echo $event['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Edit</a>
                                    <a href="?delete=<?php echo $event['id']; ?>" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div>
        
        <!-- Users Section -->
        <div id="usersSection" style="display: none;">
            <div style="margin-bottom: 2rem;">
                <h2 style="margin-bottom: 0.5rem;">User Management</h2>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <p style="color: var(--text-light); margin: 0;">
                        Total Users: <strong id="totalUsersCount"><?php 
                            $userCountStmt = $conn->query("SELECT COUNT(*) as count FROM users");
                            $userCount = $userCountStmt ? $userCountStmt->fetch_assoc()['count'] : 0;
                            echo $userCount;
                        ?></strong>
                        | Showing: <strong id="showingCount"><?php echo $userCount; ?></strong>
                    </p>
                    <button class="btn btn-secondary" onclick="exportUsers()" style="padding: 0.625rem 1.25rem; font-size: 0.9rem;">
                        📥 Export Users
                    </button>
                </div>
            </div>
            
            <!-- Advanced Search and Filter Panel -->
            <div class="card" style="padding: 1.5rem; margin-bottom: 2rem; background: var(--white); box-shadow: var(--shadow);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.25rem; color: var(--primary-navy); margin: 0;">Search & Filter</h3>
                    <button class="btn btn-secondary" onclick="resetFilters()" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Reset</button>
                </div>
                
                <!-- Search Bar -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal);">Search Users</label>
                    <input type="text" id="userSearchInput" placeholder="Search by name, email, organization..." 
                           style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 1rem;"
                           onkeyup="filterUsers()">
                </div>
                
                <!-- Filters Row -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal); font-size: 0.9rem;">Filter by Role</label>
                        <select id="filterRole" onchange="filterUsers()" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 0.95rem;">
                            <option value="">All Roles</option>
                            <option value="lawyer">Lawyer</option>
                            <option value="banker">Banker</option>
                            <option value="board">Board Member/Director</option>
                            <option value="student">Student</option>
                            <option value="regulator">Regulator</option>
                            <option value="consultant">Consultant</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal); font-size: 0.9rem;">Filter by Country</label>
                        <select id="filterCountry" onchange="filterUsers()" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 0.95rem;">
                            <option value="">All Countries</option>
                            <?php
                            $countriesResult = $conn->query("SELECT DISTINCT country FROM users WHERE country IS NOT NULL AND country != '' ORDER BY country");
                            if ($countriesResult) {
                                while ($row = $countriesResult->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row['country']) . '">' . htmlspecialchars($row['country']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal); font-size: 0.9rem;">Filter by Registrations</label>
                        <select id="filterRegistrations" onchange="filterUsers()" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 0.95rem;">
                            <option value="">All Users</option>
                            <option value="0">No Registrations</option>
                            <option value="1+">Has Registrations</option>
                            <option value="5+">5+ Registrations</option>
                            <option value="10+">10+ Registrations</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal); font-size: 0.9rem;">Sort By</label>
                        <select id="sortUsers" onchange="filterUsers()" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 0.95rem;">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="name-asc">Name (A-Z)</option>
                            <option value="name-desc">Name (Z-A)</option>
                            <option value="registrations-desc">Most Registrations</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <?php
            // Get all users with registration counts
            $usersResult = $conn->query("SELECT u.*, 
                (SELECT COUNT(*) FROM event_registrations WHERE user_id = u.id) as total_registrations
                FROM users u 
                ORDER BY u.created_at DESC");
            $users = $usersResult ? $usersResult->fetch_all(MYSQLI_ASSOC) : [];
            ?>
            
            <div style="overflow-x: auto;">
                <table class="events-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Organization</th>
                            <th>Role</th>
                            <th>Location</th>
                            <th>Registrations</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                    No users registered yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr class="user-row" 
                                    data-name="<?php echo strtolower(htmlspecialchars($user['name'])); ?>"
                                    data-email="<?php echo strtolower(htmlspecialchars($user['email'])); ?>"
                                    data-organization="<?php echo strtolower(htmlspecialchars($user['organization'] ?? '')); ?>"
                                    data-role="<?php echo strtolower(htmlspecialchars($user['role'] ?? '')); ?>"
                                    data-country="<?php echo strtolower(htmlspecialchars($user['country'] ?? '')); ?>"
                                    data-registrations="<?php echo $user['total_registrations']; ?>"
                                    data-joined="<?php echo strtotime($user['created_at']); ?>">
                                    <td><?php echo $user['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['organization'] ?? '-'); ?></td>
                                    <td>
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: var(--bg-offwhite); border-radius: 12px; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars(ucfirst($user['role'] ?? '-')); ?>
                                        </span>
                                    </td>
                                    <td><?php 
                                        $location = [];
                                        if ($user['city']) $location[] = $user['city'];
                                        if ($user['country']) $location[] = $user['country'];
                                        echo htmlspecialchars(implode(', ', $location) ?: '-');
                                    ?></td>
                                    <td>
                                        <strong style="color: <?php echo $user['total_registrations'] > 0 ? 'var(--primary-navy)' : 'var(--text-light)'; ?>;">
                                            <?php echo $user['total_registrations']; ?>
                                        </strong>
                                        <?php if ($user['total_registrations'] > 0): ?>
                                            <a href="?view_registrations=<?php echo $user['id']; ?>" style="margin-left: 0.5rem; color: var(--primary-navy); font-size: 0.85rem;">View</a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="?view_user=<?php echo $user['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- In-Person Registrations Section -->
    <div id="registrationsSection" style="display: none;">
        <div style="margin-bottom: 2rem;">
            <h2 style="margin-bottom: 0.5rem;">In-Person Event Registrations</h2>
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <p style="color: var(--text-light); margin: 0;">
                    Total Registrations: <strong id="totalInPersonCount"><?php echo count($inPersonRegistrations); ?></strong>
                    | Showing: <strong id="showingInPersonCount"><?php echo count($inPersonRegistrations); ?></strong>
                </p>
            </div>
        </div>
        
        <!-- Advanced Search and Filter Panel -->
        <div class="card" style="padding: 1.5rem; margin-bottom: 2rem; background: var(--white); box-shadow: var(--shadow);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.25rem; color: var(--primary-navy); margin: 0;">Search & Filter</h3>
                <button class="btn btn-secondary" onclick="resetInPersonFilters()" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Reset</button>
            </div>
            
            <!-- Search Bar -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal);">Search Registrations</label>
                <input type="text" id="inPersonSearchInput" placeholder="Search by name, email, institution..." 
                       style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 1rem;"
                       onkeyup="filterInPersonRegistrations()">
            </div>
            
            <!-- Filters Row -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal); font-size: 0.9rem;">Filter by Status</label>
                    <select id="filterInPersonStatus" onchange="filterInPersonRegistrations()" 
                            style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 0.95rem;">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal); font-size: 0.9rem;">Sort By</label>
                    <select id="sortInPerson" onchange="filterInPersonRegistrations()" 
                            style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 0.95rem;">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="events-table" id="inPersonTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Institution/Firm</th>
                        <th>Address</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inPersonRegistrations)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                No in-person registrations yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inPersonRegistrations as $reg): ?>
                            <tr class="inperson-row" 
                                data-name="<?php echo strtolower(htmlspecialchars($reg['full_name'])); ?>"
                                data-email="<?php echo strtolower(htmlspecialchars($reg['email'])); ?>"
                                data-institution="<?php echo strtolower(htmlspecialchars($reg['institution_firm'] ?? '')); ?>"
                                data-status="<?php echo strtolower($reg['status']); ?>"
                                data-date="<?php echo strtotime($reg['registration_date']); ?>">
                                <td><?php echo $reg['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($reg['full_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($reg['email']); ?></td>
                                <td><?php echo htmlspecialchars($reg['phone']); ?></td>
                                <td><?php echo htmlspecialchars($reg['institution_firm']); ?></td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($reg['address']); ?>">
                                    <?php echo htmlspecialchars($reg['address']); ?>
                                </td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($reg['event_title'] ?? 'N/A'); ?>">
                                    <?php echo htmlspecialchars($reg['event_title'] ?? 'N/A'); ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $reg['status']; ?>">
                                        <?php echo ucfirst($reg['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($reg['registration_date'])); ?></td>
                                <td>
                                    <a href="?view_registration=<?php echo $reg['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View</a>
                                    <?php if ($reg['status'] === 'pending'): ?>
                                        <a href="?confirm_registration=<?php echo $reg['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="return confirm('Confirm this registration?')">Confirm</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Online Registrations Section -->
    <div id="onlineRegistrationsSection" style="display: none;">
        <div style="margin-bottom: 2rem;">
            <h2 style="margin-bottom: 0.5rem;">Online Event Registrations</h2>
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <p style="color: var(--text-light); margin: 0;">
                    Total Online Registrations: <strong id="totalOnlineCount"><?php echo count($onlineRegistrations); ?></strong>
                    | Showing: <strong id="showingOnlineCount"><?php echo count($onlineRegistrations); ?></strong>
                </p>
            </div>
        </div>
        
        <!-- Advanced Search and Filter Panel -->
        <div class="card" style="padding: 1.5rem; margin-bottom: 2rem; background: var(--white); box-shadow: var(--shadow);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.25rem; color: var(--primary-navy); margin: 0;">Search & Filter</h3>
                <button class="btn btn-secondary" onclick="resetOnlineFilters()" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Reset</button>
            </div>
            
            <!-- Search Bar -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal);">Search Registrations</label>
                <input type="text" id="onlineSearchInput" placeholder="Search by name, email, organization..." 
                       style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 1rem;"
                       onkeyup="filterOnlineRegistrations()">
            </div>
            
            <!-- Filters Row -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal); font-size: 0.9rem;">Filter by Status</label>
                    <select id="filterOnlineStatus" onchange="filterOnlineRegistrations()" 
                            style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 0.95rem;">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-charcoal); font-size: 0.9rem;">Sort By</label>
                    <select id="sortOnline" onchange="filterOnlineRegistrations()" 
                            style="width: 100%; padding: 0.75rem; border: 2px solid var(--divider-grey); border-radius: 4px; font-size: 0.95rem;">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="events-table" id="onlineTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Organization</th>
                        <th>Event</th>
                        <th>Event Date</th>
                        <th>Status</th>
                        <th>Registration Date</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($onlineRegistrations)): ?>
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                No online registrations yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($onlineRegistrations as $reg): ?>
                            <tr class="online-row" 
                                data-name="<?php echo strtolower(htmlspecialchars($reg['user_name'] ?? '')); ?>"
                                data-email="<?php echo strtolower(htmlspecialchars($reg['user_email'] ?? '')); ?>"
                                data-organization="<?php echo strtolower(htmlspecialchars($reg['user_organization'] ?? '')); ?>"
                                data-status="<?php echo strtolower($reg['status']); ?>"
                                data-date="<?php echo strtotime($reg['registration_date']); ?>">
                                <td><?php echo $reg['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($reg['user_name'] ?? 'N/A'); ?></strong></td>
                                <td><?php echo htmlspecialchars($reg['user_email'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($reg['user_phone'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($reg['user_organization'] ?? 'N/A'); ?></td>
                                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($reg['event_title'] ?? 'N/A'); ?>">
                                    <?php echo htmlspecialchars($reg['event_title'] ?? 'N/A'); ?>
                                </td>
                                <td>
                                    <?php if ($reg['event_date']): ?>
                                        <?php echo date('M d, Y H:i', strtotime($reg['event_date'])); ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $reg['status']; ?>">
                                        <?php echo ucfirst($reg['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($reg['registration_date'])); ?></td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($reg['notes'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($reg['notes'] ?? ''); ?>
                                </td>
                                <td>
                                    <a href="?view_online_registration=<?php echo $reg['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View</a>
                                    <?php if ($reg['status'] === 'pending'): ?>
                                        <a href="?confirm_online_registration=<?php echo $reg['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="return confirm('Confirm this registration?')">Confirm</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add/Edit Event Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Add New Event</h2>
            <button onclick="closeModal('eventModal')" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-charcoal);">&times;</button>
            <form id="eventForm" method="POST" action="admin-save-event.php">
                <input type="hidden" name="event_id" id="event_id">
                
                <div class="form-group">
                    <label for="title">Event Title *</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="event_type">Event Type *</label>
                    <select id="event_type" name="event_type" required>
                        <option value="webinar">Webinar</option>
                        <option value="series">Series</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="event_date">Event Date & Time *</label>
                    <input type="datetime-local" id="event_date" name="event_date" required>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location">
                </div>
                
                <div class="form-group">
                    <label for="format">Format</label>
                    <select id="format" name="format">
                        <option value="online">Online</option>
                        <option value="hybrid">Hybrid</option>
                        <option value="in-person">In-Person</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="registration_link">Registration Link</label>
                    <input type="url" id="registration_link" name="registration_link">
                </div>
                
                <div class="form-group">
                    <label for="youtube_url">YouTube URL</label>
                    <input type="url" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                
                <div class="form-group">
                    <label for="speakers">Speakers (comma-separated)</label>
                    <input type="text" id="speakers" name="speakers">
                </div>
                
                <div class="form-group">
                    <label for="moderator">Moderator</label>
                    <input type="text" id="moderator" name="moderator">
                </div>
                
                <div class="form-group">
                    <label for="agenda">Agenda</label>
                    <textarea id="agenda" name="agenda"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="summary">Summary</label>
                    <textarea id="summary" name="summary"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="tags">Tags (comma-separated)</label>
                    <input type="text" id="tags" name="tags">
                </div>
                
                <div class="form-group">
                    <label for="countries">Countries (comma-separated)</label>
                    <input type="text" id="countries" name="countries">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="upcoming">Upcoming</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('eventModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
        </div>
        </div>
        
        <!-- Images Section -->
        <div id="imagesSection" style="display: none;">
            <div style="margin-bottom: 2rem; max-width: 1400px; margin-left: auto; margin-right: auto;">
                <h2 style="margin-bottom: 0.5rem; text-align: center; color: var(--primary-navy);">Image Management</h2>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
                    <p style="color: var(--text-light); margin: 0;">
                        Total Images: <strong><?php echo $totalImageCount; ?></strong>
                        (<?php echo count($staticImages); ?> Static + <?php echo count($images); ?> Uploaded)
                    </p>
                    <a href="admin-upload-image.php" class="btn btn-primary">+ Upload New Image</a>
                </div>
            </div>
            
            <?php if (empty($allImages)): ?>
                <div style="text-align: center; padding: 3rem; background: white; border-radius: 8px; box-shadow: var(--shadow); max-width: 600px; margin: 0 auto;">
                    <p style="color: var(--text-light); margin-bottom: 1.5rem;">No images found.</p>
                    <a href="admin-upload-image.php" class="btn btn-primary">Upload Your First Image</a>
                </div>
            <?php else: ?>
                <div class="images-grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; justify-content: center;">
                    <?php foreach ($allImages as $image): 
                        $isStatic = isset($image['is_static']) && $image['is_static'];
                        $imagePath = $isStatic ? 'images/gallery/New/' . $image['filename'] : 'images/uploads/' . $image['filename'];
                    ?>
                        <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow); <?php echo $isStatic ? 'border: 2px solid var(--accent-gold);' : ''; ?>">
                            <div style="width: 100%; height: 200px; overflow: hidden; background: var(--bg-offwhite); position: relative;">
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                     alt="<?php echo htmlspecialchars($image['alt_text'] ?: $image['original_filename']); ?>"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'250\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'250\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EImage%3C/text%3E%3C/svg%3E';">
                                <?php if ($isStatic): ?>
                                    <span style="position: absolute; top: 0.5rem; right: 0.5rem; background: var(--accent-gold); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">STATIC</span>
                                <?php endif; ?>
                            </div>
                            <div style="padding: 1rem;">
                                <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem; color: var(--text-charcoal); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo htmlspecialchars($image['original_filename']); ?>
                                </h4>
                                <?php if ($image['alt_text'] && !$isStatic): ?>
                                    <p style="margin: 0.25rem 0; font-size: 0.85rem; color: var(--text-light); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars($image['alt_text']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($image['category']): ?>
                                    <p style="margin: 0.25rem 0; font-size: 0.85rem; color: var(--accent-gold);">
                                        <strong>Category:</strong> <?php echo htmlspecialchars($image['category']); ?>
                                    </p>
                                <?php endif; ?>
                                <p style="margin: 0.25rem 0; font-size: 0.8rem; color: var(--text-light);">
                                    <?php if (!$isStatic): ?>
                                        <strong>Order:</strong> <?php echo $image['display_order']; ?> | 
                                    <?php endif; ?>
                                    <strong><?php echo $isStatic ? 'Modified' : 'Uploaded'; ?>:</strong> <?php echo date('M d, Y', strtotime($image['upload_date'])); ?>
                                </p>
                            </div>
                            <div style="padding: 0.75rem; border-top: 1px solid var(--divider-grey); display: flex; gap: 0.5rem;">
                                <?php if (!$isStatic): ?>
                                    <a href="?delete=<?php echo $image['id']; ?>&type=image" 
                                       class="btn btn-danger" 
                                       style="padding: 0.5rem 1rem; font-size: 0.85rem; flex: 1; text-align: center;"
                                       onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                                <?php else: ?>
                                    <span style="padding: 0.5rem 1rem; font-size: 0.85rem; flex: 1; text-align: center; color: var(--text-light); font-style: italic;">
                                        Static (read-only)
                                    </span>
                                <?php endif; ?>
                                <a href="<?php echo htmlspecialchars($imagePath); ?>" 
                                   target="_blank"
                                   class="btn btn-secondary" 
                                   style="padding: 0.5rem 1rem; font-size: 0.85rem; flex: 1; text-align: center;">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.getElementById('eventForm').reset();
            document.getElementById('event_id').value = '';
            document.getElementById('modalTitle').textContent = 'Add New Event';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        // Handle edit
        <?php if (isset($_GET['edit']) && is_numeric($_GET['edit'])): ?>
            <?php
            $editId = intval($_GET['edit']);
            $editStmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
            $editStmt->bind_param("i", $editId);
            $editStmt->execute();
            $editResult = $editStmt->get_result();
            if ($editResult->num_rows === 1) {
                $editEvent = $editResult->fetch_assoc();
            ?>
                document.addEventListener('DOMContentLoaded', function() {
                    const event = <?php echo json_encode($editEvent); ?>;
                    document.getElementById('event_id').value = event.id;
                    document.getElementById('title').value = event.title || '';
                    document.getElementById('description').value = event.description || '';
                    document.getElementById('event_type').value = event.event_type || 'webinar';
                    document.getElementById('event_date').value = event.event_date ? event.event_date.replace(' ', 'T').substring(0, 16) : '';
                    document.getElementById('location').value = event.location || '';
                    document.getElementById('format').value = event.format || 'online';
                    document.getElementById('registration_link').value = event.registration_link || '';
                    document.getElementById('youtube_url').value = event.youtube_url || '';
                    document.getElementById('speakers').value = event.speakers || '';
                    document.getElementById('moderator').value = event.moderator || '';
                    document.getElementById('agenda').value = event.agenda || '';
                    document.getElementById('summary').value = event.summary || '';
                    document.getElementById('tags').value = event.tags || '';
                    document.getElementById('countries').value = event.countries || '';
                    document.getElementById('status').value = event.status || 'upcoming';
                    document.getElementById('modalTitle').textContent = 'Edit Event';
                    openModal('eventModal');
                });
            <?php
            }
            ?>
        <?php endif; ?>
        
        // Close modal on outside click
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.classList.remove('active');
                }
            });
        }
        
        // Toggle between Events and Users sections
        function showSection(section) {
            // Hide all sections
            document.getElementById('eventsSection').style.display = 'none';
            document.getElementById('usersSection').style.display = 'none';
            document.getElementById('onlineRegistrationsSection').style.display = 'none';
            document.getElementById('registrationsSection').style.display = 'none';
            document.getElementById('imagesSection').style.display = 'none';
            
            // Reset all buttons
            document.getElementById('btnEvents').classList.remove('btn-primary');
            document.getElementById('btnEvents').classList.add('btn-secondary');
            document.getElementById('btnUsers').classList.remove('btn-primary');
            document.getElementById('btnUsers').classList.add('btn-secondary');
            document.getElementById('btnOnlineRegistrations').classList.remove('btn-primary');
            document.getElementById('btnOnlineRegistrations').classList.add('btn-secondary');
            document.getElementById('btnRegistrations').classList.remove('btn-primary');
            document.getElementById('btnRegistrations').classList.add('btn-secondary');
            document.getElementById('btnImages').classList.remove('btn-primary');
            document.getElementById('btnImages').classList.add('btn-secondary');
            
            // Show selected section
            if (section === 'events') {
                document.getElementById('eventsSection').style.display = 'block';
                document.getElementById('btnEvents').classList.add('btn-primary');
                document.getElementById('btnEvents').classList.remove('btn-secondary');
            } else if (section === 'users') {
                document.getElementById('usersSection').style.display = 'block';
                document.getElementById('btnUsers').classList.add('btn-primary');
                document.getElementById('btnUsers').classList.remove('btn-secondary');
            } else if (section === 'onlineRegistrations') {
                document.getElementById('onlineRegistrationsSection').style.display = 'block';
                document.getElementById('btnOnlineRegistrations').classList.add('btn-primary');
                document.getElementById('btnOnlineRegistrations').classList.remove('btn-secondary');
            } else if (section === 'registrations') {
                document.getElementById('registrationsSection').style.display = 'block';
                document.getElementById('btnRegistrations').classList.add('btn-primary');
                document.getElementById('btnRegistrations').classList.remove('btn-secondary');
            } else if (section === 'images') {
                document.getElementById('imagesSection').style.display = 'block';
                document.getElementById('btnImages').classList.add('btn-primary');
                document.getElementById('btnImages').classList.remove('btn-secondary');
            }
        }
        
        // Show user details
        function showUserDetails(user) {
            alert('User Details:\n\n' +
                  'Name: ' + user.name + '\n' +
                  'Email: ' + user.email + '\n' +
                  'Organization: ' + (user.organization || 'N/A') + '\n' +
                  'Role: ' + (user.role || 'N/A') + '\n' +
                  'Location: ' + (user.city || '') + ', ' + (user.country || '') + '\n' +
                  'Phone: ' + (user.phone || 'N/A') + '\n' +
                  'Joined: ' + new Date(user.created_at).toLocaleDateString());
        }
        
        // Advanced User Filtering
        function filterUsers() {
            const searchTerm = document.getElementById('userSearchInput').value.toLowerCase();
            const roleFilter = document.getElementById('filterRole').value.toLowerCase();
            const countryFilter = document.getElementById('filterCountry').value.toLowerCase();
            const registrationsFilter = document.getElementById('filterRegistrations').value;
            const sortBy = document.getElementById('sortUsers').value;
            
            const rows = document.querySelectorAll('.user-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const organization = row.dataset.organization || '';
                const role = row.dataset.role || '';
                const country = row.dataset.country || '';
                const registrations = parseInt(row.dataset.registrations) || 0;
                
                // Search filter
                const matchesSearch = !searchTerm || 
                    name.includes(searchTerm) || 
                    email.includes(searchTerm) || 
                    organization.includes(searchTerm);
                
                // Role filter
                const matchesRole = !roleFilter || role === roleFilter;
                
                // Country filter
                const matchesCountry = !countryFilter || country === countryFilter;
                
                // Registrations filter
                let matchesRegistrations = true;
                if (registrationsFilter === '0') {
                    matchesRegistrations = registrations === 0;
                } else if (registrationsFilter === '1+') {
                    matchesRegistrations = registrations >= 1;
                } else if (registrationsFilter === '5+') {
                    matchesRegistrations = registrations >= 5;
                } else if (registrationsFilter === '10+') {
                    matchesRegistrations = registrations >= 10;
                }
                
                if (matchesSearch && matchesRole && matchesCountry && matchesRegistrations) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update showing count
            document.getElementById('showingCount').textContent = visibleCount;
            
            // Sort users
            sortUserRows(sortBy);
        }
        
        // Sort user rows
        function sortUserRows(sortBy) {
            const table = document.getElementById('usersTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('.user-row:not([style*="display: none"])'));
            
            rows.sort((a, b) => {
                switch(sortBy) {
                    case 'newest':
                        return parseInt(b.dataset.joined) - parseInt(a.dataset.joined);
                    case 'oldest':
                        return parseInt(a.dataset.joined) - parseInt(b.dataset.joined);
                    case 'name-asc':
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'name-desc':
                        return b.dataset.name.localeCompare(a.dataset.name);
                    case 'registrations-desc':
                        return parseInt(b.dataset.registrations) - parseInt(a.dataset.registrations);
                    default:
                        return 0;
                }
            });
            
            // Reorder rows in DOM
            rows.forEach(row => tbody.appendChild(row));
        }
        
        // Reset all filters
        function resetFilters() {
            document.getElementById('userSearchInput').value = '';
            document.getElementById('filterRole').value = '';
            document.getElementById('filterCountry').value = '';
            document.getElementById('filterRegistrations').value = '';
            document.getElementById('sortUsers').value = 'newest';
            filterUsers();
        }
        
        // Filter In-Person Registrations
        function filterInPersonRegistrations() {
            const searchTerm = document.getElementById('inPersonSearchInput').value.toLowerCase();
            const statusFilter = document.getElementById('filterInPersonStatus').value.toLowerCase();
            const sortBy = document.getElementById('sortInPerson').value;
            
            const rows = document.querySelectorAll('.inperson-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const institution = row.dataset.institution || '';
                const status = row.dataset.status || '';
                
                const matchesSearch = !searchTerm || 
                    name.includes(searchTerm) || 
                    email.includes(searchTerm) || 
                    institution.includes(searchTerm);
                
                const matchesStatus = !statusFilter || status === statusFilter;
                
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.getElementById('showingInPersonCount').textContent = visibleCount;
            sortInPersonRows(sortBy);
        }
        
        function sortInPersonRows(sortBy) {
            const table = document.getElementById('inPersonTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('.inperson-row:not([style*="display: none"])'));
            
            rows.sort((a, b) => {
                switch(sortBy) {
                    case 'newest':
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                    case 'oldest':
                        return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                    case 'name-asc':
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'name-desc':
                        return b.dataset.name.localeCompare(a.dataset.name);
                    default:
                        return 0;
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
        
        function resetInPersonFilters() {
            document.getElementById('inPersonSearchInput').value = '';
            document.getElementById('filterInPersonStatus').value = '';
            document.getElementById('sortInPerson').value = 'newest';
            filterInPersonRegistrations();
        }
        
        // Filter Online Registrations
        function filterOnlineRegistrations() {
            const searchTerm = document.getElementById('onlineSearchInput').value.toLowerCase();
            const statusFilter = document.getElementById('filterOnlineStatus').value.toLowerCase();
            const sortBy = document.getElementById('sortOnline').value;
            
            const rows = document.querySelectorAll('.online-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const organization = row.dataset.organization || '';
                const status = row.dataset.status || '';
                
                const matchesSearch = !searchTerm || 
                    name.includes(searchTerm) || 
                    email.includes(searchTerm) || 
                    organization.includes(searchTerm);
                
                const matchesStatus = !statusFilter || status === statusFilter;
                
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.getElementById('showingOnlineCount').textContent = visibleCount;
            sortOnlineRows(sortBy);
        }
        
        function sortOnlineRows(sortBy) {
            const table = document.getElementById('onlineTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('.online-row:not([style*="display: none"])'));
            
            rows.sort((a, b) => {
                switch(sortBy) {
                    case 'newest':
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                    case 'oldest':
                        return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                    case 'name-asc':
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'name-desc':
                        return b.dataset.name.localeCompare(a.dataset.name);
                    default:
                        return 0;
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
        
        function resetOnlineFilters() {
            document.getElementById('onlineSearchInput').value = '';
            document.getElementById('filterOnlineStatus').value = '';
            document.getElementById('sortOnline').value = 'newest';
            filterOnlineRegistrations();
        }
        
        // Export users to CSV
        function exportUsers() {
            const rows = document.querySelectorAll('.user-row:not([style*="display: none"])');
            let csv = 'Name,Email,Organization,Role,Country,City,Registrations,Joined\n';
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const name = cells[1].textContent.trim().replace(/,/g, ';');
                const email = cells[2].textContent.trim();
                const org = cells[3].textContent.trim().replace(/,/g, ';');
                const role = cells[4].textContent.trim();
                const location = cells[5].textContent.trim().replace(/,/g, ';');
                const regs = cells[6].textContent.trim();
                const joined = cells[7].textContent.trim();
                
                csv += `${name},${email},${org},${role},${location},${regs},${joined}\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'cgs-users-' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }
        
        // Initialize - apply filters on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Debounce search input
            const searchInput = document.getElementById('userSearchInput');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(filterUsers, 300);
                });
            }
        });
    </script>
</body>
</html>

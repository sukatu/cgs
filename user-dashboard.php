<?php
require_once 'config.php';
requireUserLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$section = isset($_GET['section']) ? $_GET['section'] : 'papers';

// Get user info
$userStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

// Get user's papers
$papers = [];
try {
    $papersStmt = $conn->prepare("
        SELECT * FROM user_papers 
        WHERE user_id = ? 
        ORDER BY submitted_date DESC
    ");
    if ($papersStmt) {
        $papersStmt->bind_param("i", $userId);
        $papersStmt->execute();
        $papers = $papersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $papersStmt->close();
    }
} catch (Exception $e) {
    // Table might not exist yet
    $papers = [];
}

// Get user's submitted papers for library
$userPapers = [];
try {
    $papersStmt = $conn->prepare("
        SELECT id, title, abstract, 'paper' as type, submitted_date as saved_date, file_path as resource_url, status
        FROM user_papers 
        WHERE user_id = ? 
        ORDER BY submitted_date DESC
    ");
    if ($papersStmt) {
        $papersStmt->bind_param("i", $userId);
        $papersStmt->execute();
        $userPapers = $papersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $papersStmt->close();
    }
} catch (Exception $e) {
    $userPapers = [];
}

// Get user's bookmarked articles
$bookmarkedArticles = [];
try {
    $articlesStmt = $conn->prepare("
        SELECT id, title, description, 'article' as type, saved_date, resource_url, resource_type
        FROM user_library 
        WHERE user_id = ? AND resource_type = 'article'
        ORDER BY saved_date DESC
    ");
    if ($articlesStmt) {
        $articlesStmt->bind_param("i", $userId);
        $articlesStmt->execute();
        $bookmarkedArticles = $articlesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $articlesStmt->close();
    }
} catch (Exception $e) {
    $bookmarkedArticles = [];
}

// Get user's bookmarked videos
$bookmarkedVideos = [];
try {
    $videosStmt = $conn->prepare("
        SELECT id, title, description, 'video' as type, saved_date, resource_url, resource_type
        FROM user_library 
        WHERE user_id = ? AND resource_type = 'video'
        ORDER BY saved_date DESC
    ");
    if ($videosStmt) {
        $videosStmt->bind_param("i", $userId);
        $videosStmt->execute();
        $bookmarkedVideos = $videosStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $videosStmt->close();
    }
} catch (Exception $e) {
    $bookmarkedVideos = [];
}

// Combine all library items
$library = array_merge($userPapers, $bookmarkedArticles, $bookmarkedVideos);
// Sort by saved_date descending
usort($library, function($a, $b) {
    return strtotime($b['saved_date']) - strtotime($a['saved_date']);
});

// Get user's event registrations
$registrationsStmt = $conn->prepare("
    SELECT er.*, e.title, e.event_date, e.location, e.format, e.event_type, e.status as event_status
    FROM event_registrations er
    JOIN events e ON er.event_id = e.id
    WHERE er.user_id = ?
    ORDER BY er.registration_date DESC
");
$registrationsStmt->bind_param("i", $userId);
$registrationsStmt->execute();
$registrations = $registrationsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get user's profile picture
$profilePicture = $user['profile_picture'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | CGS Network</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-navy) 0%, #0d2f7a 100%);
            color: white;
            padding: 3rem 0;
        }
        .dashboard-nav {
            background-color: var(--white);
            border-bottom: 1px solid var(--divider-grey);
            padding: 1rem 0;
        }
        .dashboard-nav ul {
            list-style: none;
            display: flex;
            gap: 2rem;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }
        .dashboard-nav a {
            color: var(--text-charcoal);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .dashboard-nav a:hover,
        .dashboard-nav a.active {
            color: var(--primary-navy);
            border-bottom-color: var(--primary-navy);
        }
        .card {
            background: white;
            border: 1px solid var(--divider-grey);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        .status-confirmed, .status-approved {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        .status-pending, .status-under-review {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .status-cancelled, .status-rejected {
            background-color: #ffebee;
            color: #d32f2f;
        }
        .profile-picture-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-navy);
            margin-bottom: 1rem;
        }
        .upload-area {
            border: 2px dashed var(--divider-grey);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: var(--primary-navy);
            background-color: var(--bg-offwhite);
        }
        .file-preview {
            margin-top: 1rem;
        }
        .file-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 1rem;
        }
        .paper-form, .account-form {
            max-width: 800px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .library-filter {
            transition: all 0.3s ease;
        }
        .library-filter.active {
            background-color: var(--primary-navy);
            color: white;
            border-color: var(--primary-navy);
        }
        .library-item {
            transition: opacity 0.3s ease;
        }
        .library-item.hidden {
            display: none;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .dashboard-nav ul {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-top">
            <div class="nav-top-container">
                <div class="logo">
                    <a href="index.html"><img src="logo-header.png" alt="Corporate Governance Series CGS Logo" class="logo-header"></a>
                </div>
                <div class="nav-utility">
                    <div class="header-search">
                        <input type="text" placeholder="Search..." id="headerSearchInput">
                        <button class="search-btn" id="searchBtn" style="display: none;">Search</button>
                    </div>
                    <span style="color: var(--text-charcoal); margin-right: 1rem;"><?php echo htmlspecialchars($user['name']); ?></span>
                    <a href="user-auth.php?logout=1" class="login-btn">Logout</a>
                </div>
            </div>
        </div>
        <div class="nav-bottom">
            <div class="nav-bottom-container">
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About CGS</a></li>
                    <li><a href="events.html">Events</a></li>
                    <li><a href="user-dashboard.php" class="active">My Dashboard</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-header">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <p>Manage your papers, library, and account</p>
        </div>
    </div>

    <div class="dashboard-nav">
        <div class="container">
            <ul>
                <li><a href="user-dashboard.php?section=papers" class="<?php echo $section == 'papers' ? 'active' : ''; ?>">My Papers</a></li>
                <li><a href="user-dashboard.php?section=library" class="<?php echo $section == 'library' ? 'active' : ''; ?>">My Library</a></li>
                <li><a href="user-dashboard.php?section=account" class="<?php echo $section == 'account' ? 'active' : ''; ?>">My Account</a></li>
                <li><a href="user-dashboard.php?section=registrations" class="<?php echo $section == 'registrations' ? 'active' : ''; ?>">My Registrations</a></li>
            </ul>
        </div>
    </div>

    <main>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="container" style="margin-top: 2rem;">
                <div style="background-color: #e8f5e9; color: #388e3c; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="container" style="margin-top: 2rem;">
                <div style="background-color: #ffebee; color: #d32f2f; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($section == 'papers'): ?>
            <!-- My Papers Section -->
            <section class="section content-section">
                <div class="container">
                    <h2 class="section-title">My Papers</h2>
                    
                    <!-- Submit Paper Form -->
                    <div class="card paper-form">
                        <h3 style="margin-bottom: 1.5rem; color: var(--primary-navy);">Submit a New Paper</h3>
                        <form action="user-paper-handler.php" method="POST" enctype="multipart/form-data" id="paperForm">
                            <div class="form-group">
                                <label for="paperTitle">Paper Title *</label>
                                <input type="text" id="paperTitle" name="title" placeholder="Enter paper title" required>
                            </div>
                            <div class="form-group">
                                <label for="paperAbstract">Abstract *</label>
                                <textarea id="paperAbstract" name="abstract" rows="5" placeholder="Enter paper abstract" required></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="paperKeywords">Keywords</label>
                                    <input type="text" id="paperKeywords" name="keywords" placeholder="e.g., governance, ESG, board">
                                </div>
                                <div class="form-group">
                                    <label for="paperCategory">Category</label>
                                    <select id="paperCategory" name="category">
                                        <option value="">Select category</option>
                                        <option value="research">Research Paper</option>
                                        <option value="case-study">Case Study</option>
                                        <option value="review">Review Article</option>
                                        <option value="opinion">Opinion Piece</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="paperFile">Upload Paper (PDF) *</label>
                                <div class="upload-area" id="uploadArea">
                                    <input type="file" id="paperFile" name="paper_file" accept=".pdf" required style="display: none;" onchange="handleFileSelect(this, 'paperPreview')">
                                    <label for="paperFile" style="cursor: pointer; display: block;">
                                        <strong>Click to upload</strong> or drag and drop
                                        <br>
                                        <span style="color: var(--text-light); font-size: 0.9rem;">PDF files only (Max 10MB)</span>
                                    </label>
                                    <div class="file-preview" id="paperPreview"></div>
                                </div>
                            </div>
                            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center;">
                                <button type="submit" class="btn btn-primary" id="submitBtn">Submit Paper</button>
                                <span id="submitStatus" style="color: var(--text-light); font-size: 0.9rem;"></span>
                            </div>
                        </form>
                    </div>

                    <!-- Submitted Papers List -->
                    <h3 style="margin-top: 3rem; margin-bottom: 1.5rem;">Submitted Papers</h3>
                    <?php if (empty($papers)): ?>
                        <div class="card" style="padding: 3rem; text-align: center;">
                            <p style="color: var(--text-light); margin-bottom: 1.5rem;">You haven't submitted any papers yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($papers as $paper): ?>
                            <div class="card">
                                <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                    <div style="flex: 1;">
                                        <h3 style="margin-bottom: 0.5rem; color: var(--primary-navy);"><?php echo htmlspecialchars($paper['title']); ?></h3>
                                        <p style="color: var(--text-charcoal); margin-bottom: 0.5rem;"><?php echo htmlspecialchars(substr($paper['abstract'], 0, 150)) . '...'; ?></p>
                                        <div style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                            <strong>Submitted:</strong> <?php echo date('F d, Y', strtotime($paper['submitted_date'])); ?>
                                        </div>
                                        <?php if ($paper['category']): ?>
                                            <div style="color: var(--text-light); font-size: 0.9rem;">
                                                <strong>Category:</strong> <?php echo ucfirst(str_replace('-', ' ', $paper['category'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $paper['status'])); ?>">
                                            <?php echo ucfirst($paper['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if ($paper['file_path']): ?>
                                    <div style="margin-top: 1rem;">
                                        <a href="<?php echo htmlspecialchars($paper['file_path']); ?>" target="_blank" class="btn btn-outline" style="text-decoration: none;">View Paper</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

        <?php elseif ($section == 'library'): ?>
            <!-- My Library Section -->
            <section class="section content-section">
                <div class="container">
                    <h2 class="section-title">My Library</h2>
                    <p style="color: var(--text-charcoal); margin-bottom: 2rem;">Your repository of submitted papers, bookmarked articles, and videos</p>
                    
                    <!-- Library Filters -->
                    <div style="margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button class="btn btn-outline library-filter active" data-filter="all" style="text-decoration: none;">All Items</button>
                        <button class="btn btn-outline library-filter" data-filter="paper" style="text-decoration: none;">My Papers</button>
                        <button class="btn btn-outline library-filter" data-filter="article" style="text-decoration: none;">Articles</button>
                        <button class="btn btn-outline library-filter" data-filter="video" style="text-decoration: none;">Videos</button>
                    </div>
                    
                    <?php if (empty($library)): ?>
                        <div class="card" style="padding: 3rem; text-align: center;">
                            <p style="color: var(--text-light); margin-bottom: 1.5rem;">Your library is empty.</p>
                            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                                <a href="user-dashboard.php?section=papers" class="btn btn-primary">Submit a Paper</a>
                                <a href="blog.html" class="btn btn-outline" style="text-decoration: none;">Browse Articles</a>
                                <a href="videos.html" class="btn btn-outline" style="text-decoration: none;">Browse Videos</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php 
                        $papersCount = count($userPapers);
                        $articlesCount = count($bookmarkedArticles);
                        $videosCount = count($bookmarkedVideos);
                        ?>
                        <div style="margin-bottom: 2rem; display: flex; gap: 2rem; flex-wrap: wrap; color: var(--text-charcoal);">
                            <div><strong>Total Items:</strong> <?php echo count($library); ?></div>
                            <div><strong>Papers:</strong> <?php echo $papersCount; ?></div>
                            <div><strong>Articles:</strong> <?php echo $articlesCount; ?></div>
                            <div><strong>Videos:</strong> <?php echo $videosCount; ?></div>
                        </div>
                        
                        <div id="libraryItems">
                            <?php foreach ($library as $item): 
                                $itemType = $item['type'] ?? 'unknown';
                                $typeLabel = ucfirst($itemType);
                                $typeColor = $itemType === 'paper' ? '#1976d2' : ($itemType === 'article' ? '#388e3c' : '#d32f2f');
                            ?>
                                <div class="card library-item" data-type="<?php echo $itemType; ?>" style="margin-bottom: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                        <div style="flex: 1;">
                                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                                <span style="background-color: <?php echo $typeColor; ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                                    <?php echo $typeLabel; ?>
                                                </span>
                                                <?php if (isset($item['status'])): ?>
                                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $item['status'])); ?>">
                                                        <?php echo ucfirst($item['status']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <h3 style="margin-bottom: 0.5rem; color: var(--primary-navy);"><?php echo htmlspecialchars($item['title']); ?></h3>
                                            <?php if (isset($item['abstract']) && $item['abstract']): ?>
                                                <p style="color: var(--text-charcoal); margin-bottom: 0.75rem;"><?php echo htmlspecialchars(substr($item['abstract'], 0, 200)) . (strlen($item['abstract']) > 200 ? '...' : ''); ?></p>
                                            <?php elseif (isset($item['description']) && $item['description']): ?>
                                                <p style="color: var(--text-charcoal); margin-bottom: 0.75rem;"><?php echo htmlspecialchars(substr($item['description'], 0, 200)) . (strlen($item['description']) > 200 ? '...' : ''); ?></p>
                                            <?php endif; ?>
                                            <div style="color: var(--text-light); font-size: 0.9rem;">
                                                <?php if ($itemType === 'paper'): ?>
                                                    <strong>Submitted:</strong> <?php echo date('F d, Y', strtotime($item['saved_date'])); ?>
                                                <?php else: ?>
                                                    <strong>Bookmarked:</strong> <?php echo date('F d, Y', strtotime($item['saved_date'])); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                                            <?php if (isset($item['resource_url']) && $item['resource_url']): ?>
                                                <?php if ($itemType === 'paper'): ?>
                                                    <a href="<?php echo htmlspecialchars($item['resource_url']); ?>" target="_blank" class="btn btn-outline" style="text-decoration: none;">View PDF</a>
                                                <?php else: ?>
                                                    <a href="<?php echo htmlspecialchars($item['resource_url']); ?>" target="_blank" class="btn btn-outline" style="text-decoration: none;">View</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if ($itemType !== 'paper'): ?>
                                                <a href="user-library-handler.php?action=remove&id=<?php echo $item['id']; ?>&type=<?php echo $itemType; ?>" class="btn btn-secondary" style="text-decoration: none;" onclick="return confirm('Remove this item from your library?');">Remove</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        <?php elseif ($section == 'account'): ?>
            <!-- My Account Section -->
            <section class="section content-section">
                <div class="container">
                    <h2 class="section-title">My Account</h2>
                    
                    <div class="card account-form">
                        <!-- Profile Picture Upload -->
                        <div class="profile-picture-container">
                            <?php if ($profilePicture): ?>
                                <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-picture" id="profilePicturePreview">
                            <?php else: ?>
                                <div class="profile-picture" style="background: var(--divider-grey); display: flex; align-items: center; justify-content: center; color: var(--text-light); font-size: 3rem;">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <form action="user-picture-handler.php" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                                <input type="file" id="profilePicture" name="profile_picture" accept="image/*" style="display: none;" onchange="handleProfilePictureSelect(this)">
                                <label for="profilePicture" class="btn btn-outline" style="cursor: pointer; text-decoration: none; display: inline-block;">
                                    <?php echo $profilePicture ? 'Change Picture' : 'Upload Picture'; ?>
                                </label>
                                <button type="submit" id="uploadPictureBtn" style="display: none;" class="btn btn-primary">Save Picture</button>
                            </form>
                            <div class="file-preview" id="profilePicturePreviewContainer"></div>
                        </div>

                        <!-- Account Information -->
                        <form action="user-account-handler.php" method="POST" id="accountForm">
                            <h3 style="margin-bottom: 1.5rem; color: var(--primary-navy);">Account Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="accountName">Full Name *</label>
                                    <input type="text" id="accountName" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="accountEmail">Email Address *</label>
                                    <input type="email" id="accountEmail" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="accountOrganization">Organization</label>
                                    <input type="text" id="accountOrganization" name="organization" value="<?php echo htmlspecialchars($user['organization'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="accountRole">Profession</label>
                                    <input type="text" id="accountRole" name="profession" value="<?php echo htmlspecialchars($user['role'] ?? ''); ?>" placeholder="e.g., Director, Lawyer, Consultant">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="accountCity">City</label>
                                    <input type="text" id="accountCity" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="accountCountry">Country</label>
                                    <input type="text" id="accountCountry" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="accountBio">Bio</label>
                                <textarea id="accountBio" name="bio" rows="4" placeholder="Tell us about yourself"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>

                        <!-- Change Password -->
                        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--divider-grey);">
                            <h3 style="margin-bottom: 1.5rem; color: var(--primary-navy);">Change Password</h3>
                            <form action="user-password-handler.php" method="POST">
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" id="currentPassword" name="current_password" required>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="newPassword">New Password</label>
                                        <input type="password" id="newPassword" name="new_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirmPassword">Confirm New Password</label>
                                        <input type="password" id="confirmPassword" name="confirm_password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ($section == 'registrations'): ?>
            <!-- My Registrations Section -->
            <section class="section content-section">
            <div class="container">
                <h2 class="section-title">My Event Registrations</h2>
                
                <?php if (empty($registrations)): ?>
                    <div class="card" style="padding: 3rem; text-align: center;">
                        <p style="color: var(--text-light); margin-bottom: 1.5rem;">You haven't registered for any events yet.</p>
                        <a href="events.html" class="btn btn-primary">Browse Events</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($registrations as $reg): ?>
                            <div class="card">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                <div style="flex: 1;">
                                    <h3 style="margin-bottom: 0.5rem; color: var(--primary-navy);"><?php echo htmlspecialchars($reg['title']); ?></h3>
                                    <div style="color: var(--text-charcoal); margin-bottom: 0.5rem;">
                                        <strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($reg['event_date'])); ?>
                                    </div>
                                    <?php if ($reg['location']): ?>
                                        <div style="color: var(--text-charcoal); margin-bottom: 0.5rem;">
                                            <strong>Location:</strong> <?php echo htmlspecialchars($reg['location']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div style="color: var(--text-charcoal); margin-bottom: 0.5rem;">
                                        <strong>Registered:</strong> <?php echo date('F d, Y', strtotime($reg['registration_date'])); ?>
                                    </div>
                                </div>
                                <div>
                                    <span class="status-badge status-<?php echo $reg['status']; ?>">
                                        <?php echo ucfirst($reg['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
                        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CGS</h3>
                    <p>Corporate Governance Series<br>Transforming governance standards across Africa</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Corporate Governance Series (CGS) by CSTS Ghana. All rights reserved.</p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-light);">Website by <a href="https://www.dennislaw.com" target="_blank" style="color: var(--accent-gold); font-weight: 600;">ADDENS TECHNOLOGY LIMITED</a> - Makers of DENNISLAW</p>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
    <script>
        function handleFileSelect(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            const submitBtn = document.getElementById('submitBtn');
            const submitStatus = document.getElementById('submitStatus');
            
            if (file) {
                // Validate file type
                if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                    preview.innerHTML = `<p style="color: #d32f2f;"><strong>Error:</strong> Please select a PDF file. Selected file: ${file.name}</p>`;
                    input.value = '';
                    submitBtn.disabled = true;
                    submitStatus.textContent = '';
                    return;
                }
                
                // Validate file size (10MB max)
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    preview.innerHTML = `<p style="color: #d32f2f;"><strong>Error:</strong> File size (${(file.size / 1024 / 1024).toFixed(2)} MB) exceeds maximum allowed size of 10MB.</p>`;
                    input.value = '';
                    submitBtn.disabled = true;
                    submitStatus.textContent = '';
                    return;
                }
                
                // Show file info
                preview.innerHTML = `
                    <div style="margin-top: 1rem; padding: 1rem; background: var(--bg-offwhite); border-radius: 4px;">
                        <p style="color: var(--primary-navy); margin-bottom: 0.5rem;">
                            <strong>✓ Selected:</strong> ${file.name}
                        </p>
                        <p style="color: var(--text-charcoal); font-size: 0.9rem; margin: 0;">
                            Size: ${(file.size / 1024 / 1024).toFixed(2)} MB
                        </p>
                    </div>
                `;
                submitBtn.disabled = false;
                submitStatus.textContent = '';
            } else {
                preview.innerHTML = '';
                submitBtn.disabled = false;
            }
        }

        function handleProfilePictureSelect(input) {
            const file = input.files[0];
            if (file) {
                if (file.type.startsWith('image/')) {
                    // Validate file size (5MB max)
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    if (file.size > maxSize) {
                        alert('Image size must be less than 5MB. Your file is ' + (file.size / 1024 / 1024).toFixed(2) + 'MB.');
                        input.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewContainer = document.getElementById('profilePicturePreviewContainer');
                        const preview = document.getElementById('profilePicturePreview');
                        if (preview) {
                            preview.src = e.target.result;
                        } else {
                            previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 1rem;">`;
                        }
                        document.getElementById('uploadPictureBtn').style.display = 'inline-block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Please select an image file (JPEG, PNG, or GIF)');
                    input.value = '';
                }
            }
        }

        // Form submission handler
        document.addEventListener('DOMContentLoaded', function() {
            const paperForm = document.getElementById('paperForm');
            if (paperForm) {
                paperForm.addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('submitBtn');
                    const submitStatus = document.getElementById('submitStatus');
                    const fileInput = document.getElementById('paperFile');
                    
                    // Validate file before submission
                    if (!fileInput.files || fileInput.files.length === 0) {
                        e.preventDefault();
                        submitStatus.textContent = 'Please select a PDF file to upload.';
                        submitStatus.style.color = '#d32f2f';
                        return false;
                    }
                    
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Submitting...';
                    submitStatus.textContent = 'Please wait, uploading your paper...';
                    submitStatus.style.color = 'var(--primary-navy)';
                });
            }
        });

        // Drag and drop functionality
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('paperFile');
            
            if (uploadArea && fileInput) {
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    uploadArea.style.borderColor = 'var(--primary-navy)';
                    uploadArea.style.backgroundColor = 'var(--bg-offwhite)';
                });
                
                uploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    uploadArea.style.borderColor = 'var(--divider-grey)';
                    uploadArea.style.backgroundColor = 'transparent';
                });
                
                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    uploadArea.style.borderColor = 'var(--divider-grey)';
                    uploadArea.style.backgroundColor = 'transparent';
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        handleFileSelect(fileInput, 'paperPreview');
                    }
                });
            }

            // Library filter functionality
            const filters = document.querySelectorAll('.library-filter');
            const libraryItems = document.querySelectorAll('.library-item');
            
            filters.forEach(filter => {
                filter.addEventListener('click', function() {
                    const filterType = this.getAttribute('data-filter');
                    
                    // Update active state
                    filters.forEach(f => f.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter items
                    libraryItems.forEach(item => {
                        if (filterType === 'all') {
                            item.style.display = 'block';
                        } else {
                            const itemType = item.getAttribute('data-type');
                            if (itemType === filterType) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

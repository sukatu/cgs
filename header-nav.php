<?php
// Reusable header navigation component
// Checks if user is logged in and displays appropriate header

require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
$userName = $_SESSION['user_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;

// Get user profile picture if logged in
$userProfilePicture = null;
if ($isLoggedIn && $userId > 0) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $userData = $result->fetch_assoc();
            $userProfilePicture = $userData['profile_picture'];
        }
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        // Silently fail - just don't show profile picture
    }
}

// Get user initials for avatar fallback
$userInitials = '';
if ($isLoggedIn && $userName) {
    $nameParts = explode(' ', trim($userName));
    if (count($nameParts) >= 2) {
        $userInitials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1));
    } else {
        $userInitials = strtoupper(substr($userName, 0, 2));
    }
}
?>
<div class="nav-utility">
    <div class="header-search">
        <input type="text" placeholder="Search..." id="headerSearchInput">
        <button class="search-btn" id="searchBtn" style="display: none;">Search</button>
    </div>
    <?php if ($isLoggedIn): ?>
        <div class="user-menu-wrapper">
            <div class="user-menu-trigger" id="userMenuTrigger">
                <?php if ($userProfilePicture && file_exists($userProfilePicture)): ?>
                    <img src="<?php echo htmlspecialchars($userProfilePicture); ?>" alt="<?php echo htmlspecialchars($userName); ?>" class="user-avatar">
                <?php else: ?>
                    <div class="user-avatar-initials"><?php echo htmlspecialchars($userInitials); ?></div>
                <?php endif; ?>
                <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 0.5rem;">
                    <path d="M6 9L1 4h10L6 9z" fill="currentColor"/>
                </svg>
            </div>
            <div class="user-menu-dropdown" id="userMenuDropdown">
                <a href="user-dashboard.php" class="user-menu-item">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 8a3 3 0 100-6 3 3 0 000 6zM8 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                    </svg>
                    My Dashboard
                </a>
                <a href="user-dashboard.php?section=account" class="user-menu-item">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 0a8 8 0 100 16A8 8 0 008 0zm0 2a6 6 0 110 12A6 6 0 018 2zm0 1a5 5 0 100 10A5 5 0 018 3z" fill="currentColor"/>
                    </svg>
                    My Account
                </a>
                <a href="user-dashboard.php?section=papers" class="user-menu-item">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 0H2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V2a2 2 0 00-2-2zM2 1h12a1 1 0 011 1v12a1 1 0 01-1 1H2a1 1 0 01-1-1V2a1 1 0 011-1z" fill="currentColor"/>
                    </svg>
                    My Papers
                </a>
                <a href="user-dashboard.php?section=library" class="user-menu-item">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2v12h12V2H2zm11 11H3V3h10v10zM5 4h6v1H5V4zm0 2h6v1H5V6zm0 2h4v1H5V8z" fill="currentColor"/>
                    </svg>
                    My Library
                </a>
                <div class="user-menu-divider"></div>
                <a href="user-auth.php?logout=1" class="user-menu-item user-menu-logout">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 14H2a1 1 0 01-1-1V3a1 1 0 011-1h4m5 0h3a1 1 0 011 1v10a1 1 0 01-1 1h-3m-5 0h5M9 8h6M12 5l3 3-3 3" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Logout
                </a>
            </div>
        </div>
    <?php else: ?>
        <a href="login-user.php" class="login-btn">Login</a>
        <a href="network.php" class="join-btn">Join the Network</a>
    <?php endif; ?>
</div>

<style>
    /* User Menu Styles */
    .user-menu-wrapper {
        position: relative;
    }
    
    .user-menu-trigger {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
        color: var(--text-charcoal);
    }
    
    .user-menu-trigger:hover {
        background-color: var(--bg-offwhite);
    }
    
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--divider-grey);
    }
    
    .user-avatar-initials {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-navy) 0%, var(--accent-gold) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        border: 2px solid var(--divider-grey);
    }
    
    .user-name {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--text-charcoal);
    }
    
    .user-menu-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        min-width: 220px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1000;
        padding: 0.5rem 0;
        border: 1px solid var(--divider-grey);
    }
    
    .user-menu-wrapper:hover .user-menu-dropdown,
    .user-menu-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .user-menu-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        color: var(--text-charcoal);
        text-decoration: none;
        font-size: 0.9rem;
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    
    .user-menu-item:hover {
        background-color: var(--bg-offwhite);
        color: var(--primary-navy);
    }
    
    .user-menu-item svg {
        opacity: 0.7;
        flex-shrink: 0;
    }
    
    .user-menu-item:hover svg {
        opacity: 1;
    }
    
    .user-menu-logout {
        color: #d32f2f;
    }
    
    .user-menu-logout:hover {
        background-color: #ffebee;
        color: #c62828;
    }
    
    .user-menu-divider {
        height: 1px;
        background-color: var(--divider-grey);
        margin: 0.5rem 0;
    }
    
    @media (max-width: 768px) {
        .user-name {
            display: none;
        }
        
        .user-menu-dropdown {
            right: -1rem;
        }
    }
    
    @media (max-width: 480px) {
        .user-menu-trigger svg {
            display: none;
        }
    }
</style>

<script>
    // User menu toggle for mobile
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuTrigger = document.getElementById('userMenuTrigger');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        
        if (userMenuTrigger && userMenuDropdown) {
            // Toggle on click for mobile
            userMenuTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('active');
            });
            
            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuTrigger.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                    userMenuDropdown.classList.remove('active');
                }
            });
        }
    });
</script>

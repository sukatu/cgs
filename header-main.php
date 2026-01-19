<?php
// Main header navigation component - matches network.php structure
// This file MUST output HTML even if PHP fails

// Initialize activePage if not set
if (!isset($activePage)) {
    $activePage = '';
}

// Try to load config and determine active page, but don't fail if it doesn't work
try {
    // Ensure config.php is loaded (if not already)
    if (!function_exists('getDBConnection') && file_exists('config.php')) {
        require_once 'config.php';
    }
    
    // Ensure session is started
    if (function_exists('session_status') && session_status() === PHP_SESSION_NONE) {
        @session_start();
    } elseif (!function_exists('session_status')) {
        @session_start();
    }
    
    // Determine active page from current file if not set
    if (empty($activePage)) {
        // Try to get current file from various sources
        $currentFile = '';
        if (isset($_SERVER['PHP_SELF'])) {
            $currentFile = basename($_SERVER['PHP_SELF']);
        } elseif (isset($_SERVER['SCRIPT_NAME'])) {
            $currentFile = basename($_SERVER['SCRIPT_NAME']);
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $currentFile = basename($path);
        }
        
        // Map files to active page identifiers (now using .php extensions)
        $pageMap = [
            'index.php' => 'home',
            'about.php' => 'about',
            'events.php' => 'events',
            'webinar-diary.php' => 'events',
            'series-diary.php' => 'events',
            'resources.php' => 'resources',
            'governance-codes.php' => 'resources',
            'blog.php' => 'resources',
            'media.php' => 'media',
            'videos.php' => 'media',
            'pictures.php' => 'media',
            'training.php' => 'training',
            'network.php' => 'network',
            'contact.php' => 'contact',
            'login-user.php' => 'network',
            'login.php' => 'network',
            'user-dashboard.php' => 'network',
        ];
        
        if (!empty($currentFile) && isset($pageMap[$currentFile])) {
            $activePage = $pageMap[$currentFile];
        } else {
            // Check for blog posts (now .php)
            if (!empty($currentFile) && strpos($currentFile, 'blog-') === 0) {
                $activePage = 'resources';
            }
        }
    }
} catch (Exception $e) {
    // Silently continue - we'll use defaults
    $activePage = $activePage ?: '';
}
?>
<nav class="navbar">
    <div class="nav-top">
        <div class="nav-top-container">
            <div class="logo">
                <a href="index.php"><img src="logo-header.png" alt="Corporate Governance Series CGS Logo" class="logo-header"></a>
            </div>
            <?php 
            try {
                if (file_exists('header-nav.php')) {
                    include 'header-nav.php'; 
                } else {
                    // Fallback if header-nav.php doesn't exist
                    echo '<div class="nav-utility">';
                    echo '<div class="header-search">';
                    echo '<input type="text" placeholder="Search..." id="headerSearchInput">';
                    echo '<button class="search-btn" id="searchBtn" style="display: none;">Search</button>';
                    echo '</div>';
                    echo '<a href="login-user.php" class="login-btn">Login</a>';
                    echo '<a href="network.php" class="join-btn">Join the Network</a>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                // Fallback on error
                echo '<div class="nav-utility">';
                echo '<div class="header-search">';
                echo '<input type="text" placeholder="Search..." id="headerSearchInput">';
                echo '<button class="search-btn" id="searchBtn" style="display: none;">Search</button>';
                echo '</div>';
                echo '<a href="login-user.php" class="login-btn">Login</a>';
                echo '<a href="network.php" class="join-btn">Join the Network</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <div class="nav-bottom">
        <div class="nav-bottom-container">
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php" <?php echo ($activePage === 'home') ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="about.php" <?php echo ($activePage === 'about') ? 'class="active"' : ''; ?>>About CGS</a></li>
                <li class="dropdown">
                    <a href="events.php" <?php echo ($activePage === 'events') ? 'class="active"' : ''; ?>>Events</a>
                    <ul class="dropdown-menu">
                        <li><a href="webinar-diary.php">Webinar Diary</a></li>
                        <li><a href="series-diary.php">Series Diary</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="resources.php" <?php echo ($activePage === 'resources') ? 'class="active"' : ''; ?>>Resources</a>
                    <ul class="dropdown-menu">
                        <li><a href="governance-codes.php">Governance Codes in Africa</a></li>
                        <li><a href="blog.php">Blog</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="media.php" <?php echo ($activePage === 'media') ? 'class="active"' : ''; ?>>Media</a>
                    <ul class="dropdown-menu">
                        <li><a href="videos.php">Videos</a></li>
                        <li><a href="pictures.php">Pictures</a></li>
                    </ul>
                </li>
                <li><a href="training.php" <?php echo ($activePage === 'training') ? 'class="active"' : ''; ?>>Training</a></li>
                <li><a href="network.php" <?php echo ($activePage === 'network') ? 'class="active"' : ''; ?>>Network</a></li>
                <li><a href="contact.php" <?php echo ($activePage === 'contact') ? 'class="active"' : ''; ?>>Contact</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</nav>

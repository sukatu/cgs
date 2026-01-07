<?php
require_once 'config.php';
requireUserLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

// Get user info
$userStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

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
        .registration-card {
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
        .status-confirmed {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        .status-pending {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .status-cancelled {
            background-color: #ffebee;
            color: #d32f2f;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-top">
            <div class="nav-top-container">
                <div class="logo">
                    <a href="index.html"><img src="logo.png" alt="Corporate Governance Series CGS Logo"></a>
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
            <p>Manage your event registrations and profile</p>
        </div>
    </div>

    <div class="dashboard-nav">
        <div class="container">
            <ul>
                <li><a href="#registrations" class="active">My Registrations</a></li>
                <li><a href="#profile">Profile</a></li>
                <li><a href="events.html">Browse Events</a></li>
            </ul>
        </div>
    </div>

    <main>
        <section class="section content-section" id="registrations">
            <div class="container">
                <h2 class="section-title">My Event Registrations</h2>
                
                <?php if (empty($registrations)): ?>
                    <div class="card" style="padding: 3rem; text-align: center;">
                        <p style="color: var(--text-light); margin-bottom: 1.5rem;">You haven't registered for any events yet.</p>
                        <a href="events.html" class="btn btn-primary">Browse Events</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($registrations as $reg): ?>
                        <div class="registration-card">
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

        <section class="section content-section-alt" id="profile">
            <div class="container">
                <h2 class="section-title">My Profile</h2>
                <div class="card" style="padding: 2rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                        <div>
                            <strong>Name:</strong><br>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </div>
                        <div>
                            <strong>Email:</strong><br>
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>
                        <?php if ($user['organization']): ?>
                            <div>
                                <strong>Organization:</strong><br>
                                <?php echo htmlspecialchars($user['organization']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($user['role']): ?>
                            <div>
                                <strong>Role:</strong><br>
                                <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($user['country']): ?>
                            <div>
                                <strong>Location:</strong><br>
                                <?php echo htmlspecialchars($user['city'] . ', ' . $user['country']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top: 2rem;">
                        <a href="user-auth.php?logout=1" class="btn btn-secondary">Logout</a>
                    </div>
                </div>
            </div>
        </section>
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
                <p>&copy; 2024 Corporate Governance Series (CGS) by CSTS Ghana. All rights reserved.</p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-light);">Website by <a href="https://www.dennislaw.com" target="_blank" style="color: var(--accent-gold); font-weight: 600;">ADDENS TECHNOLOGY LIMITED</a> - Makers of DENNISLAW</p>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>

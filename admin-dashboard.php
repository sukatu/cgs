<?php
require_once 'config.php';
requireAdminLogin();

$conn = getDBConnection();
$events = [];
$alert = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $alert = '<div class="alert alert-success">Event deleted successfully!</div>';
    } else {
        $alert = '<div class="alert alert-error">Error deleting event.</div>';
    }
}

// Fetch events
$result = $conn->query("SELECT * FROM events ORDER BY event_date DESC, created_at DESC");
if ($result) {
    $events = $result->fetch_all(MYSQLI_ASSOC);
}

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
        .btn-secondary:hover {
            background-color: #d1d5db;
        }
        .events-table {
            width: 100%;
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
        }
        .events-table tr:hover {
            background-color: var(--bg-offwhite);
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
                <a href="admin-upload-image.php" class="btn btn-secondary">Manage Images</a>
            </div>
            <button class="btn btn-primary" onclick="openModal('eventModal')">+ Add New Event</button>
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
            if (section === 'events') {
                document.getElementById('eventsSection').style.display = 'block';
                document.getElementById('usersSection').style.display = 'none';
                document.getElementById('btnEvents').classList.add('btn-primary');
                document.getElementById('btnEvents').classList.remove('btn-secondary');
                document.getElementById('btnUsers').classList.add('btn-secondary');
                document.getElementById('btnUsers').classList.remove('btn-primary');
            } else if (section === 'users') {
                document.getElementById('eventsSection').style.display = 'none';
                document.getElementById('usersSection').style.display = 'block';
                document.getElementById('btnUsers').classList.add('btn-primary');
                document.getElementById('btnUsers').classList.remove('btn-secondary');
                document.getElementById('btnEvents').classList.add('btn-secondary');
                document.getElementById('btnEvents').classList.remove('btn-primary');
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

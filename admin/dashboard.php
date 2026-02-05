<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('admin'); // Uncomment to enforce admin role

// Fetch System Stats
$requests_count = $pdo->query("SELECT COUNT(*) FROM catering_requests")->fetchColumn();
$menu_items_count = $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$todays_requests = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE DATE(created_at) = CURDATE()")->fetchColumn();

define('PAGE_TITLE', 'Admin Dashboard');
include '../includes/header.php';
?>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="logo-text">
                <h2>NRSC ADMIN</h2>
                <span>System Control</span>
            </div>
        </div>
        <ul class="nav-menu">
            <div class="menu-category">Management</div>
            <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="#"><i class="fas fa-utensils"></i> Menu Items</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
            <div class="menu-category">System</div>
            <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div>
                <p class="breadcrumb">System Overview</p>
                <h1>Admin Dashboard</h1>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(59, 130, 246, 0.2); color: #60A5FA;"><i class="fas fa-file-alt"></i></div>
                <div class="stat-info">
                    <h3><?php echo $requests_count; ?></h3>
                    <p>Total Requests</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(16, 185, 129, 0.2); color: #6EE7B7;"><i class="fas fa-utensils"></i></div>
                <div class="stat-info">
                    <h3><?php echo $menu_items_count; ?></h3>
                    <p>Menu Items</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(249, 115, 22, 0.2); color: #F97316;"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?php echo $users_count; ?></h3>
                    <p>Active Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(139, 92, 246, 0.2); color: #C4B5FD;"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h3><?php echo $todays_requests; ?></h3>
                    <p>New Today</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>System Health</h3>
            </div>
            <div style="padding: 30px; text-align: center;">
                <div style="font-size: 3rem; color: #10B981; margin-bottom: 20px;"><i class="fas fa-server"></i></div>
                <h4 style="color: var(--white);">All Systems Operational</h4>
                <p style="color: var(--text-secondary);">Database connection established. Mail server active.</p>
            </div>
        </div>
    </main>
</div>
</body>
</html>

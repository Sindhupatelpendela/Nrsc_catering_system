<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
checkAuth('admin');
define('PAGE_TITLE', 'Manage Items');
require_once '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-grid">
        <aside class="sidebar">
             <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage_items.php" class="active"><i class="fas fa-hamburger"></i> Manage Items</a></li>
            </ul>
        </aside>
        <main class="dashboard-content">
            <div class="card">
                <h3>Manage Menu Items</h3>
                <p>Item management interface goes here (CRUD).</p>
            </div>
        </main>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>

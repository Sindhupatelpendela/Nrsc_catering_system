<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('canteen');

$new_orders = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE status = 'approved'")->fetchColumn();
$in_progress = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE status = 'in_progress'")->fetchColumn();
$todays_orders = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE event_date = CURDATE() AND status != 'cancelled' AND status != 'rejected'")->fetchColumn();
$completed_total = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE status = 'completed'")->fetchColumn();

// Tab Logic
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'active';
$page_title = ($tab == 'history') ? 'Order History' : 'Live Orders';

if ($tab == 'history') {
    // Fetch History (Completed/Rejected)
    $sql = "
        SELECT r.*, u.full_name as requester_name 
        FROM catering_requests r 
        JOIN users u ON r.employee_id = u.id 
        WHERE r.status IN ('completed', 'rejected') 
        ORDER BY r.event_date DESC, r.event_time DESC LIMIT 50
    ";
} else {
    // Fetch Active (Approved/In Progress)
    $sql = "
        SELECT r.*, u.full_name as requester_name 
        FROM catering_requests r 
        JOIN users u ON r.employee_id = u.id 
        WHERE r.status IN ('approved', 'in_progress') 
        ORDER BY r.event_date ASC, r.event_time ASC
    ";
}
$orders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

define('PAGE_TITLE', 'Kitchen Dashboard');
include '../includes/header.php';
?>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-utensils"></i></div>
            <div class="logo-text">
                <h2>NRSC CANTEEN</h2>
                <span>Kitchen Staff</span>
            </div>
        </div>
        <ul class="nav-menu">
            <div class="menu-category">Kitchen</div>
            <li><a href="dashboard.php?tab=active" class="<?php echo ($tab != 'history') ? 'active' : ''; ?>"><i class="fas fa-fire-burner"></i> Live Orders</a></li>
            <li><a href="dashboard.php?tab=history" class="<?php echo ($tab == 'history') ? 'active' : ''; ?>"><i class="fas fa-list-check"></i> History</a></li>
            <li><a href="#"><i class="fas fa-utensils"></i> Menu Items</a></li>
            <div class="menu-category">Account</div>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div>
                <p class="breadcrumb">Kitchen Overview</p>
                <h1><?php echo $page_title; ?></h1>
            </div>
            <div style="background: var(--bg-card); padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div style="width: 35px; height: 35px; background: #334155; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user-chef"></i>
                </div>
                <div>
                    <div style="font-size: 0.9rem; font-weight: 600;">Staff</div>
                    <div style="font-size: 0.75rem; color: #94A3B8;">Online</div>
                </div>
            </div>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div style="background: rgba(16, 185, 129, 0.2); color: #6EE7B7; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid rgba(16, 185, 129, 0.3);">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div style="background: rgba(239, 68, 68, 0.2); color: #FCA5A5; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid rgba(239, 68, 68, 0.3);">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(249, 115, 22, 0.2); color: #F97316;"><i class="fas fa-bell"></i></div>
                <div class="stat-info">
                    <h3><?php echo $new_orders; ?></h3>
                    <p>New Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(59, 130, 246, 0.2); color: #3B82F6;"><i class="fas fa-fire-burner"></i></div>
                <div class="stat-info">
                    <h3><?php echo $in_progress; ?></h3>
                    <p>Cooking</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-box" style="background: rgba(16, 185, 129, 0.2); color: #10B981;"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-info">
                    <h3><?php echo $todays_orders; ?></h3>
                    <p>Today's Total</p>
                </div>
            </div>
             <div class="stat-card">
                <div class="icon-box" style="background: rgba(139, 92, 246, 0.2); color: #8B5CF6;"><i class="fas fa-check-double"></i></div>
                <div class="stat-info">
                    <h3><?php echo $completed_total; ?></h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header">
                <h3><?php echo ($tab == 'history') ? 'Order History' : 'Active Orders Queue'; ?></h3>
            </div>
            <?php if(empty($orders)): ?>
                <div style="padding: 40px; text-align: center; color: var(--text-secondary);">
                    <i class="fas fa-mug-hot" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>No orders found in this category.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ref ID / Event</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <?php if($tab != 'history'): ?><th style="text-align: right;">Action</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): 
                             $status_class = '';
                             $display_status = '';
                             switch($order['status']) {
                                 case 'approved': $status_class = 'status-approved'; $display_status = 'NEW ORDER'; break;
                                 case 'in_progress': $status_class = 'status-in_progress'; $display_status = 'COOKING'; break;
                                 case 'completed': $status_class = 'status-completed'; $display_status = 'COMPLETED'; break;
                                 case 'rejected': $status_class = 'status-rejected'; $display_status = 'REJECTED'; break;
                             }
                        ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--white);">#<?php echo htmlspecialchars($order['request_number']); ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary);"><?php echo htmlspecialchars($order['event_name']); ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">By: <?php echo htmlspecialchars($order['requester_name']); ?></div>
                            </td>
                            <td>
                                <div style="color: var(--saffron); font-weight: 600;"><?php echo date('h:i A', strtotime($order['event_time'])); ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo date('M d', strtotime($order['event_date'])); ?></div>
                            </td>
                            <td>
                                <span style="background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 4px; color: var(--white);">
                                    <?php echo $order['guest_count']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $display_status; ?></span>
                            </td>
                            <?php if($tab != 'history'): ?>
                            <td style="text-align: right;">
                                <form action="../handlers/canteen_handler.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $order['id']; ?>">
                                    <?php if($order['status'] == 'approved'): ?>
                                        <button type="submit" name="action" value="sanction" class="btn btn-primary" style="background: var(--gradient-green); border:none;">
                                            <i class="fas fa-fire"></i> Start Cooking
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger" style="margin-left: 5px; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #FCA5A5;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php elseif($order['status'] == 'in_progress'): ?>
                                        <button type="submit" name="action" value="complete" class="btn btn-primary" style="background: var(--accent-blue); border:none;">
                                            <i class="fas fa-check-double"></i> Complete
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>

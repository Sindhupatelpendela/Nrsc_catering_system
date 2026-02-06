<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('officer');

// Fetch Current User Details for Sidebar
$user_id = $_SESSION['user_id'];
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$current_user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Sidebar Image Logic
$sidebar_image = ($current_user['profile_image']) ? "../uploads/profiles/" . $current_user['profile_image'] : "";
$initials = strtoupper(substr($current_user['name'], 0, 2));

// Fetch Requests for Officer
$sql = "
    SELECT r.id, r.request_number, r.employee_id, r.event_name as title, r.created_at as date_of_request, r.event_date, 
           r.status, 'catering' as type, u.full_name as requester_name 
    FROM catering_requests r 
    JOIN users u ON r.employee_id = u.id 
    WHERE 1=1
";

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';
$status_condition = ($tab == 'history') ? " AND (r.status = 'approved' OR r.status = 'rejected' OR r.status = 'completed')" : " AND r.status = 'pending'";

// Fetch Table Data
$table_sql = "
    SELECT r.id, r.request_number, r.employee_id, r.event_name as title, r.created_at as date_of_request, r.event_date, 
           r.status, 'catering' as type, u.name as requester_name, r.total_amount, r.guest_count 
    FROM catering_requests r 
    JOIN users u ON r.employee_id = u.id 
    WHERE 1=1
";
$all_requests = $pdo->query($table_sql . $status_condition . " ORDER BY r.event_date DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Global Stats for Cards
$pending_count = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE status = 'pending'")->fetchColumn();
$total_approved = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE status = 'approved'")->fetchColumn();
$approved_today = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE status = 'approved' AND DATE(updated_at) = CURDATE()")->fetchColumn();

// Eager Loading for Catering Items
$catering_ids = array_column($all_requests, 'id');
$items_map = [];
if (!empty($catering_ids)) {
    $in = str_repeat('?,', count($catering_ids) - 1) . '?';
    $item_sql = "SELECT ri.*, mi.item_name FROM request_items ri LEFT JOIN menu_items mi ON ri.item_id = mi.id WHERE request_id IN ($in)";
    $istmt = $pdo->prepare($item_sql);
    $istmt->execute($catering_ids);
    $all_items = $istmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($all_items as $item) {
        $items_map[$item['request_id']][] = $item;
    }
}

define('PAGE_TITLE', 'Officer Approvals');
include '../includes/header.php';
?>

<style>
    /* --- ANIMATIONS & LAYOUT --- */
    :root {
        --primary-color: #EA580C;
        --secondary-color: #1E293B;
        --text-muted: #64748B;
        --bg-light: #F8FAFC;
    }

    /* --- SIDEBAR ENHANCEMENTS (Local Override) --- */
    .sidebar {
        width: 380px !important; /* Increased Width */
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
        box-shadow: 4px 0 25px rgba(0,0,0,0.05);
    }
    .sidebar .nav-item {
        font-size: 2.2rem !important; /* Huge Links */
        padding: 25px 30px !important;
        margin-bottom: 20px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        color: #1E3A8A !important;
        border: 2px solid #E2E8F0 !important;
        border-radius: 20px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        background: #FFFFFF;
    }
    .sidebar .nav-item:hover {
        background: #F8FAFC !important;
        border-color: #3B82F6 !important;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
        color: #1E40AF !important;
    }
    .sidebar .nav-item.active {
        background: #EFF6FF !important;
        border-color: #2563EB !important;
        color: #1E40AF !important;
        box-shadow: 0 8px 15px rgba(37, 99, 235, 0.1);
    }
    .sidebar .profile-name {
        font-size: 2.2rem !important;
        margin-top: 25px;
        margin-bottom: 10px;
        color: #0F172A !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .sidebar .btn-profile-sm {
        font-size: 1.4rem !important;
        padding: 12px 30px !important;
        color: #1E3A8A !important;
        border: 2px solid #BFDBFE !important;
        font-weight: 700 !important;
        background: white;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .sidebar .btn-profile-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(37, 99, 235, 0.15);
        background: #F0F9FF;
    }

    /* --- PROFESSIONAL DASHBOARD STYLING --- */
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    }
    .card-header h3 {
        font-size: 1.4rem !important;
        font-weight: 700 !important;
    }
    table th {
        font-size: 0.9rem !important;
        padding: 20px 25px !important;
    }
    table td {
        font-size: 1rem !important;
        padding: 20px 25px !important;
    }

    /* --- MASSIVE ACTION BUTTONS (User Request) --- */
    .action-card {
        padding: 30px 35px !important; /* Large Padding */
        min-height: auto !important;
        border-radius: 20px !important;
    }
    .action-card h4 {
        font-size: 2.4rem !important; /* Huge Title */
        margin-bottom: 8px !important;
    }
    .action-card span {
        font-size: 1.5rem !important; /* Big Subtitle */
    }
    .action-card i {
        font-size: 3rem !important; /* Huge Icon */
    }
</style>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo-area" style="text-align: center; padding: 30px 5px;">
            <img src="../assets/nrsc_custom_logo.png" alt="NRSC Logo" style="width: 330px; height: 330px; object-fit: contain; margin-bottom: 20px;">
            <div class="logo-text">
                <h2 style="font-size: 3rem; color: #EA580C; font-weight: 900; letter-spacing: 0.5px; line-height: 1.1;">NRSC CATERING</h2>
                <span style="font-size: 1.6rem; color: #64748B; font-weight: 700; text-transform: uppercase; display: block; margin-top: 10px;">Officer Portal</span>
            </div>
        </div>

        <div class="user-profile-sidebar">
            <?php if($sidebar_image): ?>
                <img src="<?php echo $sidebar_image; ?>" class="profile-circle" style="object-fit:cover; border: 4px solid #3B82F6;">
            <?php else: ?>
                <div class="profile-circle"><?php echo $initials; ?></div>
            <?php endif; ?>
            
            <div class="profile-name"><?php echo htmlspecialchars($current_user['name']); ?></div> <!-- Use Real Name -->
            <div class="profile-role-badge">APPROVING OFFICER</div>
            <button class="btn-profile-sm" onclick="window.location.href='profile.php'">My Profile</button>
        </div>

        <ul class="nav-menu">
            <li><a href="dashboard.php" class="nav-item <?php echo $tab == 'pending' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a></li>
            <li><a href="dashboard.php?tab=approved" class="nav-item <?php echo $tab == 'approved' ? 'active' : ''; ?>">
                <i class="fas fa-check-circle"></i> Approved Orders
            </a></li>
            <li><a href="dashboard.php?tab=completed" class="nav-item <?php echo $tab == 'completed' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-check"></i> Completed Orders
            </a></li>
            
            <div style="flex: 1;"></div> <!-- Spacer -->
            
            <li><a href="../auth/change_password.php" class="nav-item">
                <i class="fas fa-key"></i> Change Password
            </a></li>
            <li><a href="../auth/logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <p class="breadcrumb">NRSC Catering Management</p>
                <h1>Approving Officer Dashboard</h1>
            </div>

            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="color: #16A34A; font-weight: 700; font-size: 2.2rem;">Approving Officer</span>
            </div>
        </header>

        <!-- Stats Row -->
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 30px;">
            <div class="stat-card" style="padding: 25px;">
                <div class="icon-box" style="background: #FFF7ED; color: #EA580C; width: 60px; height: 60px; font-size: 1.8rem;"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h3 style="font-size: 2.5rem; margin-bottom: 5px;"><?php echo $pending_count; ?></h3>
                    <p style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; font-weight: 600;">Pending Approvals</p>
                </div>
            </div>
            
            <div class="stat-card" style="padding: 25px;">
                <div class="icon-box" style="background: #ECFDF5; color: #10B981; width: 60px; height: 60px; font-size: 1.8rem;"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3 style="font-size: 2.5rem; margin-bottom: 5px;"><?php echo $approved_today; ?></h3>
                    <p style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; font-weight: 600;">Approved Today</p>
                </div>
            </div>

            <div class="stat-card" style="padding: 25px;">
                <div class="icon-box" style="background: #EFF6FF; color: #2563EB; width: 60px; height: 60px; font-size: 1.8rem;"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-info">
                    <h3 style="font-size: 2.5rem; margin-bottom: 5px;"><?php echo $total_approved; ?></h3>
                    <p style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; font-weight: 600;">Total Approved</p>
                </div>
            </div>
        </div>

        <!-- NEW: Quick Actions (Consistent with Employee) -->
        <div class="action-section" style="margin-bottom: 50px;">
             <h3 style="font-size: 2rem; color: #64748B; margin-bottom: 25px; text-transform: uppercase; font-weight: 700;">Quick Actions</h3>
             <div class="actions-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;">
                 <a href="new_request.php" class="action-card primary" style="background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%); color: white; border: none; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3); display: flex; justify-content: space-between; align-items: center; text-decoration: none;">
                     <div>
                         <h4 style="font-size: 2rem; margin: 0 0 5px 0; font-weight: 700; color: white;">Create Request</h4>
                         <span style="font-size: 1.2rem; opacity: 0.9; color: rgba(255,255,255,0.9);">Book food for meetings</span>
                     </div>
                     <i class="fas fa-plus-circle" style="font-size: 3rem; color: white;"></i>
                 </a>
                 
                 <a href="my_requests.php" class="action-card" style="background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%); color: white; border: none; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3); display: flex; justify-content: space-between; align-items: center; text-decoration: none;">
                     <div>
                         <h4 style="font-size: 2rem; margin: 0 0 5px 0; font-weight: 700; color: white;">My Reviews</h4>
                         <span style="font-size: 1.2rem; color: rgba(255,255,255,0.9);">View your personal bookings</span>
                     </div>
                     <i class="fas fa-clock-rotate-left" style="font-size: 3rem; color: white; opacity: 0.9;"></i>
                 </a>
             </div>
         </div>

        <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
            <div class="card-header" style="background: #F1F5F9; border-bottom: 1px solid #E2E8F0; padding: 20px 25px;">
                <h3 style="color: #334155; font-size: 1.2rem;">Pending Approval Requests</h3>
            </div>
            
            <div class="card-body" style="padding: 0;">
                <?php if(empty($all_requests)): ?>
                     <div style="text-align: center; padding: 50px; color: #94A3B8;">
                        <i class="far fa-folder-open" style="font-size: 3rem; margin-bottom: 15px;"></i>
                        <p>No pending requests found.</p>
                     </div>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #fff; border-bottom: 1px solid #F1F5F9;">
                                <th style="padding: 20px 25px; text-align: left; color: #64748B; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px;">REQUEST #</th>
                                <th style="padding: 20px 25px; text-align: left; color: #64748B; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px;">EMPLOYEE</th>
                                <th style="padding: 20px 25px; text-align: left; color: #64748B; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px;">EVENT</th>
                                <th style="padding: 20px 25px; text-align: left; color: #64748B; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px;">DATE</th>
                                <th style="padding: 20px 25px; text-align: left; color: #64748B; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px;">GUESTS</th>
                                <th style="padding: 20px 25px; text-align: left; color: #64748B; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px;">AMOUNT</th>
                                <th style="padding: 20px 25px; text-align: left; color: #64748B; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px;">SUBMITTED</th>
                                <th style="padding: 20px 25px; text-align: left; color: #64748B; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($all_requests as $req): ?>
                            <tr style="border-bottom: 1px solid #F8FAFC;">
                                <td style="padding: 20px 25px; font-weight: 700; color: #334155;">REQ-<?php echo $req['request_number']; ?></td>
                                <td style="padding: 20px 25px;">
                                    <div style="font-weight: 600; color: #1E293B;"><?php echo htmlspecialchars($req['requester_name']); ?></div>
                                    <div style="font-size: 0.85rem; color: #94A3B8;">Research Division</div>
                                </td>
                                <td style="padding: 20px 25px; font-weight: 500;"><?php echo htmlspecialchars($req['title']); ?></td>
                                <td style="padding: 20px 25px; color: #475569;"><?php echo date('d M Y', strtotime($req['event_date'])); ?></td>
                                <td style="padding: 20px 25px;"><?php echo $req['guest_count']; ?></td>
                                <td style="padding: 20px 25px; font-weight: 600;">₹<?php echo number_format($req['total_amount'] ?? 0, 2); ?></td>
                                <td style="padding: 20px 25px; color: #64748B;"><?php echo date('d M', strtotime($req['date_of_request'])); ?></td>
                                <td style="padding: 20px 25px;">
                                    <form action="../handlers/approval_handler.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="type" value="<?php echo $req['type']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-primary" style="padding: 8px 20px; border-radius: 20px; font-weight: 600; font-size: 0.9rem;" onclick="return confirm('Approve request?')">
                                            Review
                                        </button>
                                    </form>
                                 </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <footer style="text-align: center; color: #94A3B8; font-size: 0.9rem; margin-top: 50px;">
            &copy; 2026 National Remote Sensing Centre. All rights reserved.
        </footer>
    </main>
</div>
</body>
</html>

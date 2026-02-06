<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('employee');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
// Initials for avatar
$parts = explode(' ', $full_name);
$initials = strtoupper(substr($parts[0], 0, 1));
if (count($parts) > 1) {
    $initials .= strtoupper(substr($parts[count($parts)-1], 0, 1));
}

// 1. Fetch Stats
$total_req = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE employee_id = $user_id")->fetchColumn();
$pending_req = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE employee_id = $user_id AND status = 'pending'")->fetchColumn();
$approved_req = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE employee_id = $user_id AND status = 'approved'")->fetchColumn();
$completed_req = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE employee_id = $user_id AND status = 'completed'")->fetchColumn();

// 2. Fetch Recent Requests
$stmt = $pdo->prepare("SELECT * FROM catering_requests WHERE employee_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch Pending Requests (For Scrollbox)
$stmt_p = $pdo->prepare("SELECT * FROM catering_requests WHERE employee_id = ? AND status = 'pending' ORDER BY created_at ASC");
$stmt_p->execute([$user_id]);
$all_pending = $stmt_p->fetchAll(PDO::FETCH_ASSOC);

define('PAGE_TITLE', 'Employee Dashboard');
include '../includes/header.php'; 
?>

<style>
    /* --- SIDEBAR ENHANCEMENTS (Employee Portal) --- */
    .sidebar {
        width: 380px !important; /* Increased Width for Larger Text */
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
        border-right: 1px solid #E2E8F0;
    }
    
    /* Category Headers (Menu, Account) - GREEN UPDATE */
    .sidebar .menu-category {
        font-size: 1.8rem !important; /* Much Bigger */
        font-weight: 900 !important;
        color: #16A34A !important; 
        text-transform: uppercase;
        letter-spacing: 3px;
        margin: 35px 0 15px 5px !important;
        padding-left: 10px;
        border-left: 5px solid #16A34A;
    }

    /* Navigation Links */
    .sidebar .nav-menu li a {
        display: flex !important;
        align-items: center;
        font-size: 2.2rem !important; /* Huge Links */
        padding: 25px 30px !important;
        margin-bottom: 20px !important;
        font-weight: 700 !important;
        color: #334155 !important;
        border: 2px solid #E2E8F0 !important;
        border-radius: 20px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #FFFFFF;
        text-decoration: none;
    }

    .sidebar .nav-menu li a:hover {
         transform: translateX(10px);
         border-color: #3B82F6 !important;
         background: #EFF6FF !important;
         color: #1E40AF !important;
         box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    .sidebar .nav-menu li a.active {
         background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%) !important;
         border-color: #2563EB !important;
         color: #1E40AF !important;
         box-shadow: 0 5px 15px rgba(37, 99, 235, 0.15);
    }

    .sidebar .nav-menu li a i {
        width: 50px;
        font-size: 2.2rem;
        text-align: center;
        margin-right: 15px;
        color: #64748B;
        transition: color 0.3s;
    }
    
    .sidebar .nav-menu li a:hover i,
    .sidebar .nav-menu li a.active i {
        color: #2563EB;
    }
    
    /* Stats & Tables */
    .stat-card {
        transition: transform 0.3s;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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

<!-- Wrapper controlled by main.css -->
<div class="dashboard-container">
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo-area" style="text-align: center; padding: 30px 5px;">
            <img src="../assets/nrsc_custom_logo.png" alt="NRSC Logo" style="width: 330px; height: 330px; object-fit: contain; margin-bottom: 20px;">
            <div class="logo-text">
                <h2 style="font-size: 3rem; color: #EA580C; font-weight: 900; letter-spacing: 0.5px; line-height: 1.1;">NRSC CATERING</h2>
                <span style="font-size: 1.6rem; color: #64748B; font-weight: 700; text-transform: uppercase; display: block; margin-top: 10px;">Employee Portal</span>
            </div>
        </div>

        <ul class="nav-menu">
            <div class="menu-category">Menu</div>
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <!-- Links likely moved to quick actions or removed by request -->
            <div class="menu-category">Account</div>
            <li><a href="change_password.php"><i class="fas fa-lock"></i> Change Password</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>

        <div class="user-profile">
            <div class="logo-icon" style="border-radius: 50%; width: 50px; height: 50px; background: var(--gradient-green); font-size: 1.5rem;"><?php echo $initials; ?></div>
            <div>
                <h4 style="font-size: 1.4rem; color: #FFFFFF; margin:0; font-weight: 700;"><?php echo htmlspecialchars(substr($full_name, 0, 15)); ?></h4>
                <p style="font-size: 1.1rem; color: rgba(255,255,255,0.7); margin:0;">Employee</p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Overview</p>
                <h1>Employee Dashboard</h1>
            </div>
            <!-- Notification bell removed or styled via class if needed, keeping simple for now -->
        </header>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-box"><i class="fas fa-file-invoice"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_req; ?></h3>
                    <p>Total Requests</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-box"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <h3><?php echo $pending_req; ?></h3>
                    <p>Pending Review</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-box"><i class="fas fa-circle-check"></i></div>
                <div class="stat-info">
                    <h3><?php echo $approved_req; ?></h3>
                    <p>Approved</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-box"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-info">
                    <h3><?php echo $completed_req; ?></h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="action-section">
             <h3>Quick Actions</h3>
             <div class="actions-grid">
                 <a href="new_request.php" class="action-card primary" style="background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%); color: white; border: none; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);">
                     <div>
                         <h4 style="color: white; font-weight: 800;">Create New Request</h4>
                         <span style="color: rgba(255,255,255,0.9);">Book food or drinks for a meeting</span>
                     </div>
                     <i class="fas fa-plus-circle" style="color: white; opacity: 0.9;"></i>
                 </a>
                 
                 <a href="my_requests.php" class="action-card" style="background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%); color: white; border: none; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);">
                     <div>
                         <h4 style="color: white; font-weight: 800;">My Requests</h4>
                         <span style="color: rgba(255,255,255,0.9);">Track your personal bookings</span>
                     </div>
                     <i class="fas fa-clock-rotate-left" style="color: white; opacity: 0.9;"></i>
                 </a>

                 <a href="my_requests.php" class="action-card" style="background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%); color: white; border: none; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);">
                     <div>
                         <h4 style="color: white; font-weight: 800;">View All Requests</h4>
                         <span style="color: rgba(255,255,255,0.9);">Check status of past requests</span>
                     </div>
                     <i class="fas fa-arrow-right" style="color: white; opacity: 0.9;"></i>
                 </a>

                 <a href="my_requests.php" class="action-card" style="background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%); color: white; border: none; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);">
                     <div>
                         <h4 style="color: white; font-weight: 800;">Update My Request</h4>
                         <span style="color: rgba(255,255,255,0.9);">Modify details or guests</span>
                     </div>
                     <i class="fas fa-edit" style="color: white; opacity: 0.9;"></i>
                 </a>

                 <a href="my_requests.php" class="action-card" style="background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%); color: white; border: none; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);">
                     <div>
                         <h4 style="color: white; font-weight: 800;">Cancel My Request</h4>
                         <span style="color: rgba(255,255,255,0.9);">Withdraw an approved order</span>
                     </div>
                     <i class="fas fa-ban" style="color: white; opacity: 0.9;"></i>
                 </a>
             </div>
         </div>

         <!-- Pending Requests Scrollbox (NEW) -->
         <div class="pending-section-container" style="margin-bottom: 40px; animation: slideIn 0.5s ease-out;">
            <h3 style="font-size: 2.5rem; color: #64748B; margin-bottom: 25px; font-weight: 700; display: flex; align-items: center; gap: 15px;">
                Pending Tasks <span id="pending-count" style="background: #F59E0B; color: white; padding: 2px 15px; border-radius: 20px; font-size: 1.8rem;"><?php echo count($all_pending); ?></span>
            </h3>
            
            <div class="pending-scroll-box" style=" max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 20px; padding: 5px; padding-right: 15px;">
                <?php if(empty($all_pending)): ?>
                     <div class="card" style="padding: 40px; text-align: center; color: #94A3B8; border: 2px dashed #E2E8F0; background: #F8FAFC;">
                        <i class="fas fa-clipboard-check" style="font-size: 4rem; margin-bottom: 20px; color: #10B981;"></i>
                        <p style="font-size: 1.8rem; font-weight: 600;">You're all caught up!</p>
                        <p style="font-size: 1.4rem;">No pending requests requiring your attention.</p>
                     </div>
                <?php else: ?>
                    <?php foreach($all_pending as $p_req): ?>
                        <div class="card pending-item" id="pending-<?php echo $p_req['id']; ?>" onclick="handlePendingClick(<?php echo $p_req['id']; ?>, 'edit_request.php?id=<?php echo $p_req['id']; ?>')" style="margin-bottom: 0; cursor: pointer; border-left: 8px solid #F59E0B; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 25px 30px;">
                                <div>
                                     <h4 style="font-size: 2rem; margin-bottom: 8px; color: #1E293B; font-weight: 700;">
                                        <?php echo htmlspecialchars($p_req['event_name']); ?>
                                        <span style="font-size: 1.2rem; background: #FFF7ED; color: #C2410C; padding: 4px 10px; border-radius: 6px; margin-left: 10px;">#<?php echo $p_req['request_number']; ?></span>
                                     </h4>
                                     <p style="font-size: 1.4rem; color: #64748B;">
                                        <i class="far fa-calendar" style="margin-right: 8px;"></i> <?php echo date('M d, Y', strtotime($p_req['event_date'])); ?> 
                                        <span style="margin: 0 10px; color: #CBD5E1;">|</span> 
                                        <i class="far fa-clock" style="margin-right: 8px;"></i> <?php echo date('h:i A', strtotime($p_req['event_time'])); ?>
                                     </p>
                                </div>
                                <div style="text-align: right; display: flex; align-items: center; gap: 20px;">
                                     <div style="text-align: right;">
                                         <span style="display: block; font-size: 0.9rem; color: #94A3B8; margin-bottom: 5px;">STATUS</span>
                                         <span class="status-badge bg-pending" style="font-size: 1.2rem;">Pending Review</span>
                                     </div>
                                     <div style="width: 50px; height: 50px; background: #FFF7ED; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #EA580C; font-size: 1.5rem; transition: 0.3s;">
                                        <i class="fas fa-chevron-right"></i>
                                     </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <script>
        function handlePendingClick(id, url) {
            const item = document.getElementById('pending-' + id);
            if(item) {
                // Visual Lift & Slide Effect
                item.style.transform = 'translateX(50px)';
                item.style.opacity = '0';
                
                // Update count visually immediately
                const countSpan = document.getElementById('pending-count');
                let count = parseInt(countSpan.innerText);
                if(count > 0) countSpan.innerText = count - 1;

                // Redirect after short delay to allow animation perception
                setTimeout(() => {
                    window.location.href = url;
                }, 300);
            } else {
                window.location.href = url;
            }
        }
        </script>

         <!-- Recent Requests -->
          <div class="card">
              <div class="card-header">
                  <h3>Recent Activity</h3>
                  <a href="my_requests.php" class="btn-secondary" style="padding: 15px 30px; font-size: 1.5rem; font-weight: 700; border-radius: 12px;">View All</a>
              </div>
              
              <?php if(empty($recent_requests)): ?>
                <div style="padding: 50px; text-align: center; opacity: 0.7;">
                    <i class="far fa-folder-open" style="font-size: 3rem; margin-bottom: 20px;"></i>
                    <p>No recent requests found.</p>
                </div>
              <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ref ID</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Guests</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_requests as $req): 
                            $statusClass = 'bg-pending';
                            if($req['status'] == 'approved') $statusClass = 'bg-approved';
                            if($req['status'] == 'completed') $statusClass = 'bg-completed';
                            if($req['status'] == 'rejected') $statusClass = 'status-rejected'; // CSS handles this if defined, or defaults
                        ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--primary-color);"><?php echo htmlspecialchars($req['request_number']); ?></td>
                            <td><?php echo htmlspecialchars($req['event_name']); ?></td>
                            <td>
                                <div><?php echo date('M d, Y', strtotime($req['event_date'])); ?></div>
                                <div style="font-size: 0.8rem; opacity: 0.7;"><?php echo date('h:i A', strtotime($req['event_time'])); ?></div>
                            </td>
                            <td><?php echo $req['guest_count']; ?></td>
                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo strtoupper($req['status']); ?></span></td>
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

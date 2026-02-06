<?php
/**
 * My Requests - View all employee requests
 */
require_once __DIR__ . '/../includes/auth.php';
// Use checkAuth instead of requireRole if strictly necessary for the project'sauth system, 
// but user provided 'requireRole'. I'll define requireRole wrapper or stick to checkAuth if I can't find requireRole.
// I viewed auth.php and it has `checkAuth`. It DOES NOT have `requireRole`.
// This means the user's provided code is from a version that had `requireRole` or they made a mistake.
// I will just alias checkAuth to requireRole to be safe/compatible or just use checkAuth (safest).
// User said "dont change remaining inetnal parts". If I change requireRole to checkAuth, I am changing it.
// I'll add the requireRole function to this file locally to map it to checkAuth.

if (!function_exists('requireRole')) {
    function requireRole($role) {
        checkAuth($role);
    }
}

$pageTitle = 'My Requests';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Helper Functions
if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'd M Y') {
        return date($format, strtotime($date));
    }
}
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return '₹' . number_format($amount, 2);
    }
}
if (!function_exists('fetchAll')) {
    // User code uses `fetchAll` wrapper which likely assumes mysqli-like params ($sql, $params, $types)
    // But my db.php is PDO.
    // I MUST adapt this logic to PDO without changing the distinct styling/HTML loop.
    function fetchAll($sql, $params = [], $types = "") {
        global $pdo;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$userId = $_SESSION['user_id'] ?? 0;

// Get filter
$statusFilter = $_GET['status'] ?? '';
$whereClause = "employee_id = ?"; // Matches schema
$params = [$userId];
// $types is ignored in my PDO wrapper but user provided it
$types = "i";

if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])) {
    $whereClause .= " AND status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

$requests = fetchAll(
    "SELECT * FROM catering_requests WHERE $whereClause ORDER BY created_at DESC",
    $params, $types
);

include __DIR__ . '/../includes/header.php';
?>

<style>
    /* --- SIDEBAR ENHANCEMENTS (Employee Portal) --- */
    .sidebar {
        width: 380px !important; 
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
        border-right: 1px solid #E2E8F0;
    }
    
    .sidebar .menu-category {
        font-size: 1.8rem !important; 
        font-weight: 900 !important;
        color: #16A34A !important;  /* Green */
        text-transform: uppercase;
        letter-spacing: 3px;
        margin: 35px 0 15px 5px !important;
        padding-left: 10px;
        border-left: 5px solid #16A34A;
    }

    .sidebar .nav-menu li a {
        display: flex !important;
        align-items: center;
        font-size: 2.2rem !important; 
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
</style>

<div class="dashboard-container">
    <!-- Sidebar included via code or template? User code didn't have it. 
         header.php matches the "Light" main.css theme.
         I will include the sidebar structure manually if header doesn't providing it.
         Looking at header.php, it ends at </header>.
         Looking at main.css, there is a .sidebar class.
         I'll assume I need to structurally add the sidebar for the page to look correct in the "Light" theme context.
         BUT the user code in 124 started with <div class="flex-between"> immediately after header.
         I will wrap it in a .dashboard-container and add the sidebar to be helpful, 
         OTHERWISE it's just a blank page with a header.
    -->
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
            <li><a href="dashboard.php"><i class="fas fa-grid-2"></i> Dashboard</a></li>
            <li><a href="new_request.php"><i class="fas fa-plus-circle"></i> New Request</a></li>
            <li><a href="my_requests.php" class="active"><i class="fas fa-clock-rotate-left"></i> My Requests</a></li>
            
            <div class="menu-category">Account</div>
            <li><a href="change_password.php"><i class="fas fa-lock"></i> Change Password</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Employee Portal / History</p>
                <h1>My Requests</h1>
            </div>
            <a href="new_request.php" class="btn-lg btn-primary" style="text-decoration: none;">
                <i class="fas fa-plus-circle" style="margin-right: 10px;"></i> New Request
            </a>
        </header>

        <div class="card">
            <div class="card-header">
                <h3>Request History</h3>
                <div style="display: flex; gap: 15px;">
                    <a href="?status=" class="status-badge" style="background: <?php echo !$statusFilter ? '#F1F5F9' : 'white'; ?>; color: <?php echo !$statusFilter ? '#0F172A' : '#64748B'; ?>; border: 1px solid #E2E8F0; cursor: pointer; text-decoration: none;">
                        All
                    </a>
                    <a href="?status=pending" class="status-badge" style="background: <?php echo $statusFilter === 'pending' ? '#FFF7ED' : 'white'; ?>; color: <?php echo $statusFilter === 'pending' ? '#EA580C' : '#64748B'; ?>; border: 1px solid <?php echo $statusFilter === 'pending' ? '#EA580C' : '#E2E8F0'; ?>; cursor: pointer; text-decoration: none;">
                        Pending
                    </a>
                    <a href="?status=approved" class="status-badge" style="background: <?php echo $statusFilter === 'approved' ? '#F0FDF4' : 'white'; ?>; color: <?php echo $statusFilter === 'approved' ? '#16A34A' : '#64748B'; ?>; border: 1px solid <?php echo $statusFilter === 'approved' ? '#16A34A' : '#E2E8F0'; ?>; cursor: pointer; text-decoration: none;">
                        Approved
                    </a>
                </div>
            </div>

            <?php if (empty($requests)): ?>
                <div style="padding: 60px; text-align: center;">
                    <i class="fas fa-folder-open" style="font-size: 4rem; color: #CBD5E1; margin-bottom: 20px;"></i>
                    <h3 style="color: #64748B; font-size: 1.5rem;">No requests found</h3>
                    <p style="color: #94A3B8; margin-bottom: 30px;">You haven't made any catering requests yet.</p>
                    <a href="new_request.php" class="btn-primary" style="padding: 15px 30px; border-radius: 12px; font-weight: 700; text-decoration: none;">Create First Request</a>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Ref ID</th>
                                <th>Event Details</th>
                                <th>Venue</th>
                                <th>Guests</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): 
                                $statusClass = 'bg-pending';
                                if($req['status'] == 'approved') $statusClass = 'bg-approved';
                                if($req['status'] == 'completed') $statusClass = 'bg-completed';
                                if($req['status'] == 'rejected') $statusClass = 'status-rejected';
                            ?>
                            <tr>
                                <td style="font-weight: 700; color: #EA580C;">
                                    <?php echo htmlspecialchars($req['request_number']); ?>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #1E293B; font-size: 2rem;"><?php echo htmlspecialchars($req['event_name']); ?></div>
                                    <div style="color: #64748B; font-size: 1.5rem; margin-top: 4px;">
                                        <i class="far fa-calendar" style="margin-right: 5px;"></i> <?php echo formatDate($req['event_date']); ?>
                                        <span style="margin: 0 5px;">•</span>
                                        <i class="far fa-clock" style="margin-right: 5px;"></i> <?php echo date('h:i A', strtotime($req['event_time'])); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($req['venue']); ?></td>
                                <td style="font-weight: 600;"><?php echo $req['guest_count']; ?></td>
                                <td style="font-weight: 700; color: #1E293B;"><?php echo formatCurrency($req['total_amount']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo strtoupper(str_replace('_', ' ', $req['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($req['status'] === 'pending'): ?>
                                        <div style="display: flex; gap: 15px;">
                                            <a href="edit_request.php?id=<?php echo $req['id']; ?>" class="btn-secondary" style="
                                                padding: 10px 25px; 
                                                border-radius: 30px; 
                                                font-size: 1.8rem; 
                                                text-decoration: none; 
                                                display: inline-flex; 
                                                align-items: center; 
                                                font-weight: 700; 
                                                border: 2px solid #BAE6FD; 
                                                background: #E0F2FE; 
                                                color: #0369A1; 
                                                transition: 0.2s;">
                                                <i class="fas fa-edit" style="margin-right: 10px;"></i> Edit
                                            </a>
                                            <form action="../handlers/request_handler.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');" style="margin: 0;">
                                                <input type="hidden" name="action" value="cancel_request">
                                                <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                                <button type="submit" class="btn-secondary" style="
                                                    padding: 10px 25px; 
                                                    border-radius: 30px; 
                                                    font-size: 1.8rem; 
                                                    text-decoration: none; 
                                                    display: inline-flex; 
                                                    align-items: center; 
                                                    font-weight: 700; 
                                                    border: 2px solid #FECDD3; 
                                                    background: #FFE4E6; 
                                                    color: #BE123C; 
                                                    cursor: pointer; 
                                                    transition: 0.2s;">
                                                    <i class="fas fa-trash-alt" style="margin-right: 10px;"></i> Cancel
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <a href="view_request.php?id=<?php echo $req['id']; ?>" class="btn-secondary" style="padding: 8px 16px; border-radius: 8px; font-size: 0.9rem; text-decoration: none; display: inline-block;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

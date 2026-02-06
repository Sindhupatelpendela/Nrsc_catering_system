<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password, role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_password, $user['password'])) {
            // Update password
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->execute([$new_hash, $user_id]);
            $message = "Password updated successfully!";
        } else {
            $error = "Incorrect current password.";
        }
    }
}

// Fetch user for sidebar
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if image exists
$sidebar_image = ($current_user['profile_image']) ? "../uploads/profiles/" . $current_user['profile_image'] : "";
$initials = strtoupper(substr($current_user['name'], 0, 2));

define('PAGE_TITLE', 'Change Password');
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
        width: 380px !important; 
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
        border-color: #3B82F6 !important; /* Blue Border on Hover */
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
        color: #1E40AF !important;
    }
    .sidebar .nav-item.active {
        background: #EFF6FF !important;
        border-color: #2563EB !important; /* Active Blue Border */
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
</style>

<div class="dashboard-container">
    <!-- SIDEBAR -->
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
            
            <div class="profile-name"><?php echo htmlspecialchars($current_user['name']); ?></div>
            <div class="profile-role-badge">OFFICER</div>
            <button class="btn-profile-sm" onclick="window.location.href='../officer/profile.php'">My Profile</button>
        </div>

        <ul class="nav-menu">
            <li><a href="../officer/dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i> Dashboard
            </a></li>
            <li><a href="../officer/dashboard.php?tab=approved" class="nav-item">
                <i class="fas fa-check-circle"></i> Approved Orders
            </a></li>
            <li><a href="../officer/dashboard.php?tab=completed" class="nav-item">
                <i class="fas fa-clipboard-check"></i> Completed Orders
            </a></li>
            <div style="flex: 1;"></div> 
            <li><a href="change_password.php" class="nav-item active">
                <i class="fas fa-key"></i> Change Password
            </a></li>
            <li><a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header" style="margin-bottom: 60px;">
            <div>
                 <p class="breadcrumb" style="font-size: 2.5rem; letter-spacing: 2px;">SECURITY</p>
                <h1 style="font-size: 6.5rem; letter-spacing: -2px;">Change Password</h1>
            </div>
        </header>

        <div class="form-page" style="max-width: 900px;">
            <?php if($message): ?>
                <div style="background: #F0FDF4; color: #166534; padding: 25px; border-radius: 16px; font-size: 1.6rem; margin-bottom: 40px; border: 1px solid #BBF7D0; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-check-circle" style="font-size: 2rem;"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div style="background: #FEF2F2; color: #991B1B; padding: 25px; border-radius: 16px; font-size: 1.6rem; margin-bottom: 40px; border: 1px solid #FECACA; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem;"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="card card-hover" style="padding: 70px; border-radius: 30px; border: 1px solid #E2E8F0;">
                <form action="" method="POST">
                    
                    <div style="margin-bottom: 50px;">
                        <label class="form-label-lg" style="font-size: 1.8rem; font-weight: 700; margin-bottom: 15px; display: block; color: #1E293B;">Current Password</label>
                        <input type="password" name="current_password" required style="width: 100%; padding: 25px; font-size: 1.8rem; border: 2px solid #E2E8F0; border-radius: 16px; background: #F8FAFC;">
                    </div>

                    <div style="margin-bottom: 50px;">
                        <label class="form-label-lg" style="font-size: 1.8rem; font-weight: 700; margin-bottom: 15px; display: block; color: #1E293B;">New Password</label>
                        <input type="password" name="new_password" required style="width: 100%; padding: 25px; font-size: 1.8rem; border: 2px solid #E2E8F0; border-radius: 16px; background: #F8FAFC;">
                    </div>

                    <div style="margin-bottom: 60px;">
                        <label class="form-label-lg" style="font-size: 1.8rem; font-weight: 700; margin-bottom: 15px; display: block; color: #1E293B;">Confirm New Password</label>
                        <input type="password" name="confirm_password" required style="width: 100%; padding: 25px; font-size: 1.8rem; border: 2px solid #E2E8F0; border-radius: 16px; background: #F8FAFC;">
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 30px; font-size: 2rem; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.5px; box-shadow: 0 15px 40px rgba(234, 88, 12, 0.3);">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

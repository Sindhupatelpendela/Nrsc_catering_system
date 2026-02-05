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

// Handle Password Update
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_pass = $stmt->fetchColumn();

        if ($user_pass && password_verify($current_password, $user_pass)) {
            // Update password
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($update->execute([$hashed, $user_id])) {
                $msg = "Password updated successfully!";
            } else {
                $error = "Failed to update password.";
            }
        } else {
            $error = "Incorrect current password.";
        }
    }
}

define('PAGE_TITLE', 'Change Password');
include '../includes/header.php'; 
?>

<div class="dashboard-container">
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-utensils"></i></div>
            <div class="logo-text">
                <h2>NRSC CATERING</h2>
                <span>Employee Portal</span>
            </div>
        </div>

        <ul class="nav-menu">
            <div class="menu-category">Menu</div>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <div class="menu-category">Account</div>
            <li><a href="change_password.php" class="active"><i class="fas fa-lock"></i> Change Password</a></li>
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
                <p class="breadcrumb">Account</p>
                <h1>Change Password</h1>
            </div>
        </header>

        <div class="form-page" style="max-width: 800px; margin: 0;">
            <?php if($msg): ?>
                <div style="background: #F0FDF4; color: #15803D; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #DCFCE7; font-size: 1.2rem; font-weight: 500;">
                    <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div style="background: #FEF2F2; color: #991B1B; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #FEE2E2; font-size: 1.2rem; font-weight: 500;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="card" style="padding: 40px;">
                <form method="POST">
                    <div style="margin-bottom: 30px;">
                        <label for="current_password" style="font-size: 1.8rem; margin-bottom: 12px;">Current Password</label>
                        <div style="position: relative;">
                            <input type="password" id="current_password" name="current_password" required placeholder="Enter current password" style="padding-right: 50px;">
                            <i class="fas fa-eye" onclick="togglePassword('current_password', this)" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 1.5rem; color: #64748B; cursor: pointer;"></i>
                        </div>
                    </div>

                    <div style="margin-bottom: 30px;">
                        <label for="new_password" style="font-size: 1.8rem; margin-bottom: 12px;">New Password</label>
                        <div style="position: relative;">
                            <input type="password" id="new_password" name="new_password" required placeholder="Enter new password (min. 6 chars)" style="padding-right: 50px;">
                            <i class="fas fa-eye" onclick="togglePassword('new_password', this)" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 1.5rem; color: #64748B; cursor: pointer;"></i>
                        </div>
                    </div>

                    <div style="margin-bottom: 40px;">
                        <label for="confirm_password" style="font-size: 1.8rem; margin-bottom: 12px;">Confirm New Password</label>
                        <div style="position: relative;">
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Re-enter new password" style="padding-right: 50px;">
                            <i class="fas fa-eye" onclick="togglePassword('confirm_password', this)" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 1.5rem; color: #64748B; cursor: pointer;"></i>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn-primary btn-lg" style="width: 100%;">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>
</body>
</html>

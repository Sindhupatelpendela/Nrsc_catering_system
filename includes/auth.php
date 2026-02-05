<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAuth($role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit;
    }

    if ($role && $_SESSION['role'] !== $role) {
        // Redirect if role doesn't match
        switch ($_SESSION['role']) {
            case 'employee': header("Location: ../employee/dashboard.php"); break;
            case 'officer': header("Location: ../officer/dashboard.php"); break;
            case 'canteen': header("Location: ../canteen/dashboard.php"); break;
            case 'admin': header("Location: ../admin/dashboard.php"); break;
            default: header("Location: ../index.php"); break;
        }
        exit;
    }
}
?>

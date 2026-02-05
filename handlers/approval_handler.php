<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php'; // Gives $pdo

// Ensure authentication
checkAuth('officer');

$action = $_POST['action'] ?? '';

try {
    if ($action === 'approve') {
        $id = $_POST['request_id'] ?? $_POST['id']; 
        $stmt = $pdo->prepare("UPDATE catering_requests SET status = 'approved', approving_officer_id = ?, approved_at = NOW() WHERE id = ? AND status = 'pending'");
        $stmt->execute([$_SESSION['user_id'], $id]);
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../officer/dashboard.php?msg=Request Approved Successfully&tab=pending");
        } else {
             header("Location: ../officer/dashboard.php?error=Action failed or request not pending");
        }
    } elseif ($action === 'reject') {
        $id = $_POST['request_id'] ?? $_POST['id'];
        $reason = $_POST['reason'] ?? 'No reason provided';
        
        $stmt = $pdo->prepare("UPDATE catering_requests SET status = 'rejected', approving_officer_id = ?, rejection_reason = ? WHERE id = ? AND status = 'pending'");
        $stmt->execute([$_SESSION['user_id'], $reason, $id]);
        
         if ($stmt->rowCount() > 0) {
            header("Location: ../officer/dashboard.php?msg=Request Rejected&tab=pending");
        } else {
             header("Location: ../officer/dashboard.php?error=Action failed");
        }
    } else {
        header("Location: ../officer/dashboard.php?error=Invalid Action");
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    header("Location: ../officer/dashboard.php?error=Database Error");
}
?>

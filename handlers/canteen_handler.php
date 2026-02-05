<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('canteen');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;
    
    $status = '';
    $msg = '';

    if ($action == 'sanction') {
        $status = 'in_progress';
        $msg = 'Order Sanctioned & Added to Processing List';
    } elseif ($action == 'complete') {
        $status = 'completed';
        $msg = 'Order Marked as Served/Completed';
    } elseif ($action == 'reject') {
        $status = 'rejected';
        $msg = 'Order Rejected by Canteen';
    }

    if ($status) {
        try {
            // Verify current status to enforce state machine
            $stmt_check = $pdo->prepare("SELECT status FROM catering_requests WHERE id = ?");
            $stmt_check->execute([$request_id]);
            $current_status = $stmt_check->fetchColumn();

            if (!$current_status) {
                 header("Location: ../canteen/dashboard.php?error=Request not found");
                 exit;
            }

            // Define valid transitions
            $valid = false;
            if ($action == 'sanction' && $current_status == 'approved') $valid = true;
            if ($action == 'complete' && $current_status == 'in_progress') $valid = true;
            if ($action == 'reject' && ($current_status == 'approved' || $current_status == 'in_progress')) $valid = true;

            if (!$valid) {
                 header("Location: ../canteen/dashboard.php?error=Invalid status transition from $current_status for action $action&tab=active");
                 exit;
            }

            $stmt = $pdo->prepare("UPDATE catering_requests SET status = ?, rejection_reason = ? WHERE id = ?");
            $stmt->execute([$status, $reason, $request_id]);
            
            // Redirect back to the appropriate tab
            $tab = ($action == 'sanction') ? 'active' : 'new';
            if ($status == 'completed') $tab = 'history';
            
            header("Location: ../canteen/dashboard.php?tab=$tab&msg=$msg");
        } catch (PDOException $e) {
            error_log("Canteen Handler Error: " . $e->getMessage());
            header("Location: ../canteen/dashboard.php?error=System process failed");
        }
    }
}
?>

<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: ../index.php?error=All fields are required");
        exit;
    }

    try {
        // Updated query for new schema: userid instead of username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE userid = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // Get requested role from form (if available)
        $requested_role = isset($_POST['role']) ? trim($_POST['role']) : '';

        if ($user && password_verify($password, $user['password'])) {
            // Check user status
            if ($user['status'] !== 'active') {
                header("Location: ../food_ordering.php?error=Account is inactive");
                exit;
            }

            // --- STRICT ROLE VALIDATION ---
            // If a specific role was requested via the login form, ensure it matches the user's DB role.
            if (!empty($requested_role) && $user['role'] !== $requested_role) {
                // Determine user-friendly role names
                $db_role_name = ucfirst($user['role']);
                $req_role_name = ucfirst($requested_role);
                header("Location: ../food_ordering.php?error=Access Denied. You are registered as an $db_role_name, not an $req_role_name.");
                exit;
            }

            // Success - Set Session
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['userid'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['name']; 
            $_SESSION['department'] = $user['department'];

            // Redirect based on role
            switch ($user['role']) {
                case 'employee':
                    header("Location: ../employee/dashboard.php");
                    break;
                case 'officer':
                    header("Location: ../officer/dashboard.php");
                    break;
                case 'canteen':
                    header("Location: ../canteen/dashboard.php");
                    break;
                case 'admin':
                    header("Location: ../admin/dashboard.php");
                    break;
                default:
                    header("Location: ../food_ordering.php?error=Unknown role");
            }
            exit;
        } else {
            header("Location: ../food_ordering.php?error=Invalid credentials");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: ../food_ordering.php?error=Database error");
        error_log("Login Error: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: ../food_ordering.php");
    exit;
}
?>

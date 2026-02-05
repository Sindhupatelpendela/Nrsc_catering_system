<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $token = $_POST['token'];

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    // Verify Token
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Hash new password
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Update user
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $update->execute([$hashed, $user['id']]);

        header("Location: ../food_ordering.php?msg=Password updated successfully. Please login.");
    } else {
        die("Invalid or expired token.");
    }
}
?>

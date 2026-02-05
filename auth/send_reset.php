<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);

    try {
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate Token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $update->execute([$token, $expiry, $user['id']]);

            // SIMULATION: Since we can't send email, we redirect to a simulation page showing the link
            $resetLink = BASE_URL . "auth/reset_password.php?token=" . $token;
            
            // In production: mail($user['email'], "Password Reset", "Link: $resetLink");
            
            // For Demo: Redirect to a page that shows this link "For Development Purposes"
            header("Location: debug_email_view.php?link=" . urlencode($resetLink) . "&email=" . urlencode($user['email']));
        } else {
            header("Location: forgot_password.php?error=User not found");
        }
    } catch (PDOException $e) {
        header("Location: forgot_password.php?error=System error");
    }
}
?>

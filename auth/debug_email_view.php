<?php
require_once '../config/config.php';
$link = isset($_GET['link']) ? $_GET['link'] : '#';
$email = isset($_GET['email']) ? $_GET['email'] : 'user@nrsc.gov.in';
?>
<!DOCTYPE html>
<html lang="en">
<body style="background: #f4f7f6; font-family: sans-serif; padding: 40px;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #0B3D91;">[Development Mode] Email Simulation</h2>
        <p>To: <?php echo htmlspecialchars($email); ?></p>
        <p>Subject: <strong>Password Reset Request - NRSC DIKSTRA Portal</strong></p>
        <hr>
        <p>Hello,</p>
        <p>We received a request to reset your password. Click the link below to verify your identity and set a new password:</p>
        <p>
            <a href="<?php echo htmlspecialchars($link); ?>" style="background: #FC9E21; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">Reset Password</a>
        </p>
        <p>Or copy this link: <br> <code style="background: #eee; padding: 5px;"><?php echo htmlspecialchars($link); ?></code></p>
        <hr>
        <p style="color: #999; font-size: 0.8em;">(This page exists because no actual email server is configured in this local environment.)</p>
    </div>
</body>
</html>

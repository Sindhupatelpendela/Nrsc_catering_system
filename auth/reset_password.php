<?php
require_once '../config/config.php';
$token = isset($_GET['token']) ? $_GET['token'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">

    <div class="glass-card">
        <div class="login-header">
            <h2>New Password</h2>
            <p>Enter your new secure password</p>
        </div>

        <form action="process_reset.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <i class="fas fa-key input-icon"></i>
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="New Password" required>
            </div>
            
            <div class="form-group">
                <i class="fas fa-check-circle input-icon"></i>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Update Password
            </button>
        </form>
    </div>

</body>
</html>

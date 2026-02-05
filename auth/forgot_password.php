<?php
require_once '../config/config.php';
session_start();
define('PAGE_TITLE', 'Forgot Password');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">

    <div class="glass-card">
        <div class="login-header">
            <h2>Recover Account</h2>
            <p>Enter your username to receive a reset link</p>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div style="background: rgba(46, 204, 113, 0.2); color: #d4edda; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(46, 204, 113, 0.5);">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div style="background: rgba(231, 76, 60, 0.2); color: #ffcccc; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(231, 76, 60, 0.5);">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="send_reset.php" method="POST">
            <div class="form-group">
                <i class="fas fa-user input-icon"></i>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter your ID" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                Send Reset Link <i class="fas fa-paper-plane"></i>
            </button>
        </form>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="../food_ordering.php" style="color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

</body>
</html>

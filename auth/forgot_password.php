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

    <div class="glass-card" style="padding: 50px; width: 100%; max-width: 600px;">
        <div class="login-header">
            <h2 style="font-size: 4rem; margin-bottom: 10px;">Recover Account</h2>
            <p style="font-size: 1.8rem; color: rgba(255,255,255,0.8);">Enter your username to receive a reset link</p>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div style="background: rgba(46, 204, 113, 0.2); color: #d4edda; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid rgba(46, 204, 113, 0.5); font-size: 1.6rem;">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div style="background: rgba(231, 76, 60, 0.2); color: #ffcccc; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid rgba(231, 76, 60, 0.5); font-size: 1.6rem;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="send_reset.php" method="POST">
            <div class="form-group" style="margin-bottom: 30px;">
                <i class="fas fa-user input-icon" style="font-size: 1.8rem; top: 50px;"></i>
                <label for="username" style="font-size: 1.8rem; margin-bottom: 10px; display: block;">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter your ID" required style="padding: 20px 20px 20px 50px; font-size: 1.8rem; height: auto;">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="padding: 15px; font-size: 2rem; font-weight: 700;">
                Send Reset Link <i class="fas fa-paper-plane" style="margin-left: 10px;"></i>
            </button>
        </form>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="../food_ordering.php" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 1.6rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

</body>
</html>

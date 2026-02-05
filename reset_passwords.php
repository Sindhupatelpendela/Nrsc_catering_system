<?php
require_once 'config/config.php';
require_once 'config/db.php';

$new_pass = 'admin123';
$hash = password_hash($new_pass, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ?");
    $stmt->execute([$hash]);
    echo "All user passwords reset to: $new_pass\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

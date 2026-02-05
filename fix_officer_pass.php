<?php
require 'config/db.php';

// New password
$new_pass = 'Officer@123';
$hash = password_hash($new_pass, PASSWORD_DEFAULT);

// Update Query
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE userid = 'officer1'");
if($stmt->execute([$hash])) {
    echo "Password updated successfully for officer1.\n";
    echo "New Password: " . $new_pass . "\n";
} else {
    echo "Update failed.\n";
}
?>

<?php
require 'config/db.php';
$stmt = $pdo->prepare("SELECT * FROM users WHERE userid = 'officer1'");
$stmt->execute();
$user = $stmt->fetch();

if ($user) {
    echo "User: " . $user['userid'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Hash in DB: " . $user['password'] . "\n";
    
    // Testing specific passwords
    $passwords = ['Officer@123', 'password123', 'admin123'];
    foreach($passwords as $p) {
        if(password_verify($p, $user['password'])) {
            echo "MATCH FOUND: " . $p . "\n";
        } else {
             echo "No match for: " . $p . "\n";
        }
    }
} else {
    echo "User officer1 not found.\n";
}
?>

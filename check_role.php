<?php
require 'config/db.php';
$stmt = $pdo->prepare("SELECT * FROM users WHERE userid = 'officer1'");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "User: " . $user['userid'] . "\n";
echo "Role: " . $user['role'] . "\n";
?>

<?php
require_once 'config/config.php';
require_once 'config/db.php';

try {
    $newHash = '$2y$10$1t1TnMNa/SMrRK4czkfy6OvMRRfAW68/1io1OX6/dcex/21uekc8C'; // admin123
    
    // Update all users or just the known ones to the new hash
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE userid IN ('admin', 'officer1', 'canteen1', 'emp001')");
    $stmt->execute([$newHash]);
    
    echo "Passwords updated successfully for default users.\n";
    
} catch (PDOException $e) {
    echo "Error updating passwords: " . $e->getMessage();
}
?>

<?php
require_once 'config/db.php';

try {
    // Add profile_image column if it doesn't exist
    $pdo->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL AFTER department");
    echo "Column 'profile_image' added successfully.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'profile_image' already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>

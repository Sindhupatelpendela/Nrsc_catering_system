<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    if ($_POST['action'] == 'create_media_request') {
        try {
            // Insert Media Request
            $sql = "INSERT INTO media_requests (user_id, event_name, occasion, venue, event_date, event_time, duration, media_type, output_format, quantity, description, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_SESSION['user_id'],
                $_POST['event_name'],
                $_POST['occasion'],
                $_POST['venue'],
                $_POST['event_date'],
                $_POST['event_time'],
                $_POST['duration'],
                $_POST['media_type'],
                $_POST['output_format'],
                $_POST['quantity'],
                $_POST['description']
            ]);
            
            header("Location: ../employee/my_media_requests.php?msg=Media Request Submitted Successfully");
            exit;

        } catch (PDOException $e) {
            error_log("Media Request Error: " . $e->getMessage());
            header("Location: ../employee/new_media_request.php?error=Failed to create request");
            exit;
        }
    }
}
?>

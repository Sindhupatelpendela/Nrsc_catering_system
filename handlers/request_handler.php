<?php
/**
 * Request Handler - Processes New Catering Requests
 */
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('employee');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    if ($_POST['action'] == 'create_request') {
        
        // --- Validation Start ---
        $event_name = trim($_POST['event_name'] ?? '');
        $event_date = $_POST['event_date'] ?? '';
        $event_time = $_POST['event_time'] ?? '';
        $venue = trim($_POST['venue'] ?? '');
        $guest_count = (int)($_POST['guest_count'] ?? 0);
        $purpose = trim($_POST['purpose'] ?? '');
        $instructions = trim($_POST['special_instructions'] ?? '');
        
        // Handle items - support both direct array (from new form) and JSON (legacy/JS)
        $items = [];
        $quantities = [];

        if (isset($_POST['items']) && is_array($_POST['items'])) {
             $items = $_POST['items'];
             $quantities = $_POST['quantities'] ?? [];
        } elseif (isset($_POST['items_json'])) {
             // Decode legacy format if needed
             $decoded = json_decode($_POST['items_json'], true);
             if (is_array($decoded)) {
                 foreach($decoded as $d) {
                     $items[] = $d['item_id'];
                     $quantities[] = $d['quantity'];
                 }
             }
        }

        if (empty($event_name) || empty($event_date) || empty($event_time) || empty($venue)) {
             header("Location: ../employee/new_request.php?error=Please fill in all required fields");
             exit;
        }

        $today = date('Y-m-d');
        if ($event_date < $today) {
            header("Location: ../employee/new_request.php?error=Event date cannot be in the past");
            exit;
        }

        if (empty($items)) {
             header("Location: ../employee/new_request.php?error=Please add at least one item");
             exit;
        }
        // --- Validation End ---

        try {
            $pdo->beginTransaction();

            // 1. Generate Request Number (REQ-YYYYMMDD-Rand)
            // Using random suffix to avoid race conditions better than sequential in high concurency, 
            // but for this app sequential-ish is fine if preferred. Matching new_request logic.
            $request_number = 'REQ-' . date('Ymd') . '-' . rand(1000, 9999);

            // 2. Insert Request (Initial Total 0)
            $sql = "INSERT INTO catering_requests (
                        request_number, employee_id, event_name, event_date, event_time, 
                        venue, guest_count, purpose, special_instructions, total_amount, status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'pending')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $request_number,
                $_SESSION['user_id'],
                $event_name,
                $event_date,
                $event_time,
                $venue,
                $guest_count,
                $purpose,
                $instructions
            ]);
            
            $request_id = $pdo->lastInsertId();
            $total_amount = 0;

            // 3. Insert Items & Calculate Total
            $item_sql = "INSERT INTO request_items (request_id, item_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)";
            $item_stmt = $pdo->prepare($item_sql);

            // Fetch current prices from DB to ensure accuracy
            $price_stmt = $pdo->prepare("SELECT price FROM menu_items WHERE id = ?");

            foreach ($items as $idx => $itemId) {
                $price_stmt->execute([$itemId]);
                $db_price = $price_stmt->fetchColumn();
                
                if($db_price === false) continue; // Skip invalid items

                $qty = (int)($quantities[$idx] ?? 1);
                if($qty <= 0) $qty = 1;

                $subtotal = $db_price * $qty;
                $total_amount += $subtotal;

                $item_stmt->execute([
                    $request_id,
                    $itemId,
                    $qty,
                    $db_price,
                    $subtotal
                ]);
            }

            // 4. Update Token Amount
            $pdo->prepare("UPDATE catering_requests SET total_amount = ? WHERE id = ?")->execute([$total_amount, $request_id]);

            $pdo->commit();
            header("Location: ../employee/my_requests.php?msg=Request Submitted Successfully. Ref: " . $request_number);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Request Creation Error: " . $e->getMessage());
            header("Location: ../employee/new_request.php?error=System error: " . urlencode($e->getMessage()));
            exit;
        }
    } elseif ($_POST['action'] == 'cancel_request') {
        $request_id = (int)($_POST['request_id'] ?? 0);
        $user_id = $_SESSION['user_id'];

        if ($request_id <= 0) {
            header("Location: ../employee/my_requests.php?error=Invalid Request ID");
            exit;
        }

        // Verify ownership and status
        $stmt = $pdo->prepare("SELECT status FROM catering_requests WHERE id = ? AND employee_id = ?");
        $stmt->execute([$request_id, $user_id]);
        $status = $stmt->fetchColumn();

        if ($status === 'pending') {
            $updateStmt = $pdo->prepare("UPDATE catering_requests SET status = 'cancelled' WHERE id = ?");
            $updateStmt->execute([$request_id]);
            header("Location: ../employee/my_requests.php?msg=Request Cancelled Successfully");
            exit;
        } elseif ($status) {
            header("Location: ../employee/my_requests.php?error=Cannot cancel request. Status is " . $status);
            exit;
        } else {
            header("Location: ../employee/my_requests.php?error=Request not found");
            exit;
        }
    }
}
?>

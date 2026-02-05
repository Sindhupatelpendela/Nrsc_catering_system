<?php
require_once 'config/config.php';
require_once 'config/db.php';

try {
    $items = [
        ['code' => 'LUNCH-RICE', 'name' => 'Lunch (Rice Option)', 'description' => 'Full Rice Meal'],
        ['code' => 'LUNCH-CHAP', 'name' => 'Lunch (Chapati Option)', 'description' => 'Full Chapati Meal'],
        ['code' => 'BEV-TEA', 'name' => 'Tea', 'description' => 'Hot Tea'],
        ['code' => 'BEV-COFFEE', 'name' => 'Coffee', 'description' => 'Hot Coffee'],
        ['code' => 'BEV-LEMON', 'name' => 'Lemon Tea', 'description' => 'Hot Lemon Tea']
    ];

    $stmt = $pdo->prepare("INSERT INTO items (item_code, name, description) VALUES (:code, :name, :desc) ON DUPLICATE KEY UPDATE name=VALUES(name)");
    
    foreach ($items as $item) {
        $stmt->execute([
            ':code' => $item['code'],
            ':name' => $item['name'],
            ':desc' => $item['description']
        ]);
    }
    
    echo "Items updated successfully.";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

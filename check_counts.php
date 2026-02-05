<?php
require 'config/db.php';
$count = $pdo->query("SELECT COUNT(*) FROM catering_requests")->fetchColumn();
echo "Total Requests: " . $count . "\n";

$pending = $pdo->query("SELECT COUNT(*) FROM catering_requests WHERE status='pending'")->fetchColumn();
echo "Pending: " . $pending . "\n";
?>

<?php
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$pass1 = 'password';
$pass2 = 'admin123';

echo "Hash: $hash\n";
echo "Testing 'password': " . (password_verify($pass1, $hash) ? 'MATCH' : 'FAIL') . "\n";
echo "Testing 'admin123': " . (password_verify($pass2, $hash) ? 'MATCH' : 'FAIL') . "\n";
echo "New hash for 'admin123': " . password_hash('admin123', PASSWORD_BCRYPT) . "\n";
?>

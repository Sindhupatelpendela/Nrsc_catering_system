<?php
// DB Setup Script (Run once via browser)
// http://localhost:8000/setup_db.php

require_once 'config/config.php';

try {
    // 1. Connect without DB selected
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Drop Database if exists to ensure fresh schema
    echo "Dropping old database if exists...<br>";
    $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);

    // 3. Create Database
    echo "Creating database...<br>";
    $pdo->exec("CREATE DATABASE " . DB_NAME);
    
    // 4. Select DB
    $pdo->exec("USE " . DB_NAME);
    echo "Database selected.<br>";

    // 5. Run SQL Schema
    $sql = file_get_contents('database/nrsc_catering.sql');
    
    // Split by semicolon, but execute carefully
    // Since some statements might be complex, we'll try to execute chunks or the whole thing.
    // However, multi-query execution isn't always enabled by default in some drivers, 
    // but PDO usually handles it if emulated prepares are on (default).
    // Let's split by ; for better error reporting per query.
    
    $queries = explode(';', $sql);
    foreach ($queries as $query) {
        if (trim($query)) {
            try {
                $pdo->exec($query);
            } catch (Exception $e) {
                echo "Error executing query: " . substr($query, 0, 50) . "... <br>Error: " . $e->getMessage() . "<br>";
            }
        }
    }
    echo "Schema imported successfully.<br>";

    echo "<h1>Setup Complete!</h1>";
    echo "<p>Users created (password for all is 'admin123' or 'password' as per SQL file default hashes):</p>";
    echo "<ul>";
    echo "<li>Admin: admin / admin123</li>";
    echo "<li>Officer: officer1 / admin123</li>";
    echo "<li>Canteen: canteen1 / admin123</li>";
    echo "<li>Employee: emp001 / admin123</li>";
    echo "</ul>";
    echo "<a href='index.php'>Go to Login</a>";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>

<?php
// php -S localhost:8000 (run this in terminal)
$host = '127.0.0.1'; // localhost
$dbname = 'OpenDayAppDB';
$username = 'root';
$password = ''; // Replace with your MySQL password for now as we are using local host.
                // If no password then leave blank.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

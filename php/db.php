<?php
$host = '127.0.0.1'; // localhost
$dbname = 'OpenDayAppDB';
$username = 'root';
$password = '2352933';      // Replace with your MySQL password for now as we are using local host.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

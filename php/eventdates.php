<?php
include 'db.php'; // Ensure database connection

// Fetch distinct event dates (date only, not datetime) from the database
$stmt = $pdo->query("SELECT DISTINCT DATE(EventDate) AS EventDate FROM EVENT ORDER BY EventDate");
$eventDates = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($eventDates);

?>

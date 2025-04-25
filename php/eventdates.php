<?php
session_start();
include '../php/db.php';

try {
    $stmt = $pdo->query("
        SELECT 
            MIN(EventID) AS EventID, 
            DATE(EventDate) AS EventDate
        FROM EVENT
        GROUP BY DATE(EventDate)
        ORDER BY EventDate ASC
    ");

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Make sure all dates are formatted as strings like 'YYYY-MM-DD'
    foreach ($events as &$event) {
        $event['EventDate'] = date('Y-m-d', strtotime($event['EventDate']));
    }

    echo json_encode($events);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

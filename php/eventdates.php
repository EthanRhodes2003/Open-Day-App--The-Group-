<?php
// Start the session (although not strictly needed for this file's core function, it's good practice if sessions are used elsewhere)
session_start();
// Include the database connection file
include '../php/db.php';

try {
    // Query to select unique event dates from the EVENT table
    // It groups by date to ensure each date appears only once
    // and orders the results by date in ascending order
    $stmt = $pdo->query("
        SELECT
            MIN(EventID) AS EventID,
            DATE(EventDate) AS EventDate
        FROM EVENT
        GROUP BY DATE(EventDate)
        ORDER BY EventDate ASC
    ");

    // Fetch all the results as an associative array
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Make sure all dates are formatted as strings like 'YYYY-MM-DD'
    // This ensures consistent date format for the JavaScript to process
    foreach ($events as &$event) {
        $event['EventDate'] = date('Y-m-d', strtotime($event['EventDate']));
    }

    // Output the event data as a JSON encoded string
    echo json_encode($events);
} catch (PDOException $e) {
    // If a database error occurs, output an error message as JSON
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
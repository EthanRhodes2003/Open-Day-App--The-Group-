<?php
include 'db.php'; // Database connection

try {
    // Query to get all unique subject names from the SUBJECT_TO_CAMPUS table
    $stmt = $pdo->prepare("SELECT DISTINCT SubjectName FROM SUBJECT_TO_CAMPUS");
    $stmt->execute();
    // Fetch all the results as an associative array
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the list of subjects as a JSON encoded string
    echo json_encode($subjects);
} catch (PDOException $e) {
    // If a database error occurs, return an error message as JSON
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
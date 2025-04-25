<?php
include 'db.php'; // Include database connection

try {
    // Query to get all unique subjects from the SUBJECT_TO_CAMPUS table
    $stmt = $pdo->prepare("SELECT DISTINCT SubjectName FROM SUBJECT_TO_CAMPUS");
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return subjects as JSON
    echo json_encode($subjects);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

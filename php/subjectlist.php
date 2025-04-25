<?php
include 'db.php'; // Database connection

// Get subject
$subject = isset($_GET['subject']) ? $_GET['subject'] : null;

if ($subject) {
    try {
        // Query to get the corresponding campus based on the subject
        $stmt = $pdo->prepare("SELECT c.Name AS campusName
                               FROM CAMPUS c
                               INNER JOIN SUBJECT_TO_CAMPUS stc ON stc.CampusID = c.CampusID
                               WHERE stc.SubjectName = :subject");

        $stmt->execute(['subject' => $subject]);
        $campus = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the campus name as a JSON response
        if ($campus) {
            echo json_encode(['campusName' => $campus['campusName']]);
        } else {
            echo json_encode(['campusName' => 'No campus found for this subject']);
        }

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Subject not specified']);
}
?>

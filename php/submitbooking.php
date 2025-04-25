<?php
session_start();
include 'db.php'; // Include your database connection

// Check if user is logged in and AccountID exists in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to book.']);
    exit();
}

// Get the AccountID from session
$accountID = $_SESSION['user_id'];

// Get the other form details
$subject = isset($_GET['subject']) ? $_GET['subject'] : null;
$entryYear = $_POST['entryYear'] ?? '';
$educationLevel = $_POST['educationLevel'] ?? '';
$contactPreference = $_POST['contactPreference'] ?? '';
$eventID = $_POST['eventID'] ?? '';

if (!$subject || !$entryYear || !$educationLevel || !$contactPreference || !$eventID) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit();
}

try {
    // Get the campus related to the subject
    $stmt = $pdo->prepare("SELECT c.Name AS campusName, c.CampusID
                           FROM CAMPUS c
                           INNER JOIN SUBJECT_TO_CAMPUS s ON s.CampusID = c.CampusID
                           WHERE s.SubjectName = :subject");

    $stmt->execute(['subject' => $subject]);
    $campus = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($campus) {
        $campusName = $campus['campusName'];
        $campusID = $campus['CampusID'];

        // Check if the event is valid
        $stmtEvent = $pdo->prepare("SELECT EventID FROM EVENT WHERE EventID = ?");
        $stmtEvent->execute([$eventID]);

        if ($stmtEvent->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid EventID selected.']);
            exit();
        }

        // Insert the booking into the database
        $stmtBooking = $pdo->prepare("
            INSERT INTO BOOKING (AccountID, YearOfEntry, LevelOfInterest, SubjectOfInterest, ContactPreference, EventID, CampusID) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtBooking->execute([$accountID, $entryYear, $educationLevel, $subject, $contactPreference, $eventID, $campusID]);

        echo json_encode(['success' => true, 'message' => 'Booking successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Campus not found for this subject.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

<?php
session_start();
include 'db.php'; // Database connection

header('Content-Type: application/json'); // Set response type to JSON

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in."
    ]);
    exit();
}

// Get the logged in user's AccountID
$accountID = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $entryYear = trim(htmlspecialchars($_POST['entryYear']));
    $educationLevel = trim(htmlspecialchars($_POST['educationLevel']));
    $subjectInterest = trim(htmlspecialchars($_POST['subjectInterest']));
    $contactPreference = trim(htmlspecialchars($_POST['contactPreference']));
    $eventID = $_POST['eventID']; // EventID from the selected date option

    // Fetch CampusID based on SubjectInterest
    $stmtCampus = $pdo->prepare("SELECT c.CampusID
                                 FROM CAMPUS c
                                 JOIN SUBJECT_TO_CAMPUS stc ON c.CampusID = stc.CampusID
                                 WHERE stc.SubjectName = :subject");
    $stmtCampus->execute(['subject' => $subjectInterest]);
    $campus = $stmtCampus->fetch(PDO::FETCH_ASSOC);

    $campusID = $campus ? $campus['CampusID'] : null; // Get CampusID or null if not found

    // Basic validation for required fields
    if (empty($entryYear) || empty($educationLevel) || empty($subjectInterest) || empty($contactPreference) || empty($eventID) || empty($campusID)) {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required."
        ]);
        exit;
    }

    // Insert booking into the database
    $stmt = $pdo->prepare("INSERT INTO BOOKING (AccountID, EventID, CampusID, LevelOfInterest, SubjectOfInterest, YearOfEntry, ContactPreference)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");

    $result = $stmt->execute([$accountID, $eventID, $campusID, $educationLevel, $subjectInterest, $entryYear, $contactPreference]); // Corrected order

    if ($result) {
        echo json_encode([
            "success" => true,
            "message" => "Booking successful!",
            "redirect" => "account.php" // Redirect to account page on success
        ]);
    } else {
         // Log the actual error for debugging, but show a generic message to the user
        error_log("Booking failed for AccountID: " . $accountID . " Error: " . $stmt->errorInfo()[2]);
        echo json_encode([
            "success" => false,
            "message" => "There was an error while submitting your booking. Please try again."
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
?>
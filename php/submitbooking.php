<?php
session_start();
include '../php/db.php'; // Ensure database connection

// Ensure user is logged in, otherwise return error
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Get data from the POST request
$entryYear = $_POST['entryYear'];
$educationLevel = $_POST['educationLevel'];
$subjectInterest = $_POST['subjectInterest'];
$contactPreference = $_POST['contactPreference'];
$bookingDate = $_POST['bookingDate']; // Ensure this is a valid Event Date
$eventID = $_POST['eventID']; // New parameter for EventID

// Validate data (check if fields are empty)
if (empty($entryYear) || empty($educationLevel) || empty($subjectInterest) || empty($contactPreference) || empty($bookingDate) || empty($eventID)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all the fields.']);
    exit();
}

// Validate that the bookingDate corresponds to a valid EventID
try {
    // Ensure the date format is correct (YYYY-MM-DD) before checking the event
    $stmt = $pdo->prepare("SELECT EventID FROM EVENT WHERE DATE(EventDate) = ? AND EventID = ?");
    $stmt->execute([$bookingDate, $eventID]);

    // Check if the event exists in the EVENT table
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid event date or EventID selected.']);
        exit();
    }

    // Insert booking into the database
    $stmt = $pdo->prepare("INSERT INTO BOOKING (AccountID, YearOfEntry, LevelOfInterest, SubjectOfInterest, ContactPreference, BookingDate, EventID) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $entryYear, $educationLevel, $subjectInterest, $contactPreference, $bookingDate, $eventID]);

    // If booking was successful
    echo json_encode(['success' => true, 'message' => 'Booking successful!']);
} catch (PDOException $e) {
    // If there was an issue inserting the booking
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

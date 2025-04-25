<?php
session_start();
include 'db.php'; // Database connection

header('Content-Type: application/json'); // Set response type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if the user exists in the database
    $stmt = $pdo->prepare("SELECT AccountID, FirstName, LastName, Email, Password FROM ACCOUNT WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($password === $user['Password']) {
            $_SESSION['user_id'] = $user['AccountID'];
            $_SESSION['username'] = $user['FirstName'] . ' ' . $user['LastName'];
            $_SESSION['email'] = $user['Email'];

            // Send a success response with redirect
            echo json_encode([
                "success" => true,
                "redirect" => "php/homepage.php"
            ]);
        } else {
            // Incorrect password error message
            echo json_encode([
                "success" => false,
                "message" => "Incorrect password!"
            ]);
        }
    } else {
        // User not found error message
        echo json_encode([
            "success" => false,
            "message" => "No user found with that email!"
        ]);
    }
}
?>

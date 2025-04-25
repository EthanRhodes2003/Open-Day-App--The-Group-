<?php
session_start();
include '../php/db.php'; // Include your database connection

header('Content-Type: application/json'); // Set response type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin from database
    $stmt = $pdo->prepare("SELECT AdminID, Name, Email, Password FROM ADMIN WHERE Email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        if ($password === $admin['Password']) {
            // Login success
            $_SESSION['admin_id'] = $admin['AdminID']; // Save admin ID in session
            $_SESSION['admin_name'] = $admin['Name'];
            $_SESSION['admin_email'] = $admin['Email'];
            
            // Return success response with redirect URL
            echo json_encode([
                "success" => true,
                "message" => "Login successful",
                "redirect" => "../php/bookingsdata.php"
            ]);
        } else {
            // Incorrect password
            echo json_encode([
                "success" => false,
                "message" => "Incorrect admin password!"
            ]);
        }
    } else {
        // No admin found with that email
        echo json_encode([
            "success" => false,
            "message" => "No admin found with that email!"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method!"
    ]);
}
?>

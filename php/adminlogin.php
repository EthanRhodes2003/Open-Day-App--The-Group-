<?php
session_start();
include '../php/db.php'; // Database connection

header('Content-Type: application/json'); // Set response type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin from database
    $stmt = $pdo->prepare("SELECT AdminID, Name, Email, Password FROM ADMIN WHERE Email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Hash the entered password 
        $hashedPasswordInput = hash('sha256', $password);

        // Compare the hashed input password with the hashed password from the database
        if ($hashedPasswordInput === $admin['Password']) {
            // Login success
            $_SESSION['admin_id'] = $admin['AdminID']; 
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
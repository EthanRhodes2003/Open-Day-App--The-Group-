<?php
session_start();
include '../php/db.php'; // Database connection

header('Content-Type: application/json'); // Set response type to JSON

// Define lockout parameters for admin login
$admin_max_login_attempts = 5; // Maximum failed attempts before lockout for admin
$admin_lockout_duration = 20 * 60; // Lockout duration in seconds (20 minutes)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize email
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim($_POST['password']); // Get raw password

    // Basic validation
    if (empty($email) || empty($password)) {
         echo json_encode([
            "success" => false,
            "message" => "Please enter both email and password."
        ]);
        exit;
    }

    // Check for existing admin lockout
    if (isset($_SESSION['admin_login_lockout_until']) && $_SESSION['admin_login_lockout_until'] > time()) {
        $remaining_time = $_SESSION['admin_login_lockout_until'] - time();
        echo json_encode([
            "success" => false,
            "message" => "Too many failed admin login attempts. Please try again in " . ceil($remaining_time / 60) . " minutes."
        ]);
        exit;
    }

    // Fetch admin from database using prepared statement
    $stmt = $pdo->prepare("SELECT AdminID, Name, Email, Password FROM ADMIN WHERE Email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Hash the entered password (keeping SHA256 as requested)
        $hashedPasswordInput = hash('sha256', $password);

        // Compare the hashed input password with the hashed password from the database
        if ($hashedPasswordInput === $admin['Password']) {
            // Login success
            $_SESSION['admin_id'] = $admin['AdminID'];
            $_SESSION['admin_name'] = $admin['Name'];
            $_SESSION['admin_email'] = $admin['Email'];

            // Reset admin login attempt counter on successful login
            unset($_SESSION['admin_login_attempts']);
            unset($_SESSION['admin_login_lockout_until']);

            // Return success response with redirect URL
            echo json_encode([
                "success" => true,
                "message" => "Admin login successful",
                "redirect" => "../php/bookingsdata.php"
            ]);
        } else {
            // Incorrect password
            // Increment failed admin login attempts
            $_SESSION['admin_login_attempts'] = ($_SESSION['admin_login_attempts'] ?? 0) + 1;

            if ($_SESSION['admin_login_attempts'] >= $admin_max_login_attempts) {
                // Lockout the admin account
                $_SESSION['admin_login_lockout_until'] = time() + $admin_lockout_duration;
                echo json_encode([
                    "success" => false,
                    "message" => "Too many failed admin login attempts. Admin account locked for 20 minutes."
                ]);
            } else {
                // Incorrect password error message 
                echo json_encode([
                    "success" => false,
                    "message" => "Incorrect admin password!"
                ]);
            }
        }
    } else {
        // No admin found with that email
        // Increment failed admin login attempts 
        $_SESSION['admin_login_attempts'] = ($_SESSION['admin_login_attempts'] ?? 0) + 1;

         if ($_SESSION['admin_login_attempts'] >= $admin_max_login_attempts) {
                $_SESSION['admin_login_lockout_until'] = time() + $admin_lockout_duration;
                 echo json_encode([
                    "success" => false,
                    "message" => "No admin found with that email or incorrect credentials. Too many failed attempts, admin account locked for 20 minutes."
                ]);
         } else {
            // No admin found error message 
            echo json_encode([
                "success" => false,
                "message" => "No admin found with that email or incorrect credentials."
            ]);
         }
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
?>
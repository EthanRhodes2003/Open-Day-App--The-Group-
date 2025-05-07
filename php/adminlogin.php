<?php
// Start the session to manage admin login state
session_start();
// Include the database connection file
include '../php/db.php'; // Database connection

// Set the response header to indicate JSON content
header('Content-Type: application/json'); // Set response type to JSON

// Define parameters for admin login lockout
$admin_max_login_attempts = 5; // Maximum failed attempts before lockout for admin
$admin_lockout_duration = 20 * 60; // Lockout duration in seconds (20 minutes)

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email and password from the form data, and sanitize email
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim($_POST['password']); // Get raw password

    // Basic validation to check if fields are empty
    if (empty($email) || empty($password)) {
         echo json_encode([
            "success" => false,
            "message" => "Please enter both email and password."
        ]);
        exit;
    }

    // Check if there is an existing admin lockout for this session
    if (isset($_SESSION['admin_login_lockout_until']) && $_SESSION['admin_login_lockout_until'] > time()) {
        // Calculate remaining lockout time
        $remaining_time = $_SESSION['admin_login_lockout_until'] - time();
        // Output error message about lockout duration
        echo json_encode([
            "success" => false,
            "message" => "Too many failed admin login attempts. Please try again in " . ceil($remaining_time / 60) . " minutes."
        ]);
        exit;
    }

    // Prepare and execute a statement to fetch admin data by email
    $stmt = $pdo->prepare("SELECT AdminID, Name, Email, Password FROM ADMIN WHERE Email = ?");
    $stmt->execute([$email]);
    // Fetch the admin data
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if an admin with the provided email was found
    if ($admin) {
        // Hash the entered password using SHA256 (as requested)
        $hashedPasswordInput = hash('sha256', $password);

        // Compare the hashed input password with the hashed password stored in the database
        if ($hashedPasswordInput === $admin['Password']) {
            // Admin login successful

            // Store admin information in the session
            $_SESSION['admin_id'] = $admin['AdminID'];
            $_SESSION['admin_name'] = $admin['Name'];
            $_SESSION['admin_email'] = $admin['Email'];

            // Reset admin login attempt counter on successful login
            unset($_SESSION['admin_login_attempts']);
            unset($_SESSION['admin_login_lockout_until']);

            // Output success response with a redirect URL to the bookings data page
            echo json_encode([
                "success" => true,
                "message" => "Admin login successful",
                "redirect" => "../php/bookingsdata.php"
            ]);
        } else {
            // Incorrect password

            // Increment the failed admin login attempt counter
            $_SESSION['admin_login_attempts'] = ($_SESSION['admin_login_attempts'] ?? 0) + 1;

            // Check if the maximum number of failed attempts has been reached
            if ($_SESSION['admin_login_attempts'] >= $admin_max_login_attempts) {
                // Lockout the admin account by setting the lockout time
                $_SESSION['admin_login_lockout_until'] = time() + $admin_lockout_duration;
                // Output error message about lockout
                echo json_encode([
                    "success" => false,
                    "message" => "Too many failed admin login attempts. Admin account locked for 20 minutes."
                ]);
            } else {
                // Output incorrect password error message
                echo json_encode([
                    "success" => false,
                    "message" => "Incorrect admin password!"
                ]);
            }
        }
    } else {
        // No admin found with that email

        // Increment the failed admin login attempt counter
        $_SESSION['admin_login_attempts'] = ($_SESSION['admin_login_attempts'] ?? 0) + 1;

         // Check if the maximum number of failed attempts has been reached
         if ($_SESSION['admin_login_attempts'] >= $admin_max_login_attempts) {
                // Lockout the admin account
                $_SESSION['admin_login_lockout_until'] = time() + $admin_lockout_duration;
                 // Output error message about no admin found and lockout
                 echo json_encode([
                    "success" => false,
                    "message" => "No admin found with that email or incorrect credentials. Too many failed attempts, admin account locked for 20 minutes."
                ]);
         } else {
            // Output error message about no admin found
            echo json_encode([
                "success" => false,
                "message" => "No admin found with that email or incorrect credentials."
            ]);
         }
    }
} else {
    // Handle cases where the request method is not POST
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
?>
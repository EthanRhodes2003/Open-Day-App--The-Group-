<?php
session_start();
include 'db.php'; // Database connection

header('Content-Type: application/json'); // Set response type to JSON

// Lockout parameters
$max_login_attempts = 5; // Maximum failed attempts before lockout
$lockout_duration = 20 * 60; // Lockout duration in seconds (20 minutes)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize email
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim($_POST['password']); // Get raw password for verification

    // Basic validation
    if (empty($email) || empty($password)) {
         echo json_encode([
            "success" => false,
            "message" => "Please enter both email and password."
        ]);
        exit;
    }

    // Check for existing lockout
    if (isset($_SESSION['login_lockout_until']) && $_SESSION['login_lockout_until'] > time()) {
        $remaining_time = $_SESSION['login_lockout_until'] - time();
        echo json_encode([
            "success" => false,
            "message" => "Too many failed login attempts. Please try again in " . ceil($remaining_time / 60) . " minutes."
        ]);
        exit;
    }

    // Check if the user exists in the database using prepared statement
    $stmt = $pdo->prepare("SELECT AccountID, FirstName, LastName, Email, Password FROM ACCOUNT WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify the hashed password
        if (password_verify($password, $user['Password'])) {
            // Login success

            // Regenerate session ID after successful login
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['AccountID'];
            $_SESSION['username'] = $user['FirstName'] . ' ' . $user['LastName'];
            $_SESSION['email'] = $user['Email'];

            // Reset login attempt counter on successful login
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_lockout_until']);

            // Send a success response with redirect 
            echo json_encode([
                "success" => true,
                "redirect" => "php/homepage.php" // Redirect to homepage
            ]);
        } else {
            // Incorrect password
            // Increment failed login attempts
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

            if ($_SESSION['login_attempts'] >= $max_login_attempts) {
                // Lockout the account
                $_SESSION['login_lockout_until'] = time() + $lockout_duration;
                echo json_encode([
                    "success" => false,
                    "message" => "Too many failed login attempts. Account locked for 20 minutes."
                ]);
            } else {
                // Incorrect password error message 
                echo json_encode([
                    "success" => false,
                    "message" => "Incorrect password!"
                ]);
            }
        }
    } else {
        // User not found error message
        // Increment failed login attempts 
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

         if ($_SESSION['login_attempts'] >= $max_login_attempts) {
                $_SESSION['login_lockout_until'] = time() + $lockout_duration;
                 echo json_encode([
                    "success" => false,
                    "message" => "No user found with that email or incorrect credentials. Too many failed attempts, account locked for 20 minutes."
                ]);
         } else {
            // User not found error message 
            echo json_encode([
                "success" => false,
                "message" => "No user found with that email or incorrect credentials."
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
<?php
session_start();
include 'db.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Query to check if the email exists in the account table
    $stmt = $pdo->prepare("SELECT * FROM account WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify the password using password_verify()
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['email'] = $user['email'];

            // Redirect to homepage or another protected page
            header("Location: homepage.html"); // Change this to your protected page URL
            exit();
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "No user found with that email!";
    }
}
?>

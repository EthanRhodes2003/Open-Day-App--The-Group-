<?php
session_start();
include 'db.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug to check if form data is received
    echo "<pre>";
    print_r($_POST);  
    echo "</pre>";

    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        die("Error: Email or Password field is missing.");
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Corrected SQL query (matching table column names exactly)
    $stmt = $pdo->prepare("SELECT AccountID, FirstName, LastName, Email, Password FROM ACCOUNT WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Directly compare the plain password with the stored one
        if ($password === $user['Password']) {  
            $_SESSION['user_id'] = $user['AccountID'];
            $_SESSION['username'] = $user['FirstName'] . ' ' . $user['LastName'];
            $_SESSION['email'] = $user['Email'];

            // Redirect to homepage or another protected page
            header("Location: ../php/homepage.php"); 
            exit();
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "No user found with that email!";
    }
} else {
    echo "Invalid request!";
}
?>
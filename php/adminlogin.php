<?php
session_start();
include 'db.php'; // Connect to DB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        die("Error: Email or Password field is missing.");
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin from database
    $stmt = $pdo->prepare("SELECT AdminID, Name, Email, Password FROM ADMIN WHERE Email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        if ($password === $admin['Password']) {
            // Login success
            $_SESSION['admin_id'] = $admin['AdminID'];
            $_SESSION['admin_name'] = $admin['Name'];
            $_SESSION['admin_email'] = $admin['Email'];

            header("Location: ../php/bookingsdata.php");
            exit();
        } else {
            echo "Incorrect admin password!";
        }
    } else {
        echo "No admin found with that email!";
    }
} else {
    echo "Invalid request!";
}
?>

<?php
session_start();
include 'db.php'; // Connect to DB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug output 
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        die("Error: Email or Password field is missing.");
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin by email
    $stmt = $pdo->prepare("SELECT AdminID, Name, Email, Password FROM ADMIN WHERE Email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Plaintext password comparison (for now)
        if ($password === $admin['Password']) {
            // Store admin session
            $_SESSION['admin_id'] = $admin['AdminID'];
            $_SESSION['admin_name'] = $admin['Name'];
            $_SESSION['admin_email'] = $admin['Email'];

            // Redirect to admin dashboard
            header("Location: /bookingsdata.html");
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

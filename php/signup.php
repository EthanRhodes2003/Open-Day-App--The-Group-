<?php
session_start();
include 'db.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting form data
    $first_name = trim($_POST['first_name']);  // Change to match the database column
    $last_name = trim($_POST['last_name']);    // Change to match the database column
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password']; // Directly use the password as entered
    $dob = $_POST['dob'];
    $country = $_POST['country'];

    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT * FROM account WHERE Email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        // If email already exists
        echo "Email already registered!";
    } else {
        // Insert new user into the account table without providing AccountID, as it auto increments
        $stmt = $pdo->prepare("INSERT INTO account (FirstName, LastName, Email, Phone, Password, Birthday, Country)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$first_name, $last_name, $email, $phone, $password, $dob, $country]);

        if ($result) {
            // Redirect to login page or set a success message
            echo "Signup successful! You can now log in.";
            // Optionally, automatically log the user in after signup
            $_SESSION['user_id'] = $pdo->lastInsertId(); // Get the ID of the newly inserted user
            $_SESSION['username'] = $first_name . ' ' . $last_name;
            header("Location: /homepage.html"); // Redirect to homepage after successful signup
        } else {
            echo "There was an error while signing up. Please try again.";
        }
    }
}
?>

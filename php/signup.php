<?php
session_start();
include 'db.php'; // Database connection

header('Content-Type: application/json'); // Set response type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data with matching camel case
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $country = $_POST['country'];

    // Check if the phone number is only numbers and max 11 digits
    if (!preg_match("/^\d{1,11}$/", $phone)) {
        echo json_encode([
            "success" => false,
            "message" => "Phone number must contain only numbers and be no more than 11 digits."
        ]);
        exit; // Stop execution if phone number is invalid
    }

    // Calculate age from the date of birth and check if user is at least 13 years old
    $birthDate = new DateTime($dob);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;

    if ($age < 13) {
        echo json_encode([
            "success" => false,
            "message" => "You must be at least 13 years old to create an account."
        ]);
        exit; // Stop execution if user is younger than 13
    }

    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT * FROM account WHERE Email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Email already registered!"
        ]);
    } else {
        // Insert new user into the account table
        $stmt = $pdo->prepare("INSERT INTO account (FirstName, LastName, Email, Phone, Password, Birthday, Country)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$firstName, $lastName, $email, $phone, $password, $dob, $country]);

        if ($result) {
            // Fetch the newly created user to log them in
            $stmt = $pdo->prepare("SELECT AccountID, FirstName, LastName, Email FROM ACCOUNT WHERE Email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user_id'] = $user['AccountID'];
                $_SESSION['username'] = $user['FirstName'] . ' ' . $user['LastName'];
                $_SESSION['email'] = $user['Email'];

                // Return successful response with a redirect for homepage
                echo json_encode([
                    "success" => true,
                    "redirect" => "php/homepage.php"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "There was an error while signing up. Please try again."
            ]);
        }
    }
}
?>

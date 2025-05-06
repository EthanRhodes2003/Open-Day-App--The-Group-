<?php
session_start();
include 'db.php'; // Database connection

header('Content-Type: application/json'); // Set response type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data with matching camel case and sanitize
    $firstName = trim(htmlspecialchars($_POST['firstName']));
    $lastName = trim(htmlspecialchars($_POST['lastName']));
    $email = trim(htmlspecialchars($_POST['email']));
    $phone = trim(htmlspecialchars($_POST['phone']));
    $password = $_POST['password']; // Get raw password for hashing and validation
    $dob = $_POST['dob'];
    $country = trim(htmlspecialchars($_POST['country']));

    // Basic validation for required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password) || empty($dob) || empty($country)) {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required."
        ]);
        exit;
    }

    // Server-side Password Policy Validation
    $minLength = 8;
    $hasUppercase = preg_match('/[A-Z]/', $password);
    $hasLowercase = preg_match('/[a-z]/', $password);
    $hasNumber = preg_match('/[0-9]/', $password);
    $hasSpecial = preg_match('/[!@#$%^&*]/', $password); // Match allowed special characters

    if (strlen($password) < $minLength || !$hasUppercase || !$hasLowercase || !$hasNumber || !$hasSpecial) {
         echo json_encode([
            "success" => false,
            "message" => "Password does not meet the required policy (Min 8 characters, uppercase, lowercase, number, special character)."
        ]);
        exit;
    }


    // Check if the phone number is only numbers and max 11 digits
    if (!preg_match("/^\d{1,11}$/", $phone)) {
        echo json_encode([
            "success" => false,
            "message" => "Phone number must contain only numbers and be no more than 11 digits."
        ]);
        exit; // Stop execution if phone number is invalid
    }

    // Calculate age from the date of birth and check if user is at least 13 years old
    try {
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
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid date of birth format."
        ]);
        exit;
    }


    // Check if the email already exists using prepared statement
    $stmt = $pdo->prepare("SELECT * FROM account WHERE Email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Email already registered!"
        ]);
    } else {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the account table using prepared statement
        $stmt = $pdo->prepare("INSERT INTO account (FirstName, LastName, Email, Phone, Password, Birthday, Country)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Use the hashed password in the execute statement
        $result = $stmt->execute([$firstName, $lastName, $email, $phone, $hashedPassword, $dob, $country]);

        if ($result) {
            // Fetch the newly created user to log them in
            $stmt = $pdo->prepare("SELECT AccountID, FirstName, LastName, Email FROM ACCOUNT WHERE Email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                // Regenerate session ID after successful signup and immediate login
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['AccountID'];
                $_SESSION['username'] = $user['FirstName'] . ' ' . $user['LastName'];
                $_SESSION['email'] = $user['Email'];

                // Reset login attempt counter on successful signup and immediate login
                unset($_SESSION['login_attempts']);
                unset($_SESSION['login_lockout_until']);

                // Return successful response with a redirect for homepage
                echo json_encode([
                    "success" => true,
                    "redirect" => "php/homepage.php"
                ]);
            } else {
                 // This case should ideally not happen if insertion was successful
                 echo json_encode([
                    "success" => false,
                    "message" => "Account created, but could not log in automatically. Please try logging in."
                ]);
            }
        } else {
            // Log the actual error for debugging, but show a generic message to the user
            error_log("Signup failed for email: " . $email . " Error: " . $stmt->errorInfo()[2]);
            echo json_encode([
                "success" => false,
                "message" => "There was an error while signing up. Please try again."
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
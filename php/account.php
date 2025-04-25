<?php
session_start();
include '../php/db.php';

// Ensure user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT * FROM ACCOUNT WHERE AccountID = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch booking data, including campus name and event date
$stmtBookings = $pdo->prepare("SELECT b.*, c.Name AS CampusName, e.EventDate 
                               FROM BOOKING b
                               JOIN CAMPUS c ON b.CampusID = c.CampusID
                               JOIN EVENT e ON b.EventID = e.EventID
                               WHERE b.AccountID = ?");
$stmtBookings->execute([$userId]);
$bookings = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account - Wolvo Open Day</title>
  <link rel="stylesheet" href="../css/account.css"> <!-- Link to custom CSS file -->
</head>
<body>

<!-- Mobile Frame -->
<div class="mobileFrame">
  
  <!-- Title Bar -->
  <div class="titleBar">
    <div class="logo">Wolvo Open Day</div>
    <div class="logoutButtonContainer">
      <form method="POST" action="logout.php">
        <button type="submit" class="logoutButton">Logout</button>
      </form>
    </div>
  </div>

  <!-- Main Content -->
  <div class="mobileContent">

    <!-- Account Details Section -->
    <section id="accountDetails" class="accountSection">
      <div class="sectionHeader">
        <h2>My Account</h2>
      </div>

      <div class="userProfile">
        <div class="profileHeader">
          <div class="profilePicture">
            <span>👤</span>
          </div>
          <div class="profileInfo">
            <h3><?php echo htmlspecialchars($user['FirstName']) . ' ' . htmlspecialchars($user['LastName']); ?></h3>
            <p>Student ID: <?php echo htmlspecialchars($user['AccountID']); ?></p>
          </div>
        </div>

        <div class="dataSection">
          <h4>Personal Information</h4>
          <div class="dataItem">
            <span class="itemLabel">Email:</span>
            <span class="itemValue"><?php echo htmlspecialchars($user['Email']); ?></span>
          </div>
          <div class="dataItem">
            <span class="itemLabel">Phone:</span>
            <span class="itemValue"><?php echo htmlspecialchars($user['Phone']); ?></span>
          </div>
          <div class="dataItem">
            <span class="itemLabel">Country:</span>
            <span class="itemValue"><?php echo htmlspecialchars($user['Country']); ?></span>
          </div>
          <div class="dataItem">
            <span class="itemLabel">Birthday:</span>
            <span class="itemValue"><?php echo date('d/m/Y', strtotime($user['Birthday'])); ?></span>
          </div>
        </div>

        <div class="dataSection">
          <h4>Booking Information</h4>
          <?php if (count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
              <div class="bookingCard">
                <h5>Booking ID: <?php echo htmlspecialchars($booking['BookingID']); ?></h5>
                <div class="dataItem">
                  <span class="itemLabel">Year of Entry:</span>
                  <span class="itemValue"><?php echo htmlspecialchars($booking['YearOfEntry']); ?></span>
                </div>
                <div class="dataItem">
                  <span class="itemLabel">Education Level:</span>
                  <span class="itemValue"><?php echo htmlspecialchars($booking['LevelOfInterest']); ?></span>
                </div>
                <div class="dataItem">
                  <span class="itemLabel">Subject Interest:</span>
                  <span class="itemValue"><?php echo htmlspecialchars($booking['SubjectOfInterest']); ?></span>
                </div>
                <div class="dataItem">
                  <span class="itemLabel">Contact Preference:</span>
                  <span class="itemValue"><?php echo htmlspecialchars($booking['ContactPreference']); ?></span>
                </div>
                <div class="dataItem">
                  <span class="itemLabel">Campus:</span>
                  <span class="itemValue"><?php echo htmlspecialchars($booking['CampusName']); ?></span>
                </div>
                <div class="dataItem">
                  <span class="itemLabel">Event Date:</span>
                  <span class="itemValue"><?php echo date('d/m/Y', strtotime($booking['EventDate'])); ?></span>
                </div>
              </div>
              <hr class="bookingSeparator" />
            <?php endforeach; ?>
          <?php else: ?>
            <p>No booking information available.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="btnContainer">
        <a href="../php/homepage.php" class="btn">Back to Home</a>
      </div>
    </section>

  </div>

</div>

<!-- Inactivity Timer Script -->
<script>
let inactivityTimeout;

// Function to reset the inactivity timer
function resetInactivityTimer() {
  clearTimeout(inactivityTimeout);
  inactivityTimeout = setTimeout(() => {
    alert("You have been logged out due to inactivity."); // Show alert
    window.location.href = "logout.php"; // Redirect to logout page
  }, 5 * 60 * 1000); // Set timeout for 5 minutes
}

// Detect user interaction and reset the inactivity timer
['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
  document.addEventListener(evt, resetInactivityTimer, false);
});

// Start the inactivity timer
resetInactivityTimer(); 
</script>

</body>  
</html>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wolvo Open Day</title>
  <link rel="stylesheet" href="../css/homepage.css">
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

    <section id="hero">
      <div class="heroContent">
        <h1>Discover Your Future at Wolvo</h1>
        <p>Experience campus life, meet our experts, and start your journey with us.</p>
        <a href="#register" class="button">Book NOW</a>
      </div>
    </section>

    <section id="events">
      <div class="sectionHeader">
        <h2>Upcoming Open Days</h2>
      </div>
      <ul class="eventList">
        <li>March 26, 2025</li>
        <li>April 2, 2025</li>
        <li>April 9, 2025</li>
      </ul>
    </section>

    <section id="register">
      <div class="sectionHeader">
        <h2>Book Now</h2>
      </div>
      <a href="../php/booking.php" class="button">Booking</a>
    </section>

    <section id="account">
      <div class="sectionHeader">
        <h2>Your Account</h2>
      </div>
      <a href="../php/account.php" class="button accountButton">My Account</a>
    </section>

    <section id="contact">
      <div class="sectionHeader">
        <h2>Contact Us</h2>
      </div>
      <p>Email: <strong>openday@wlv.ac.uk</strong></p>
      <p>Phone: <strong>01902 123456</strong></p>
    </section>

  </div>
</div>

<script>
let inactivityTimeout;

function resetInactivityTimer() {
  clearTimeout(inactivityTimeout);
  inactivityTimeout = setTimeout(() => {
    alert("You have been logged out due to inactivity.");
    window.location.href = "logout.php";
  }, 5 * 60 * 1000); // 5 minutes
}

['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
  document.addEventListener(evt, resetInactivityTimer, false);
});

resetInactivityTimer(); 
</script>

<script src="../js/homepage.js"></script>
</body>
</html>

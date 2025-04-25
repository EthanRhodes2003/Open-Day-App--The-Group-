<?php
session_start();
// Check if the user is logged in, if not, redirect to the login page
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
  <link rel="stylesheet" href="../css/homepage.css"> <!-- Link to custom CSS -->
</head>
<body> 

<!-- Mobile Frame -->
<div class="mobileFrame">

  <!-- Title Bar with Logo and Logout Button -->
  <div class="titleBar">
    <div class="logo">Wolvo Open Day</div>
    <!-- Logout Button Form -->
    <div class="logoutButtonContainer">
      <form method="POST" action="logout.php">
        <button type="submit" class="logoutButton">Logout</button>
      </form>
    </div>
  </div>

  <!-- Main Content -->
  <div class="mobileContent">

    <!-- Hero Section with Title and Book Now Button -->
    <section id="hero">
      <div class="heroContent">
        <h1>Discover Your Future at Wolvo</h1> <!-- Main title -->
        <p>Experience campus life, meet our experts, and start your journey with us.</p> <!-- Subtitle -->
        <a href="#register" class="button">Book NOW</a> <!-- Button linking to registration -->
      </div>
    </section>

    <!-- Upcoming Events Section -->
    <section id="events">
      <div class="sectionHeader">
        <h2>Upcoming Open Days</h2>
      </div>
      <!-- List of upcoming events -->
      <ul class="eventList">
        <li>March 26, 2025</li>
        <li>April 2, 2025</li>
        <li>April 9, 2025</li>
      </ul>
    </section>

    <!-- Registration Section with Booking Button -->
    <section id="register">
      <div class="sectionHeader">
        <h2>Book Now</h2>
      </div>
      <!-- Button to navigate to booking page -->
      <a href="../php/booking.php" class="button">Booking</a>
    </section>

    <!-- Account Section with Link to User Account -->
    <section id="account">
      <div class="sectionHeader">
        <h2>Your Account</h2>
      </div>
      <!-- Button to navigate to the user's account page -->
      <a href="../php/account.php" class="button accountButton">My Account</a>
    </section>

    <!-- Contact Us Section with Email and Phone -->
    <section id="contact">
      <div class="sectionHeader">
        <h2>Contact Us</h2>
      </div>
      <p>Email: <strong>openday@wlv.ac.uk</strong></p>
      <p>Phone: <strong>01902 123456</strong></p>
    </section>

  </div>
</div>

<script src="../js/homepage.js"></script> <!-- Link to custom JS file -->
</body>
</html>

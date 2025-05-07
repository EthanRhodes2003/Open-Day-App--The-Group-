<?php
// Start the session to manage user login state
session_start();
// Check if the user is logged in, if not, redirect to the index (login) page
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
  <link rel="stylesheet" href="../css/homepage.css"> </head>
<body>

<div class="mobileFrame">

  <div class="titleBar">
    <div class="logo">Wolvo Open Day</div>
    <div class="logoutButtonContainer">
      <form method="POST" action="logout.php">
        <button type="submit" class="logoutButton">Logout</button>
      </form>
    </div>
  </div>

  <div class="mobileContent">

    <section id="hero">
      <div class="heroContent">
        <h1>Discover Your Future at Wolvo</h1> <p>Experience campus life, meet our experts, and start your journey with us.</p> <a href="#register" class="button">📖 Book NOW</a> </div>
    </section>

    <section id="upcomingEvents">
      <div class="sectionHeader">
        <h2>📅 Upcoming Open Days</h2>
      </div>
      <ul class="eventList">
        <li>March 26, 2025</li>
        <li>April 2, 2025</li>
        <li>April 9, 2025</li>
      </ul>
    </section>

    <section id="eventInfo">
      <div class="sectionHeader">
        <h2>Event Schedule</h2>
      </div>
      <div class="eventSchedule">
        <div class="eventItem">
          <h3>
            🗣️ University Talk
          </h3>
          <p>Lecture Hall | 10:00 AM - 11:00 AM</p>
        </div>
        <div class="eventItem">
          <h3>
            🚶 Guided Campus Tour
          </h3>
          <p>Meet at Main Reception | 11:00 AM - 12:00 PM</p>
        </div>
        <div class="eventItem">
          <h3>
            📚 Chosen Subject Tour
          </h3>
          <p>Check subject department for location | 12:00 PM - 4:00 PM</p>
        </div>
      </div>
       </section>

    <section id="register">
      <div class="sectionHeader">
        <h2>Book Now</h2>
      </div>
      <a href="../php/booking.php" class="button">Booking</a>
    </section>

    <section id="faq">
      <div class="sectionHeader">
        <h2>❓ Frequently Asked Questions</h2>
      </div>
      <div class="faqItems">
          <div class="faqItem">
            <h3>How do we change our password?</h3>
            <p>You can change your password by contacting our support team via email or phone. Please see the Contact Us section for details.</p>
          </div>
          <div class="faqItem">
            <h3>Where can I find parking on campus during the Open Day?</h3>
            <p>Visitor parking is available in designated car parks across campus. Detailed information and a map is available online on our website.</p>
          </div>
          <div class="faqItem">
            <h3>Are food and drink options available?</h3>
            <p>Yes, our campus cafes, catering and shops will be open during the Open Day, offering a variety of food and drink options for purchase.</p>
          </div>
          </div>
    </section>

    <section id="account">
      <div class="sectionHeader">
        <h2>Your Account</h2>
      </div>
      <a href="../php/account.php" class="button accountButton">My Account</a>
    </section>

    <section id="contact">
      <div class="sectionHeader">
        <h2>📞 Contact Us</h2>
      </div>
      <p>Email: <strong>openday@wlv.ac.uk</strong></p>
      <p>Phone: <strong>01902 123456</strong></p>
    </section>

  </div>
</div>

<script src="../js/homepage.js"></script> </body>
</html>
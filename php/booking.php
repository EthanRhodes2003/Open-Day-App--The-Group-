<?php
// Start the session to manage user login state
session_start();
// Include the database connection file
include '../php/db.php';

// Check if the user is logged in, if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the logged-in user's account ID from the session
$accountID = $_SESSION['user_id'];

// Variables to hold messages (these will be set by booking.js's fetch request)
$successMessage = '';
$errorMessage = '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Open Day Signup</title>
  <link rel="stylesheet" href="../css/booking.css">
</head>
<body>

<div class="mobileFrame">
  <div class="titleBar">
    <a href="homepage.php" class="backButton">&larr;</a>
    <div class="logo">Open Day Signup</div>
    <div class="logoutButtonContainer">
      <form method="POST" action="logout.php">
        <button type="submit" class="logoutButton">Logout</button>
      </form>
    </div>
  </div>

  <div class="mobileContent">
    <div class="formContainer">
      <h1>Register for Open Day</h1>

      <form id="signupForm" method="POST">
        <div class="formElement">
          <label for="entryYear" class="formLabel">Year of Entry</label>
          <select id="entryYear" name="entryYear" required>
            <option value="" disabled selected>Select year</option>
            <option value="September 2025">September 2025</option>
            <option value="January 2026">January 2026</option>
            <option value="September 2026">September 2026</option>
            <option value="January 2027">January 2027</option>
            <option value="September 2027">September 2027</option>
            <option value="January 2028">January 2028</option>
            <option value="September 2028">September 2028</option>
          </select>
        </div>

        <div class="formElement">
          <label for="educationLevel" class="formLabel">Education Level</label>
          <select id="educationLevel" name="educationLevel" required>
            <option value="" disabled selected>Select level</option>
            <option value="Level 4">Level 4 (Certificate)</option>
            <option value="Level 5">Level 5 (Diploma/Foundation)</option>
            <option value="Level 6">Level 6 (Bachelor's Degree)</option>
            <option value="Level 7">Level 7 (Master's Degree)</option>
            <option value="Level 8">Level 8 (Doctorate)</option>
          </select>
        </div>

        <div class="formElement">
          <label for="subjectInterest" class="formLabel">Subject Interest</label>
          <select id="subjectInterest" name="subjectInterest" required>
            <option value="" disabled selected>Select subject</option>
          </select>
        </div>

        <div class="formElement">
          <label for="campus" class="formLabel">Campus</label>
          <input type="text" id="campus" name="campus" readonly placeholder="Campus will appear here">
        </div>

        <div class="formElement">
          <label for="contactPreference" class="formLabel">Contact Preference</label>
          <select id="contactPreference" name="contactPreference" required>
            <option value="" disabled selected>Select preference</option>
            <option value="Email">Email</option>
            <option value="Phone">Phone</option>
            <option value="SMS">SMS</option>
          </select>
        </div>

        <div class="formElement">
          <label for="bookingDate" class="formLabel">Open Day Date</label>
          <select id="bookingDate" name="bookingDate" required>
            <option value="" disabled selected>Select date</option>
          </select>
        </div>

        <button type="submit" class="btn">Register</button>
      </form>

      <div id="message">
        <?php
          // Display success or error message if set (these would likely be set by the JS fetch now)
          if (!empty($successMessage)) {
              echo "<p class='success'>$successMessage</p>";
          } elseif (!empty($errorMessage)) {
              echo "<p class='error'>$errorMessage</p>";
          }
        ?>
      </div>
    </div>
  </div>
</div>

<script>
  // Function to load available event dates into the dropdown
  async function loadEventDates() {
  try {
    // Fetch event dates from the server
    const response = await fetch('../php/eventdates.php');
    const events = await response.json();
    const bookingDateSelect = document.getElementById('bookingDate');
    // Clear previous options and add a default option
    bookingDateSelect.innerHTML = '<option value="" disabled selected>Select date</option>';

    const seen = new Set();
    // Iterate through the fetched events
    events.forEach(event => {
      const rawDate = new Date(event.EventDate);
      const dateStr = rawDate.toISOString().split('T')[0];
      // Add unique dates to the dropdown
      if (!seen.has(dateStr)) {
        seen.add(dateStr);
        const option = document.createElement('option');
        option.value = dateStr;

        // Format the date as DD/MM/YYYY for display
        option.textContent = `${String(rawDate.getDate()).padStart(2, '0')}/${
          String(rawDate.getMonth() + 1).padStart(2, '0')}/${rawDate.getFullYear()}`;

        bookingDateSelect.appendChild(option);
      }
    });
  } catch (err) {
    // Log errors if fetching event dates fails
    console.error('Error loading event dates:', err);
  }
}

  // Function to load available subjects into the dropdown
  async function loadSubjects() {
    try {
      // Fetch subjects from the server
      const response = await fetch('../php/getsubjects.php'); // Corrected path
      const subjects = await response.json();
      const subjectSelect = document.getElementById('subjectInterest');
      // Clear previous options and add a default option
      subjectSelect.innerHTML = '<option value="" disabled selected>Select subject</option>';
      // Add each fetched subject to the dropdown
      subjects.forEach(subject => {
        const option = document.createElement('option');
        option.value = subject.SubjectName;
        option.textContent = subject.SubjectName;
        subjectSelect.appendChild(option);
      });
    } catch (error) {
      // Log errors if fetching subjects fails
      console.error('Error fetching subjects:', error);
    }
  }

  // Add an event listener to the subject dropdown to update the campus field
  document.getElementById('subjectInterest').addEventListener('change', async (e) => {
    const selectedSubject = e.target.value;
    try {
      // Fetch the campus name based on the selected subject
      const response = await fetch(`../php/subjectlist.php?subject=${encodeURIComponent(selectedSubject)}`); // Corrected path
      const data = await response.json();
      // Update the campus input field with the fetched campus name
      document.getElementById('campus').value = data.campusName || 'No campus found for this subject';
    } catch (err) {
      // Log errors and update the campus field if fetching campus fails
      console.error('Error fetching campus:', err);
      document.getElementById('campus').value = 'Error fetching campus';
    }
  });

  // Handle form submission using fetch
  document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevent page reload on form submission

    // Collect the form data
    const formData = {
      entryYear: document.getElementById('entryYear').value,
      educationLevel: document.getElementById('educationLevel').value,
      subjectInterest: document.getElementById('subjectInterest').value,
      contactPreference: document.getElementById('contactPreference').value,
      bookingDate: document.getElementById('bookingDate').value,
      eventID: document.getElementById('bookingDate').selectedOptions[0].value // This assumes the option value is EventID as per loadEventDates function
    };

    // Validate all fields are filled
    for (const key in formData) {
      if (!formData[key]) {
        document.getElementById('message').textContent = 'Please fill in all the required fields.';
        document.getElementById('message').className = 'message error'; // Added 'message' class
        return;
      }
    }

    try {
      // Send the form data to the PHP script for processing
      const response = await fetch('../php/submitbooking.php', { // Path confirmed from booking.js
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(formData)
      });

      const data = await response.json();

      console.log(data); // Log the response for debugging

      const messageDiv = document.getElementById('message');

      // Check if the booking was successfully processed
      if (data.success) {
        messageDiv.textContent = 'Booking successful!';
        messageDiv.className = 'message success'; // Added 'message' class
        // Redirect to account page after successful booking with a slight delay
        setTimeout(() => {
             window.location.href = '../php/account.php';
        }, 1000); // 1 second delay
      } else {
        messageDiv.textContent = data.message || 'Booking failed. Please try again.';
        messageDiv.className = 'message error'; // Added 'message' class
      }
    } catch (error) {
      console.error('Error during form submission:', error);
      const messageDiv = document.getElementById('message');
      messageDiv.textContent = 'An error occurred. Please try again.';
      messageDiv.className = 'message error'; // Added 'message' class
    }
  });


  // Load event dates and subjects when the window finishes loading
  window.onload = () => {
    loadEventDates();
    loadSubjects();
  };
</script>

</body>
</html>
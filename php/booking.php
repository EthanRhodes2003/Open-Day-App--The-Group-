<?php
session_start();
include '../php/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $entryYear = $_POST['entryYear'] ?? '';
    $educationLevel = $_POST['educationLevel'] ?? '';
    $subjectInterest = $_POST['subjectInterest'] ?? '';
    $contactPreference = $_POST['contactPreference'] ?? '';
    $eventDate = $_POST['bookingDate'] ?? '';

    $accountID = $_SESSION['user_id']; // Get the current user's account ID from the session

    // Validate the data
    if (empty($entryYear) || empty($educationLevel) || empty($subjectInterest) || empty($contactPreference) || empty($eventDate)) {
        $errorMessage = 'All fields are required.';
    } else {
        try {
            // Fetch the associated campus based on the subject selected
            $stmt = $pdo->prepare("SELECT c.CampusID, c.Name AS campusName 
                                   FROM CAMPUS c
                                   INNER JOIN SUBJECT_TO_CAMPUS stc ON c.CampusID = stc.CampusID
                                   WHERE stc.SubjectName = ?");
            $stmt->execute([$subjectInterest]);
            $campus = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($campus) {
                // Insert the booking into the database
                $stmtBooking = $pdo->prepare("INSERT INTO BOOKING (AccountID, YearOfEntry, LevelOfInterest, SubjectOfInterest, ContactPreference, EventID, CampusID)
                                              VALUES (?, ?, ?, ?, ?, (SELECT EventID FROM EVENT WHERE EventDate = ? LIMIT 1), ?)");
                $stmtBooking->execute([
                    $accountID, 
                    $entryYear, 
                    $educationLevel, 
                    $subjectInterest, 
                    $contactPreference, 
                    $eventDate, 
                    $campus['CampusID']
                ]);

                $successMessage = 'Booking successfully created!';
            } else {
                $errorMessage = 'Campus not found for this subject.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Error processing the booking: ' . $e->getMessage();
        }
    }
}
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

<!-- Mobile Frame -->
<div class="mobileFrame">
  
  <!-- Title Bar -->
  <div class="titleBar">
    <a href="homepage.php" class="backButton">&larr;</a>
    <div class="logo">Open Day Signup</div>
    <div class="logoutButtonContainer">
  <form method="POST" action="logout.php">
    <button type="submit" class="logoutButton">Logout</button>
  </form>
</div>
  </div>

  <!-- Main Content -->
  <div class="mobileContent">
    <div class="formContainer">

      <h1>Register for Open Day</h1>

      <form id="signupForm" method="POST">
        <!-- Year of Entry -->
        <div class="formElement">
          <label for="entryYear" class="formLabel">Year of Entry</label>
          <select id="entryYear" name="entryYear" required>
            <option value="" disabled selected>Select year</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>
            <option value="2027">2027</option>
            <option value="2028">2028</option>
          </select>
        </div>

        <!-- Education Level -->
        <div class="formElement">
          <label for="educationLevel" class="formLabel">Education Level</label>
          <select id="educationLevel" name="educationLevel" required>
            <option value="" disabled selected>Select level</option>
            <option value="level-4">Level 4 (Certificate)</option>
            <option value="level-5">Level 5 (Diploma/Foundation)</option>
            <option value="level-6">Level 6 (Bachelor's Degree)</option>
            <option value="level-7">Level 7 (Master's Degree)</option>
            <option value="level-8">Level 8 (Doctorate)</option>
          </select>
        </div>

        <!-- Subject Interest -->
        <div class="formElement">
          <label for="subjectInterest" class="formLabel">Subject Interest</label>
          <select id="subjectInterest" name="subjectInterest" required>
            <option value="" disabled selected>Select subject</option>
            <!-- Subject options will be populated dynamically -->
          </select>
        </div>

        <!-- Auto-filled Campus -->
        <div class="formElement">
          <label for="campus" class="formLabel">Campus</label>
          <input type="text" id="campus" name="campus" readonly placeholder="Campus will appear here">
        </div>

        <!-- Contact Preference -->
        <div class="formElement">
          <label for="contactPreference" class="formLabel">Contact Preference</label>
          <select id="contactPreference" name="contactPreference" required>
            <option value="" disabled selected>Select preference</option>
            <option value="email">Email</option>
            <option value="phone">Phone</option>
            <option value="sms">SMS</option>
          </select>
        </div>

        <!-- Booking Date -->
        <div class="formElement">
          <label for="bookingDate" class="formLabel">Open Day Date</label>
          <select id="bookingDate" name="bookingDate" required>
            <option value="" disabled selected>Select date</option>
          </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn">Register</button>
      </form>

      <!-- Message -->
      <div id="message">
        <?php
          if (isset($successMessage)) {
              echo "<p class='success'>$successMessage</p>";
          } elseif (isset($errorMessage)) {
              echo "<p class='error'>$errorMessage</p>";
          }
        ?>
      </div>
    </div>
  </div>
</div>

<!-- Event Dates and Subject Handling -->
<script>
  // Function to load event dates from the server
  async function loadEventDates() {
    try {
      const response = await fetch('../php/eventdates.php');
      const events = await response.json();
      const bookingDateSelect = document.getElementById('bookingDate');
      bookingDateSelect.innerHTML = '<option value="" disabled selected>Select date</option>';

      const seen = new Set();
      if (Array.isArray(events)) {
        events.forEach(event => {
          const rawDate = new Date(event.EventDate);
          const dateStr = rawDate.toISOString().split('T')[0]; // Use ISO format for comparison

          if (!seen.has(dateStr)) {
            seen.add(dateStr);

            const option = document.createElement('option');
            option.value = dateStr;

            // Format the date as "Month Day, Year" (e.g., "April 24, 2025")
            option.textContent = rawDate.toLocaleDateString(undefined, {
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            });

            bookingDateSelect.appendChild(option);
          }
        });
      }
    } catch (err) {
      console.error('Error loading event dates:', err);
    }
  }

  // Function to dynamically load subjects from getsubjects.php
  async function loadSubjects() {
    try {
      const response = await fetch('/php/getsubjects.php'); // Fetching subjects from the PHP script
      const subjects = await response.json();
      const subjectSelect = document.getElementById('subjectInterest');
      
      // Clear any existing options
      subjectSelect.innerHTML = '<option value="" disabled selected>Select subject</option>';

      // Populate the subject dropdown with options from the database
      subjects.forEach(subject => {
        const option = document.createElement('option');
        option.value = subject.SubjectName;
        option.textContent = subject.SubjectName;
        subjectSelect.appendChild(option);
      });
    } catch (error) {
      console.error('Error fetching subjects:', error);
    }
  }

  // Update campus field based on subject interest
  const subjectInterestSelect = document.getElementById('subjectInterest');
  subjectInterestSelect.addEventListener('change', async (e) => {
    const selectedSubject = e.target.value;
    try {
      // Fetch campus based on the selected subject using the PHP script (subjectlist.php)
      const response = await fetch(`/php/subjectlist.php?subject=${encodeURIComponent(selectedSubject)}`);
      const data = await response.json();

      if (data.campusName) {
        // Update the campus field with the name of the campus
        document.getElementById('campus').value = data.campusName;
      } else {
        document.getElementById('campus').value = 'No campus found for this subject';
      }
    } catch (err) {
      console.error('Error fetching campus:', err);
      document.getElementById('campus').value = 'Error fetching campus';
    }
  });

  // Load subjects when the page loads
  window.onload = function() {
    loadEventDates();
    loadSubjects(); // Load the subject list
  };
</script>

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

resetInactivityTimer(); // Start the timer initially
</script>

</body>
</html>

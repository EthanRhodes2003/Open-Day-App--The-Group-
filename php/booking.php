<?php
session_start();
include '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$accountID = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entryYear = $_POST['entryYear'] ?? '';
    $educationLevel = $_POST['educationLevel'] ?? '';
    $subjectInterest = $_POST['subjectInterest'] ?? '';
    $contactPreference = $_POST['contactPreference'] ?? '';
    $eventDate = $_POST['bookingDate'] ?? '';

    if (empty($entryYear) || empty($educationLevel) || empty($subjectInterest) || empty($contactPreference) || empty($eventDate)) {
        $errorMessage = 'All fields are required.';
    } else {
        try {
            // Get EventID from date
            $stmt = $pdo->prepare("SELECT EventID FROM EVENT WHERE EventDate = ?");
            $stmt->execute([$eventDate]);
            $eventRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($eventRow) {
                $eventID = $eventRow['EventID'];

                // Check for duplicate booking
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM BOOKING WHERE AccountID = ? AND EventID = ?");
                $stmt->execute([$accountID, $eventID]);
                $alreadyBooked = $stmt->fetchColumn();

                if ($alreadyBooked) {
                    $errorMessage = "You’ve already booked this Open Day.";
                } else {
                    // Get campus based on subject
                    $stmt = $pdo->prepare("SELECT c.CampusID, c.Name AS campusName 
                                           FROM CAMPUS c
                                           INNER JOIN SUBJECT_TO_CAMPUS stc ON c.CampusID = stc.CampusID
                                           WHERE stc.SubjectName = ?");
                    $stmt->execute([$subjectInterest]);
                    $campus = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($campus) {
                        $stmt = $pdo->prepare("INSERT INTO BOOKING (AccountID, YearOfEntry, LevelOfInterest, SubjectOfInterest, ContactPreference, EventID, CampusID)
                                               VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $accountID,
                            $entryYear,
                            $educationLevel,
                            $subjectInterest,
                            $contactPreference,
                            $eventID,
                            $campus['CampusID']
                        ]);
                        $successMessage = 'Booking successfully created!';
                    } else {
                        $errorMessage = 'No campus found for the selected subject.';
                    }
                }
            } else {
                $errorMessage = 'Invalid event date selected.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Booking failed: ' . $e->getMessage();
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
        <!-- Entry Year -->
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

        <!-- Education Level -->
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

        <!-- Subject Interest -->
        <div class="formElement">
          <label for="subjectInterest" class="formLabel">Subject Interest</label>
          <select id="subjectInterest" name="subjectInterest" required>
            <option value="" disabled selected>Select subject</option>
          </select>
        </div>

        <!-- Campus Auto-filled -->
        <div class="formElement">
          <label for="campus" class="formLabel">Campus</label>
          <input type="text" id="campus" name="campus" readonly placeholder="Campus will appear here">
        </div>

        <!-- Contact Preference -->
        <div class="formElement">
          <label for="contactPreference" class="formLabel">Contact Preference</label>
          <select id="contactPreference" name="contactPreference" required>
            <option value="" disabled selected>Select preference</option>
            <option value="Email">Email</option>
            <option value="Phone">Phone</option>
            <option value="SMS">SMS</option>
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

      <!-- Message Area -->
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

<!-- JavaScript to load data dynamically -->
<script>
  async function loadEventDates() {
  try {
    const response = await fetch('../php/eventdates.php');
    const events = await response.json();
    const bookingDateSelect = document.getElementById('bookingDate');
    bookingDateSelect.innerHTML = '<option value="" disabled selected>Select date</option>';

    const seen = new Set();
    events.forEach(event => {
      const rawDate = new Date(event.EventDate);
      const dateStr = rawDate.toISOString().split('T')[0];
      if (!seen.has(dateStr)) {
        seen.add(dateStr);
        const option = document.createElement('option');
        option.value = dateStr;

        // Format as DD/MM/YYYY
        option.textContent = `${String(rawDate.getDate()).padStart(2, '0')}/${
          String(rawDate.getMonth() + 1).padStart(2, '0')}/${rawDate.getFullYear()}`;

        bookingDateSelect.appendChild(option);
      }
    });
  } catch (err) {
    console.error('Error loading event dates:', err);
  }
}

  async function loadSubjects() {
    try {
      const response = await fetch('/php/getsubjects.php');
      const subjects = await response.json();
      const subjectSelect = document.getElementById('subjectInterest');
      subjectSelect.innerHTML = '<option value="" disabled selected>Select subject</option>';
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

  document.getElementById('subjectInterest').addEventListener('change', async (e) => {
    const selectedSubject = e.target.value;
    try {
      const response = await fetch(`/php/subjectlist.php?subject=${encodeURIComponent(selectedSubject)}`);
      const data = await response.json();
      document.getElementById('campus').value = data.campusName || 'No campus found for this subject';
    } catch (err) {
      console.error('Error fetching campus:', err);
      document.getElementById('campus').value = 'Error fetching campus';
    }
  });

  window.onload = () => {
    loadEventDates();
    loadSubjects();
  };
</script>

</body>
</html>

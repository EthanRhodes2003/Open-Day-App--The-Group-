<?php
// Start the session to manage admin login state
session_start();
// Include the database connection file
include '../php/db.php';

// Check if the admin is logged in; if not, redirect to the index (login) page
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.html"); // Redirect to login page if not logged in
    exit();
}

// Fetch all booking data from the database, joining related tables for details
$bookings = $pdo->query("
    SELECT
        b.BookingID, b.AccountID,
        b.EventID, b.LevelOfInterest, b.SubjectOfInterest, b.YearOfEntry,
        e.EventDate, b.CampusID, c.Name AS CampusName, b.ContactPreference
    FROM BOOKING b
    JOIN ACCOUNT a ON b.AccountID = a.AccountID
    JOIN EVENT e ON b.EventID = e.EventID
    JOIN CAMPUS c ON b.CampusID = c.CampusID
    ORDER BY b.BookingID ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all accounts data from the database
$accounts = $pdo->query("SELECT * FROM ACCOUNT")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bookings Data - Wolvo Open Day</title>
  <link rel="stylesheet" href="../css/homepage.css" />
  <link rel="stylesheet" href="../css/bookingsdata.css" />
</head>
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

    <section id="bookings">
      <div class="sectionHeader">
        <h2>Booking Data</h2>
        <div class="filterInputs">
          <input type="text" id="filterBookingID" placeholder="Booking ID" />
          <input type="text" id="filterAccountID" placeholder="Account ID" />
          <input type="text" id="filterEventID" placeholder="Event ID" />
          <input type="text" id="filterCampusID" placeholder="Campus ID" />
        </div>
      </div>

      <div class="tableWrapper">
        <table id="bookingsTable">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Account ID</th>
              <th>Event ID</th>
              <th>Event Date</th>
              <th>Level of Interest</th> <th>Subject Interest</th> <th>Year of Entry</th>    <th>Campus ID</th>
              <th>Campus Name</th>
              <th>Contact Preference</th> </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['BookingID']) ?></td>
                <td><?= htmlspecialchars($row['AccountID']) ?></td>
                <td><?= htmlspecialchars($row['EventID']) ?></td>
                <td><?= date('d/m/Y', strtotime($row['EventDate'])) ?></td>
                <td><?= htmlspecialchars($row['LevelOfInterest']) ?></td> <td><?= htmlspecialchars($row['SubjectOfInterest']) ?></td> <td><?= htmlspecialchars($row['YearOfEntry']) ?></td>    <td><?= htmlspecialchars($row['CampusID']) ?></td>
                <td><?= htmlspecialchars($row['CampusName']) ?></td>
                <td><?= htmlspecialchars($row['ContactPreference']) ?></td> </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <hr style="margin: 2rem 0;">
    <section id="accounts">
      <div class="sectionHeader">
        <h2>Account Data</h2>
        <input type="text" id="accountFilter" placeholder="Filter by Account ID" />
      </div>

      <div class="tableWrapper">
        <table id="accountsTable">
          <thead>
            <tr>
              <th>Account ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Birthday</th>
              <th>Country</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($accounts as $acc): ?>
              <tr>
                <td><?= htmlspecialchars($acc['AccountID']) ?></td>
                <td><?= htmlspecialchars($acc['FirstName'] . ' ' . htmlspecialchars($acc['LastName'])) ?></td>
                <td><?= htmlspecialchars($acc['Email']) ?></td>
                <td><?= htmlspecialchars($acc['Phone']) ?></td>
                <td><?= date('d/m/Y', strtotime($acc['Birthday'])) ?></td>
                <td><?= htmlspecialchars($acc['Country']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</div>

<script src="../js/adminlogin.js"></script>
<script>
// Filtering functionality for the bookings table based on Booking ID
document.getElementById("filterBookingID").addEventListener("input", function () {
  const filter = this.value.trim().toLowerCase();
  const rows = document.querySelectorAll("#bookingsTable tbody tr");
  rows.forEach(row => {
    const bookingID = row.cells[0].textContent.toLowerCase();
    row.style.display = bookingID.includes(filter) ? "" : "none";
  });
});

// Filtering functionality for the bookings table based on Account ID
document.getElementById("filterAccountID").addEventListener("input", function () {
  const filter = this.value.trim().toLowerCase();
  const rows = document.querySelectorAll("#bookingsTable tbody tr");
  rows.forEach(row => {
    const accountID = row.cells[1].textContent.toLowerCase();
    row.style.display = accountID.includes(filter) ? "" : "none";
  });
});

// Filtering functionality for the bookings table based on Event ID
document.getElementById("filterEventID").addEventListener("input", function () {
  const filter = this.value.trim().toLowerCase();
  const rows = document.querySelectorAll("#bookingsTable tbody tr");
  rows.forEach(row => {
    const eventID = row.cells[2].textContent.toLowerCase();
    row.style.display = eventID.includes(filter) ? "" : "none";
  });
});

// Filtering functionality for the bookings table based on Campus ID
document.getElementById("filterCampusID").addEventListener("input", function () {
  const filter = this.value.trim().toLowerCase();
  const rows = document.querySelectorAll("#bookingsTable tbody tr");
  rows.forEach(row => {
    const campusID = row.cells[7].textContent.toLowerCase();
    row.style.display = campusID.includes(filter) ? "" : "none";
  });
});

// Filtering the account table based on Account ID
document.getElementById("accountFilter").addEventListener("input", function () {
  const filter = this.value.trim().toLowerCase();
  const rows = document.querySelectorAll("#accountsTable tbody tr");
  rows.forEach(row => {
    const accountID = row.cells[0].textContent.toLowerCase();
    row.style.display = accountID.includes(filter) ? "" : "none";
  });
});
</script>

</body>
</html>
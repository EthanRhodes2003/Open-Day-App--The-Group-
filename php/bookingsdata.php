<?php
session_start();
include '../php/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.html");
    exit();
}

$bookings = $pdo->query("
    SELECT 
        b.BookingID, b.AccountID, a.Phone, a.Email, a.Birthday, a.Country, 
        b.EventID, b.LevelOfInterest, b.SubjectOfInterest, b.YearOfEntry,
        e.EventDate, b.CampusID, c.Name AS CampusName,
        CONCAT(a.FirstName, ' ', a.LastName) AS FullName
    FROM BOOKING b
    JOIN ACCOUNT a ON b.AccountID = a.AccountID
    JOIN EVENT e ON b.EventID = e.EventID
    JOIN CAMPUS c ON b.CampusID = c.CampusID
")->fetchAll(PDO::FETCH_ASSOC);

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
  <style>
    .sectionHeader input[type="text"] {
      padding: 8px 12px;
      margin: 8px 8px 15px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
      max-width: 200px;
    }
    .filterInputs {
      display: flex;
      flex-wrap: wrap;
    }
  </style>
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
    
    <!-- Booking Data -->
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
              <th>Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>DOB</th>
              <th>Country</th>
              <th>Event ID</th>
              <th>Event Date</th>
              <th>Level of Interest</th>
              <th>Subject</th>
              <th>Campus ID</th>
              <th>Campus Name</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['BookingID']) ?></td>
                <td><?= htmlspecialchars($row['AccountID']) ?></td>
                <td><?= htmlspecialchars($row['FullName']) ?></td>
                <td><?= htmlspecialchars($row['Phone']) ?></td>
                <td><?= htmlspecialchars($row['Email']) ?></td>
                <td><?= htmlspecialchars($row['Birthday']) ?></td>
                <td><?= htmlspecialchars($row['Country']) ?></td>
                <td><?= htmlspecialchars($row['EventID']) ?></td>
                <td><?= htmlspecialchars($row['EventDate']) ?></td>
                <td><?= htmlspecialchars($row['LevelOfInterest']) ?></td>
                <td><?= htmlspecialchars($row['SubjectOfInterest']) ?></td>
                <td><?= htmlspecialchars($row['CampusID']) ?></td>
                <td><?= htmlspecialchars($row['CampusName']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Account Data -->
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
                <td><?= htmlspecialchars($acc['FirstName'] . ' ' . $acc['LastName']) ?></td>
                <td><?= htmlspecialchars($acc['Email']) ?></td>
                <td><?= htmlspecialchars($acc['Phone']) ?></td>
                <td><?= htmlspecialchars($acc['Birthday']) ?></td>
                <td><?= htmlspecialchars($acc['Country']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
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
  }, 5 * 60 * 1000);
}
['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
  document.addEventListener(evt, resetInactivityTimer, false);
});
resetInactivityTimer();

// Booking Filters
const bookingFilters = {
  filterBookingID: 0,
  filterAccountID: 1,
  filterEventID: 7,
  filterCampusID: 11
};

Object.entries(bookingFilters).forEach(([inputID, columnIndex]) => {
  document.getElementById(inputID).addEventListener("input", function () {
    const filterValues = Object.entries(bookingFilters).map(([id, col]) => ({
      colIndex: col,
      value: document.getElementById(id).value.trim().toLowerCase()
    }));

    const rows = document.querySelectorAll("#bookingsTable tbody tr");
    rows.forEach(row => {
      const cells = row.getElementsByTagName("td");
      const visible = filterValues.every(f => cells[f.colIndex].textContent.toLowerCase().includes(f.value));
      row.style.display = visible ? "" : "none";
    });
  });
});

// Account Filter
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

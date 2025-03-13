// Add event listener for back button
document.addEventListener('DOMContentLoaded', function() {
  const backButton = document.querySelector('.back-button');
  if (backButton) {
    backButton.addEventListener('click', function() {
      window.location.href = "homepage.html";
    });
  }
});


// Check if the admin is logged in when the page loads
window.onload = function() {
  if (!localStorage.getItem("adminLoggedIn")) {
    // If the admin is not logged in, redirect to the login page
    window.location.href = "adminlogin.html";
  } else {
    // If logged in, load and display the booking data
    loadBookingData();
  }
};

// Mock booking data (This would typically come from a server in a real-world scenario)
const bookings = [
  {
    bookingID: "001",
    accountID: "101",
    phone: "07495483903",
    email: "user1@example.com",
    dob: "1998-03-25",
    country: "UK",
    eventID: "EVT01",
    eventTitle: "March Open Day",
    eventDate: "2025-03-26",
    levelOfInterest: "Level 3",
    subjectOfInterest: "Computer Science"
  },
  // Add more mock data as needed
];

// Function to render the booking data on the page
function loadBookingData() {
  const tbody = document.getElementById("bookings-table-body");

  bookings.forEach(booking => {
    const row = document.createElement("tr");

    // Loop through each booking property and add it to the table
    Object.keys(booking).forEach(key => {
      const td = document.createElement("td");
      td.textContent = booking[key];
      row.appendChild(td);
    });

    tbody.appendChild(row); // Append the row to the table
  });
}

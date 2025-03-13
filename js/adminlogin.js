document.getElementById("admin-login-form").addEventListener("submit", function(event) {
  event.preventDefault(); // Prevent the form from submitting and refreshing the page

  // Hardcoded admin credentials for testing
  const adminEmail = "admin@example.com";
  const adminPassword = "admin123";

  // Get the email and password entered by the user
  const email = document.getElementById("admin-email").value;
  const password = document.getElementById("admin-password").value;

  // Check if the entered credentials match the admin credentials
  if (email === adminEmail && password === adminPassword) {
    // If credentials are correct, save the login status in localStorage
    localStorage.setItem("adminLoggedIn", "true");

    // Redirect the user to the bookings data page
    window.location.href = "bookingsdata.html";
  } else {
    // Show an alert if the credentials are incorrect
    alert("Invalid credentials. Please try again.");
  }
});

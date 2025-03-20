// Handle Sign Up form submission
document.getElementById("signup-form").addEventListener("submit", function(event) {
  event.preventDefault(); // Prevent form from submitting
});

// Handle Log In form submission (with test credentials)
document.getElementById("login-form").addEventListener("submit", function(event) {
  event.preventDefault(); // Prevent form from submitting

  // Get the entered email and password
  const email = document.getElementById("login-email").value;
  const password = document.getElementById("login-password").value;

  // Test credentials
  const testEmail = "test@example.com";
  const testPassword = "password123";

  if (email === testEmail && password === testPassword) {
    // If login is successful, store login status in localStorage
    localStorage.setItem("loggedIn", "true");

    window.location.href = "index.html"; // Redirect to the homepage
  } else {
    alert("Invalid credentials. Please try again."); 
  }
});

// Handle back button click
document.getElementById("back-button").addEventListener("click", function() {
  window.location.href = "index.html"; // Navigate back to index.html
});
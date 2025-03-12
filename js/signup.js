// Handle Sign Up form submission
document.getElementById("signup-form").addEventListener("submit", function(event) {
  event.preventDefault(); // Prevent form from submitting
  alert("Sign up will be sorted soon"); // Placeholder message
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

    // Redirect to homepage
    window.location.href = "index.html"; // Redirect to the homepage
  } else {
    alert("Invalid credentials. Please try again."); // Error message
  }
});

// Check if script is loading
console.log("Admin login script loaded!");

// Wait for the DOM to be fully loaded before adding event listeners
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("admin-login-form");

    if (loginForm) {
        loginForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission

            // Hardcoded admin credentials for testing
            const adminEmail = "admin@example.com";
            const adminPassword = "admin123";

            // Get user input values
            const email = document.getElementById("admin-email").value;
            const password = document.getElementById("admin-password").value;

            console.log("User entered:", email, password); 

            // Validate credentials
            if (email === adminEmail && password === adminPassword) {

                console.log("Login successful, redirecting...");

                // Redirect to bookings data page
                window.location.href = "bookingsdata.html";
            } else {
                alert("Invalid credentials. Please try again.");
            }
        });
    } else {
        console.error("Admin login form not found!");
    }
});

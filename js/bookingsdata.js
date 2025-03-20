// Wait for the DOM to be fully loaded before adding event listeners
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("admin-login-form");

    if (loginForm) {
        loginForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevents the form from submitting

            // Hardcoded admin credentials for testing
            const adminEmail = "admin@example.com";
            const adminPassword = "admin123";

            // Get user input values
            const email = document.getElementById("admin-email").value.trim();
            const password = document.getElementById("admin-password").value.trim();

            console.log("User entered:", email, password); 

            // Validate credentials
            if (email === adminEmail && password === adminPassword) {
                console.log("Login successful, redirecting...");

                // Ensure form does not submit
                event.stopPropagation(); 

                // Redirect to bookings data page
                window.location.replace("bookingsdata.html");
            } else {
                alert("Invalid credentials. Please try again.");
            }
        });
    } else {
        console.error("Admin login form not found!");
    }
});

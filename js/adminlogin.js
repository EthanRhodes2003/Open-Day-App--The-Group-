console.log("Admin login script loaded!");

// Wait for DOM to fully load
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("adminLoginForm");

    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            // Prevent the form from submitting
            event.preventDefault();

            const email = document.getElementById("adminEmail").value;
            const password = document.getElementById("adminPassword").value;

            // Prepare the form data to be sent
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            // Send AJAX request to adminlogin.php
            fetch("../php/adminlogin.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json()) // Expect JSON response
            .then(data => {
                const messageDiv = document.getElementById("loginMessage");

                if (data.success) {
                    window.location.href = data.redirect; // Takes the user to bookingsdata.php after login.
                } else {
                    // Display error message if login fails
                    messageDiv.innerText = data.message; // Display error message
                    messageDiv.style.color = "red"; // Set error message color to red
                }
            })
            .catch(error => {
                console.error("Error during login:", error);
                const messageDiv = document.getElementById("loginMessage");
                messageDiv.innerText = "An error occurred. Please try again later.";
                messageDiv.style.color = "red";
            });
        });
    } else {
        console.error("Admin login form not found!");
    }
});

// Check if script is loading
console.log("Admin login script loaded!");

// Wait for DOM to fully load
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("admin-login-form");

    if (loginForm) {
        loginForm.addEventListener("submit", function(event) {
            // Don't block form submission to PHP — just log the input
            const email = document.getElementById("admin-email").value;
            const password = document.getElementById("admin-password").value;

            console.log("Submitting form with:", email, password);
            // Form will now naturally submit to adminlogin.php
        });
    } else {
        console.error("Admin login form not found!");
    }
});

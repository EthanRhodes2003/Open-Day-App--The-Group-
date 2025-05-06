document.addEventListener("DOMContentLoaded", function () {
    const signupForm = document.getElementById("signupForm");
    const loginForm = document.getElementById("loginForm");
    const passwordInput = document.getElementById("password"); // Get the password input
    const passwordRequirements = document.getElementById("passwordRequirements"); // Get the requirements list

    // Function to check password against policy and update UI
    function checkPasswordPolicy(password) {
        // Define the requirements
        const minLength = 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*]/.test(password); // Allowed special characters

        // Get the list items for feedback
        const reqs = document.querySelectorAll("#passwordRequirements li");

        // Check each requirement and update the corresponding list item class
        if (reqs.length >= 5) { // Make sure we have all the list items
            // Check length
            if (password.length >= minLength) {
                reqs[0].className = 'valid'; // Mark as valid
            } else {
                reqs[0].className = 'invalid'; // Mark as invalid
            }

            // Check uppercase
            if (hasUppercase) {
                reqs[1].className = 'valid';
            } else {
                reqs[1].className = 'invalid';
            }

            // Check lowercase
            if (hasLowercase) {
                reqs[2].className = 'valid';
            } else {
                reqs[2].className = 'invalid';
            }

            // Check number
            if (hasNumber) {
                reqs[3].className = 'valid';
            } else {
                reqs[3].className = 'invalid';
            }

            // Check special character
            if (hasSpecial) {
                reqs[4].className = 'valid';
            } else {
                reqs[4].className = 'invalid';
            }
        }


        // Return true only if all requirements are met
        return password.length >= minLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;
    }

    // Add event listener to password input for real-time feedback
    // Make sure the password input and requirements list exist
    if (passwordInput && passwordRequirements) {
        passwordInput.addEventListener("input", function () {
            checkPasswordPolicy(this.value);
        });
    }


    // Handle Sign Up form submission
    if (signupForm) {
        signupForm.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent form from submitting

            const password = document.getElementById("password").value;
            const messageDiv = document.getElementById("signupMessage");

            // Client-side password policy check before sending
            if (!checkPasswordPolicy(password)) {
                 messageDiv.innerText = "Password does not meet the required policy.";
                 messageDiv.style.color = "red";
                 return; // Prevent form submission if policy is not met
            }

            const formData = new FormData(signupForm);

            fetch("php/signup.php", {
                method: "POST",
                body: formData,
            })
                .then(res => res.json()) // Parse the JSON response from PHP
                .then(data => {
                
                    if (data.message) {
                        messageDiv.innerText = data.message;
                        messageDiv.style.color = data.success ? "green" : "red";
                    }

                    // If sign up is successful, redirect to the homepage
                    if (data.success) {
                        // Add a small delay before redirecting to allow message to be seen
                        setTimeout(() => {
                             window.location.href = data.redirect;
                        }, 1000); // 1 second delay
                    }
                })
                .catch(err => {
                    console.error(err);
             
                    messageDiv.innerText = "There was an error during signup. Please try again.";
                    messageDiv.style.color = "red";
                });
        });
    }

    // Handle Log In form submission
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent form from submitting

            const formData = new FormData(loginForm);

            fetch("php/login.php", {
                method: "POST",
                body: formData,
            })
                .then(res => res.json()) // Parse the JSON response from PHP
                .then(data => {
                    const messageDiv = document.getElementById("loginMessage");

                    // If message exists, display it
                    if (data.message) {
                        messageDiv.innerText = data.message;
                        messageDiv.style.color = data.success ? "green" : "red";
                    }

                    if (data.success) {
                        // Redirect to homepage if login is successful
                         // Add a small delay before redirecting to allow message to be seen
                         setTimeout(() => {
                            window.location.href = data.redirect || "php/homepage.php";
                         }, 1000); // 1 second delay
                    }
                })
                .catch(err => {
                    console.error("Error during login:", err); // More specific error log
                    const messageDiv = document.getElementById("loginMessage");
                    messageDiv.innerText = "There was an error during login. Please try again.";
                    messageDiv.style.color = "red";
                });
        });
    }
});
document.addEventListener("DOMContentLoaded", function () {
  const signupForm = document.getElementById("signupForm");  
  const loginForm = document.getElementById("loginForm");    

  // Handle Sign Up form submission
  if (signupForm) {
      signupForm.addEventListener("submit", function (e) {
          e.preventDefault(); // Prevent form from submitting

          const formData = new FormData(signupForm);

          fetch("php/signup.php", {
              method: "POST",
              body: formData,
          })
              .then(res => res.json()) // Parse the JSON response from PHP
              .then(data => {
                  const messageDiv = document.getElementById("signupMessage"); 

                  if (data.message) {
                      messageDiv.innerText = data.message;
                      messageDiv.style.color = data.success ? "green" : "red";
                  }

                  // If sign up is successful, redirect to the homepage
                  if (data.success) {
                      window.location.href = data.redirect;
                  }
              })
              .catch(err => {
                  console.error(err);
                  const messageDiv = document.getElementById("signupMessage");
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
                      window.location.href = data.redirect || "php/homepage.php";
                  }
              })
              .catch(err => {
                  console.error(err);
                  const messageDiv = document.getElementById("loginMessage");
                  messageDiv.innerText = "There was an error during login. Please try again.";
                  messageDiv.style.color = "red";
              });
      });
  }
});

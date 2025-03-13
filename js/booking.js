// Initialize the Flatpickr to handle the booking date selection
flatpickr("#bookingDate", {
  dateFormat: "Y-m-d",
  enable: [
      function(date) {
          const day = date.getDay();
          return day === 1 || day === 6; // Only allow Mondays and Saturdays
      }
  ],
  minDate: "today" // Prevent selecting past dates
});

// Handle form submission for the signup process
document.getElementById('signupForm').addEventListener('submit', async function(e) {
  e.preventDefault(); // Prevent page reload on form submission

  // Collecting the form data entered by the user
  const formData = {
      firstName: document.getElementById('firstName').value.trim(),
      lastName: document.getElementById('lastName').value.trim(),
      email: document.getElementById('email').value.trim(),
      bookingDate: document.getElementById('bookingDate').value.trim()
  };

  // Basic validation to check if all fields are filled
  if (!formData.firstName || !formData.lastName || !formData.email || !formData.bookingDate) {
      document.getElementById('message').textContent = 'Please fill in all the required fields.';
      return;
  }

  try {
      // Send the form data to the server for registration
      const response = await fetch('/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData)
      });

      const data = await response.json();

      // Handle the server response
      if (response.ok) {
          document.getElementById('message').textContent = `Registration successful! Your booking ID is: ${data.bookingID}. A confirmation email has been sent.`;
          document.getElementById('signupForm').reset(); // Reset the form after successful registration
      } else {
          document.getElementById('message').textContent = data.error || 'Registration failed. Please try again.';
      }
  } catch (error) {
      console.error('Error:', error);
      document.getElementById('message').textContent = 'Something went wrong. Please try again later.';
  }
});

flatpickr("#bookingDate", {
  dateFormat: "Y-m-d",
  enable: [
      function(date) {
          const day = date.getDay();
          return day === 1 || day === 6; // Allow only Mondays & Saturdays
      }
  ],
  minDate: "today"
});

// Handle Signup Form Submission
document.getElementById('signupForm').addEventListener('submit', async function(e) {
  e.preventDefault(); // Prevent page reload

  // Collect and form data
  const formData = {
      firstName: document.getElementById('firstName').value.trim(),
      lastName: document.getElementById('lastName').value.trim(),
      email: document.getElementById('email').value.trim(),
      bookingDate: document.getElementById('bookingDate').value.trim()
  };

  // Basic Client-side Validation
  if (!formData.firstName || !formData.lastName || !formData.email || !formData.bookingDate) {
      document.getElementById('message').textContent = 'All fields are required!';
      return;
  }

  try {
      // Send data to server
      const response = await fetch('/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData)
      });

      const data = await response.json();

      if (response.ok) {
          document.getElementById('message').textContent = `Registration successful! Your booking ID is: ${data.bookingID}. A confirmation email has been sent.`;
          document.getElementById('signupForm').reset();
      } else {
          document.getElementById('message').textContent = data.error || 'Registration failed. Please try again.';
      }
  } catch (error) {
      console.error('Error:', error);
      document.getElementById('message').textContent = 'An error occurred. Please try again later.';
  }
});
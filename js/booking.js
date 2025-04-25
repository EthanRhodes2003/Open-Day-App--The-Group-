// Fetch event dates and show in drop down
async function loadEventDates() {
    try {
      // Fetch event dates from the eventdates.php script
      const response = await fetch('../php/eventdates.php');
      const events = await response.json();
  
      const bookingDateSelect = document.getElementById('bookingDate');
  
      // Check if events were fetched successfully
      if (Array.isArray(events) && events.length > 0) {
        events.forEach(event => {
          const option = document.createElement('option');
          option.value = event.EventID; // Use EventID as the value
          // Format the date as "Month Day, Year"
          option.textContent = new Date(event.EventDate).toLocaleDateString();
          bookingDateSelect.appendChild(option);
        });
      } else {
        // If no events are available, show a message
        const option = document.createElement('option');
        option.disabled = true;
        option.textContent = 'No open day events available';
        bookingDateSelect.appendChild(option);
      }
    } catch (error) {
      console.error('Error loading event dates:', error);
      const bookingDateSelect = document.getElementById('bookingDate');
      const option = document.createElement('option');
      option.disabled = true;
      option.textContent = 'Error loading event dates';
      bookingDateSelect.appendChild(option);
    }
  }
  
  // Call the function to load event dates when the page loads
  loadEventDates();
  
  // Handle form submission
  document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevent page reload on form submission
  
    // Collect the form data
    const formData = {
      entryYear: document.getElementById('entryYear').value,
      educationLevel: document.getElementById('educationLevel').value,
      subjectInterest: document.getElementById('subjectInterest').value,
      contactPreference: document.getElementById('contactPreference').value,
      bookingDate: document.getElementById('bookingDate').value,
      eventID: document.getElementById('bookingDate').selectedOptions[0].value // Add EventID to the data
    };
  
    // Validate all fields are filled
    for (const key in formData) {
      if (!formData[key]) {
        document.getElementById('message').textContent = 'Please fill in all the required fields.';
        document.getElementById('message').className = 'error';
        return;
      }
    }
  
    try {
      // Send the form data to the PHP script for processing
      const response = await fetch('../php/submitbooking.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(formData)
      });
  
      const data = await response.json();
  
      console.log(data); // Log the response for debugging
  
      // Check if the booking was successfully processed
      if (data.success) {
        document.getElementById('message').textContent = 'Booking successful!';
        document.getElementById('message').className = 'success';
        window.location.href = '../php/account.php';  // Redirect to account page after successful booking
      } else {
        document.getElementById('message').textContent = data.message || 'Booking failed. Please try again.';
        document.getElementById('message').className = 'error';
      }
    } catch (error) {
      console.error('Error during form submission:', error);
      document.getElementById('message').textContent = 'An error occurred. Please try again.';
      document.getElementById('message').className = 'error';
    }
  });
  
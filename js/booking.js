// Handle the contact preference change
document.getElementById('contactPreference').addEventListener('change', function() {
    const preference = this.value;
    
    // No need to update contact info fields since they've been removed
});

// Handle form submission
document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevent page reload on form submission

    // Collect the form data
    const formData = {
        entryYear: document.getElementById('entryYear').value,
        educationLevel: document.getElementById('educationLevel').value, // Updated field name
        subjectInterest: document.getElementById('subjectInterest').value,
        contactPreference: document.getElementById('contactPreference').value,
        bookingDate: document.getElementById('bookingDate').value
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
        // Send the form data to the server
        const response = await fetch('/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        // Handle the server response
        if (response.ok) {
            document.getElementById('message').textContent = `Registration successful! Your booking ID is: ${data.bookingID}. You will be contacted via your preferred method.`;
            document.getElementById('message').className = 'success';
            document.getElementById('signupForm').reset();
        } else {
            document.getElementById('message').textContent = data.error || 'Registration failed. Please try again.';
            document.getElementById('message').className = 'error';
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('message').textContent = 'Something went wrong. Please try again later.';
        document.getElementById('message').className = 'error';
    }
});
// Handle the contact preference change
document.getElementById('contactPreference').addEventListener('change', function() {
    const preference = this.value;
    
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
})
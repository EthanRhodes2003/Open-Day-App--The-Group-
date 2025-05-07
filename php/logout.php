<?php
// Start the session
session_start();
// Unset all session variables
session_unset();
// Destroy the session
session_destroy();
// Redirect the user to the index (login) page after logging out
header("Location: ../index.html"); // Redirect to login page
exit();
?>
/*<?php
session_start();

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to index.html after logging out
header("Location: index.html");
exit();
?>

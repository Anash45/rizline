<?php
// Start the session
session_start();

// Destroy the session
session_destroy();

// Clear the cookies by setting them to expire in the past
setcookie('username', '', time() - 3600, '/'); // Expire in the past
setcookie('password', '', time() - 3600, '/'); // Expire in the past

// Redirect to the sign-in page
header('Location: signin.php');
exit();
?>

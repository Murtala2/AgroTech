<?php
session_start();
session_unset();    // clear all session variables
session_destroy();  // destroy the session

header("Location: bootstrap-5.0.2-dist/Welcome.html");  // redirect to welcome or login page
exit;
?>

<?php
include("DB/conn.php");
session_start();

session_unset();

// Destroy the session
session_destroy();

// Redirect to the register page
header("Location: register.php");
exit;
?>

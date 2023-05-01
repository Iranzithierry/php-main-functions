<?php

session_start();
session_unset();

// Destroy the session
session_destroy();

// Redirect to the register page
header("Location:admin_login.php");
exit;

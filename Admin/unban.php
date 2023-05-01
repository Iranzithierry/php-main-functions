<?php
// Establish database connection
include("../DB/conn.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user ID from query string
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header("Location: admin.php");
    exit();
}

// Update banned_until column for user ID to NULL
$stmt = $conn->prepare("UPDATE users SET banned_until = NULL WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $email = $row['email'];
}

// $sender = "Tabnine Income";
// $message = "Hello " . $email . " Your Tabnine Account Was Unbanned From Now You Can Visit Tabnine Income";
// $subject =  "Your Account Was Unbanned";

// if (mail($email, $subject, $message, $sender)) {
// } else {

//     echo "Failed while sending your mail!";
// }
$done = "User Unbanned Successfully";
// Redirect to admin page
header("Location: admin.php?done=$done");
exit();
?>

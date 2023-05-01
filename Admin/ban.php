<?php
include("../DB/conn.php");

$id = $_GET['id'];
$duration = $_GET['duration'];

switch ($duration) {
    case 'forever':
        $seconds = 199592000;
        break;
    case '1month':
        $seconds = 2592000;
        break;
    default:
        $seconds = 0;
        break;
}

$banned_until = date('Y-m-d H:i:s', time() + $seconds);

$stmt = $conn->prepare("UPDATE users SET banned_until = ? WHERE id = ?");
$stmt->bind_param("si", $banned_until, $id);
$stmt->execute();

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $email = $row['email'];
}

// $sender = "Tabnine Income";
// $message = "Hello " . $email . " Your Tabnine Account Was Banned Until " . $banned_until . "";
// $subject =  "Your Account Was Banned";

// if (mail($email, $subject, $message, $sender)) {
// } else {

//     echo "Failed while sending your mail!";
// }

$done = "User Was Banned Successfully";
// Redirect to admin page
header("Location: admin.php?done=$done");
exit();

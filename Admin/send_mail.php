<?php session_start();

include("../DB/conn.php");
if (isset($_SESSION['username']) && $_SESSION['admin_permission'] = true) {
} else {
    header("location:form_admin.php");
}

// Get email addresses of active users
$stmt = $conn->prepare("SELECT email FROM users WHERE banned_until IS NULL OR banned_until <= NOW()");

$stmt->execute();

$active_result = $stmt->get_result();

$active_users = [];

while ($row = $active_result->fetch_assoc()) {
    $active_users[] = $row['email'];
}

// Get email addresses of banned users
$stmt = $conn->prepare("SELECT email FROM users WHERE banned_until > NOW()");

$stmt->execute();

$banned_result = $stmt->get_result();

$banned_users = [];

while ($row = $banned_result->fetch_assoc()) {
    $banned_users[] = $row['email'];
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (isset($_POST['active'])) {

        $message_tobe_sent = $_POST['message'];

        if (empty($message_tobe_sent)) {
            $done = "Please Enter Message";
            header("Location: admin.php?error=$done");
        } else {

            $sender = "Tabnine Income";
            $message = "" . $message_tobe_sent . "";
            $subject =  "You Have Message From Tabnine";

            if (count($active_users) <= 0) {

                $done = "No Active Users To Send Mail";
                header("Location: admin.php?error=$done");
            } else {
                foreach ($active_users as $user) {
                    if (mail($user, $subject, $message, $sender)) {
                        $done = "Message Was Successfully Sent To Active Users";
                        header("Location: admin.php?done=$done");
                    } else {
                        $done = "Failed while sending your mail!";
                        header("Location: admin.php?error=$done");
                    }
                }
            }
        }
    } else {

        if (isset($_POST['banned'])) {

            $message_tobe_sent = $_POST['message'];

            if (empty($message_tobe_sent)) {
                $done = "Please Enter Message";
                header("Location: admin.php?error=$done");
            } else {

                $sender = "Tabnine Income";
                $message = "" . $message_tobe_sent . "";
                $subject =  "YOU HAVE MESSAGE FROM TABNINE INCOME";
                if (count($banned_users) <= 0) {

                    $done = "No Banned Users To Send Mail";
                    header("Location: admin.php?error=$done");
                } else {
                    foreach ($banned_users as $user) {
                        if (mail($user, $subject, $message, $sender)) {
                            $done = "Message Was Successfully Sent To Banned Users";
                            header("Location: admin.php?done=$done");
                        } else {
                            $done = "Failed while sending your mail!";
                            header("Location: admin.php?error=$done");
                        }
                    }
                }
            }
        } else {
            $done = "Please Select Who You Want To Send Mail";
            header("Location: admin.php?error=$done");
        }
    }

    // Redirect to admin page
}

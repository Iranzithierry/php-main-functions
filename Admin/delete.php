<?php
require_once "../DB/conn.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $email = $user['email'];

    $stmt = $conn->prepare("SELECT email FROM recycle_bin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $email_availability = $stmt->get_result();

    $done = "";
    $restore = false;

    if ($email_availability->num_rows > 0) {
        $done = "There's Deleted User Who has already registered to this email.You Better Edit First Email";
        header("Location: admin.php?done=$done");
        exit;
    } else {
        $restore = true;
    }

    if ($restore) {

        $stmt = $conn->prepare("INSERT INTO recycle_bin(email,password, fname, sname, mobile_number, username,country,code, deleted_at) VALUES (?,?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssssss", $user['email'], $user['password'], $user['fname'], $user['sname'], $user['mobile_number'], $user['username'],$user['country'], $user['code']);
        $stmt->execute();


        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $done = "User Deleted Successfully";
            // Redirect to admin page
            header("Location: admin.php?error=$done");
        } else {
            echo "<div class='alert alert-danger'>Error deleting user: " . $conn->error . "</div>";
        }
    }
} else {
    echo "<div class='alert alert-danger'>User id not set.</div>";
    die();
}

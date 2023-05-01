<?php
include("../DB/conn.php");

if (isset($_POST['delete'])) {
    foreach ($_POST['delete'] as $Id) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $email = $user['email'];

        $stmt = $conn->prepare("SELECT email FROM recycle_bin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $email_availability = $stmt->get_result();

        if ($email_availability->num_rows > 0) {
            $done = "There's Deleted User Who has already registered to this email.You Better Edit First Email";
            header("Location: admin.php?done=$done");
            exit;
        } else {

            $stmt = $conn->prepare("INSERT INTO recycle_bin(email,password, fname, sname, mobile_number, username,country,code, deleted_at) VALUES (?,?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssssss", $user['email'], $user['password'], $user['fname'], $user['sname'], $user['mobile_number'], $user['username'],$user['country'], $user['code']);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $Id);
            if ($stmt->execute()) {
                $done = "User Deleted Successfully";
            } else {
                $error = "Error deleting user: " . $conn->error;
            }
        }
    }
    // Redirect to admin page
    if (isset($done)) {
        header("Location: admin.php?done=$done");
    } elseif (isset($error)) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
} else {
    echo "<div class='alert alert-danger'>User id not set.</div>";
    die();
}

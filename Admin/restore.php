<?php
require_once "../DB/conn.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM recycle_bin WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $email = $user['email'];

    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $email_availability = $stmt->get_result();

    $done = "";
    $restore = false;

    if ($email_availability->num_rows > 0) {
        $done = "Another user has already registered to this email.";
        header("Location: admin.php?error=$done");
    } else {
        $restore = true;
    }

    if ($restore) {
        $stmt = $conn->prepare("INSERT INTO users (fname, sname, mobile_number, email, username,country,password, code) VALUES (?,?, ?, ?, ?, ?, ?,?)");
        $stmt->bind_param("ssssssss", $user['fname'], $user['sname'], $user['mobile_number'], $user['email'], $user['username'],$user['country'], $user['password'], $user['code']);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM recycle_bin WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $done = "User restored successfully.";
            header("Location: admin.php?done=$done");
        } else {
            echo "<div class='alert alert-danger'>Error restoring user: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>$done</div>";
    }
} else {
    echo "<div class='alert alert-danger'>User ID not set.</div>";
    die();
}

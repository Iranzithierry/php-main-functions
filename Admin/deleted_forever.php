<?php
require_once "../DB/conn.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);          
    $stmt = $conn->prepare("DELETE FROM recycle_bin WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $done = "User Deleted Forever";
        // Redirect to admin page
        header("Location: admin.php?error=$done");
    } else {
        echo "<div class='alert alert-danger'>Error deleting user: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>User id not set.</div>";
    die();
}

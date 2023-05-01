<?php
include("../DB/conn.php");

session_start();
if (isset($_SESSION['loggedIn'])) {
} else {
    header("location: ../index.php");
}

$error = "";
$succes = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_number = $_POST['new_number'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $current_password = $_POST['current_password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE mobile_number = ?");
    mysqli_stmt_bind_param($stmt, "s" , $new_number);
    mysqli_stmt_execute($stmt);

    $number_check = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($number_check) > 0) {
        $error = " This Number '".$new_number."' is already Registered";
    }

    if (empty($new_password)) {
        $error = "Please enter a new password";
    }
    if (empty($confirm_password)) {
        $error = "Please confirm the new password";
    }
    if (empty($current_password)) {
        $error = "Please enter the current password";
    }
    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    }
    if(strlen($new_number) <9) {
        $error = "Number You entered is Not Valid";
    }
    if(strlen($new_password) <6) {
        $error = "Password Must Be at least 9 characters";
    }

    if (empty($error)) {
        // hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // check if the current password matches the password in the database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("s", $_SESSION['loggedIn']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            header("Location: logout.php");
          }
        $row = $result->fetch_assoc();
        $password_from_db = $row['password'];
        if (password_verify($current_password, $password_from_db)) {
            // update the user's information
            $stmt = $conn->prepare("UPDATE users SET password = ?, mobile_number = ? WHERE id = ?");
            $stmt->bind_param("sss", $hashed_password, $new_number, $_SESSION['loggedIn']);
            $stmt->execute();
            $stmt->close();

            $success =  "User updated successfully.";
            header("refresh:5;url=../home.php");
        } else {
            $error = "Current password is incorrect";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style-update-profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Update Profile</title>
</head>

<body>
    <div class="wrapper">
        <div class="input-field">
            <div class="error-text" <?php if (empty($error)) echo "style='display:none;'" ?>>
                <span><?php echo $error ?></span>
            </div>
            <div class="success" <?php if (empty($success)) echo "style='display:none;'" ?>>
                <span><?php echo $success ?></span>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="content">
                    <h3>Update New Number</h3>
                    <input type="number" name="new_number" placeholder="Enter New Number"><br>
                    <h3>Update New Password</h3>
                    <label for="new_password">New Password</label><br>
                    <input type="password" name="new_password" placeholder="Enter New Password"><br>
                    <label for="confirm_password">Confirm New Password</label><br>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password">
                    <label for="current_password" class="password">Current Password</label>
                    <!-- <input type="checkbox" name="eye" id="eye"> -->
                    <input type="password" name="current_password" placeholder="Enter Cuurent Password">
                <button type="submit">Update Now</button><br>
                </form>
            </div>
        </div>
    </div>
    <script src="../SCRIPT/script.js"></script>
</body>

</html>
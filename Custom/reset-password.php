<?php
include("../DB/conn.php");

session_start();
if (isset($_SESSION['reset']) == true) {
} else {
    header("location: ../index.php");
}

$error = "";
$succes = "";
$email = $_SESSION["email"];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password)) {
        $error = "Please enter a new password";
    }
    if (empty($confirm_password)) {
        $error = "Please confirm the new password";
    }
    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    }
    if(strlen($new_password) <6) {
        $error = "Password Must Be at least 9 characters";
    }

    if (empty($error)) {
        // hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // update the user's information
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password,$email);
            $stmt->execute();
            $stmt->close();

            $success =  "User updated successfully.";
            header("refresh:5;url=../index.php");
        } else {
            $error = "There was an error Please try again";
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
                    <label for="new_password">New Password</label><br>
                    <input type="password" name="new_password" placeholder="Enter New Password"><br>
                    <label for="confirm_password">Confirm New Password</label><br>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password">
                <button type="submit">Update Now</button><br>
                </form>
            </div>
        </div>
    </div>
    <script src="../SCRIPT/script.js"></script>
</body>

</html>
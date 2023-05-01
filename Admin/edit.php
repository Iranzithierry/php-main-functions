<?php
require_once "../DB/conn.php";

$errorMsg = $successMsg = "";
$update = true;

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows <= 0) {
        $errorMsg = "User not found";
    }
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $fname = $row['fname'];
        $sname = $row['sname'];
        $email = $row['email'];
        $username = $row['username'];
        $code = $row['code'];
        $number = $row['mobile_number'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname_update = filter_var($_POST["fname"], FILTER_SANITIZE_STRING);
    $sname_update = filter_var($_POST["sname"], FILTER_SANITIZE_STRING);
    $mobile_number = filter_var($_POST["mobile_number"], FILTER_SANITIZE_NUMBER_INT);
    $email_update = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $username_update = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $code = filter_var($_POST["code"], FILTER_SANITIZE_NUMBER_INT);

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($stmt, "si", $email_update, $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $errorMsg = "User with email " . $email_update . " already exists.";
        $update = false;
    }

    if (!filter_var($email_update, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Please enter a valid email address.";
        $update = false;
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? AND id != ?");
    mysqli_stmt_bind_param($stmt, "si", $username_update, $id);
    mysqli_stmt_execute($stmt);
    $result_username = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result_username) > 0) {
        $errorMsg = "User with username " . $username_update . " already exists.";
        $update = false;
    }

    if (!preg_match("/^[a-zA-Z ]+$/", $fname_update)) {
        $errorMsg = "Name must contain alphabets and spaces.";
        $update = false;
    }

    if (!preg_match("/^[a-zA-Z ]+$/", $sname_update)) {
        $errorMsg = "Name must contain alphabets and spaces.";
        $update = false;
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE mobile_number = ? AND id != ?");
    mysqli_stmt_bind_param($stmt, "si", $mobile_number, $id);
    mysqli_stmt_execute($stmt);
    $result_number = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result_number) > 0) {
        $errorMsg = "User with this number " . $mobile_number . " already exists.";
        $update = false;
    }

    if (strlen(trim($_POST["mobile_number"])) < 10) {
        $errorMsg = "The number you entered is not valid. Please try again.";
        $update = false;
    }

    if ($update == true) {
        $stmt = $conn->prepare("UPDATE users SET fname = ?, sname = ?, mobile_number = ?, email = ?, username = ?, code = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $fname_update, $sname_update, $mobile_number, $email_update, $username_update, $code, $id);

        if ($stmt->execute()) {
            $successMsg = "User updated successfully!";
            header("refresh:3;url=admin.php");
        } else {
            $errorMsg = "Failed to update user";
        }
        $stmt->close();
    }
}
?>

<style>
    .wrapper {
        display: block;
        align-items: center;
        border-radius: 16px;
        width: fit-content;
        margin: 0 auto;
        border: 1px solid #000;
        padding: 16px;
        overflow: auto;
        box-shadow: 0 0 128px 0 rgba(0, 0, 0, 0.1), 0 32px 64px -48px rgba(0, 0, 0, 0.5);
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="../CSS/style-update-profile.css">

<div class="wrapper">
    <div class="container">
        <div class="form-fied">
            <div class="error-text" <?php if (empty($errorMsg)) echo "style='display:none;'" ?>>
                <span><?php echo $errorMsg ?></span>
            </div>
            <div class="success" <?php if (empty($successMsg)) echo "style='display:none;'" ?>>
                <span><?php echo $successMsg ?></span>
            </div>

            <form action="#" method="post">
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" class="form-control" id="inputfname" placeholder="First Name" name="fname" value="<?php echo $fname ?>">
                </div>

                <div class="form-group">
                    <label for="sname">Second Name</label>
                    <input type="text" class="form-control" id="inputsname" placeholder="Second Name" name="sname" value="<?php echo $sname ?>">
                </div>

                <div class="form-group">
                    <label for="number">Mobile Number</label>
                    <input type="number" class="form-control" id="inputnumber" placeholder="Number" name="mobile_number" value="<?php echo $number ?>">
                </div>

                <div class="form-group">
                    <label for="code">Code</label>
                    <input type="number" class="form-control" id="inputfname" placeholder="Code" name="code" value="<?php echo $code ?>">
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="inputEmail1" placeholder="Username" name="username" value="<?php echo $username ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="emailinput" placeholder="Password" name="email" value="<?php echo $email ?>">
                </div>

                <div class="form-group">
                    <button type="submit">Edit</button>
                </div>
            </form>
        </div>
        <?php ?>
    </div>
</div>

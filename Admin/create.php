<?php
include('../DB/conn.php');

session_start();
$fname = $sname = $number = $email = $username = $password = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number_of_users = $_POST["users"];
    $code = substr(str_shuffle("ABCDEFGHT123456789"), 0, 4);

    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (mysqli_num_rows($result) > 0) {
        $error = "This email address is already in use.";
    } 
    
    if (empty($_POST['fname'])) {
        $error = "First name is required";
    } else {
        $fname = $_POST['fname'];
    }
    
    if (empty($_POST['sname'])) {
        $error = "Last name is required";
    } else {
        $sname = $_POST['sname'];
    }
    
    if (empty($_POST['number'])) {
        $error = "Mobile number is required";
    } else {
        $number = $_POST['number'];
    }
    
    if (empty($_POST['username'])) {
        $error = "Username is required";
    } else {
        $username = $_POST['username'];
    }
    
    if (empty($_POST['password'])) {
        $error = "Password is required";
    } else {
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    if (empty($error)) {
        for ($i = 1; $i <= $number_of_users; $i++) {
            $code = substr(str_shuffle("ABCDEFGHT123456789"), 0, 4);
            if ($stmt = $conn->prepare("INSERT INTO users (fname, sname, mobile_number, email, username, password,code) VALUES (?, ?, ?, ?,?, ?, ?)")) {
                $stmt->bind_param("sssssss", $fname, $sname, $number, $email, $username, $hashed_password, $code);
                $stmt->execute();
                $stmt->close();
                echo "Successfully added user #" . $i . "<br>";
            } else {
                echo "Failed to add user #" . $i . "<br>";
            }
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
    <link rel="stylesheet" href="../CSS/style-create-users.css">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="content">
            <div class="error-text" <?php if (empty($error)) {
                                        echo "style='display:none;'";
                                    } ?>>
                <?php echo $error ?>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-group">
                    <!-- <select name="users" id="">
                        <option value="" selected hidden>Select User</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                    </select> -->
                    <input type="number" name="users">
                </div>
                <div class="user-form-container">
                    <div class="input-group">
                        <input type="text" name="fname" placeholder="All Users First Name">
                    </div>
                    <div class="input-group">
                        <input type="text" name="sname" placeholder="All Users Last Name">
                    </div>
                    <div class="input-group">
                        <input type="tel" name="number" placeholder="All Users  Mobile Number">
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" placeholder="All Users Email">
                    </div>
                    <div class="input-group">
                        <input type="text" name="username" placeholder="All Users Username">
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder="All Users Password">
                    </div>
                </div>
                <div class="button">
                    <button class="create-btn">Create</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
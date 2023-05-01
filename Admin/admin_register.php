<?php
include('../DB/conn.php');
include("header-forms.php");

$username = $password = "";
$error = "";
$success = "";

if (isset($_SESSION["admin_permission"])) {
    header("location: admin.php");
    exit;
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];
    $comfirm_password = $_POST["comfirm-password"];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("i", $username);
    $stmt->execute();
    $username_availability = $stmt->get_result();


    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    }
    else if(strlen($password)<8){
        $error = "Password must be atleast 8 characters";
    }
    else if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)){
        $error = "Username can only contain letters, numbers, and underscores";
    }
    else if($password !== $comfirm_password){
        $error = "Passwords do not match";
    }
    else if(mysqli_num_rows($username_availability) >0 ) {
        $error = "Username already exists";
    }
    else if(empty($error)){
       $stmt = $conn->prepare("INSERT INTO admin(username,password) VALUES(?,?)");
       $stmt->bind_param("ss", $username, $hashed_password);
       $stmt->execute();
       $stmt->close();

       session_start();

       $_SESSION["admin_permission"] =true;
       $_SESSION["username"] = $username;

       $success = "Login successful";
       header("refresh:3:url=admin.php");
    }
}
?>
<body>
    <div class="overlay"></div>
    <div class="container">
        <div class="wrapper">
        <div class="error-text" <?php if (empty($error)) echo "style='display:none;'" ?>>
                <span><?php echo $error ?></span>
            </div>
            <div class="success" <?php if (empty($success)) echo "style='display:none;'" ?>>
                <span><?php echo $success ?></span>
            </div>
            <form action="#" method="post">
                <div class="input-field">
                    <div class="form-field">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" placeholder="Username" name="username" autocomplete="none">
                    </div>
                </div>
                <div class="form-field">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" placeholder="password" name="password" autocomplete="none">
                </div>
                <div class="form-field">
                    <label for="password">Comfirm</label>
                    <input type="password" class="form-control" placeholder="Comfirm-password" name="comfirm-password" autocomplete="none">
                </div>
                <div class="button-area">
                    <button class="btn" type="submit">
                        Sign Up
                    </button>
                </div>
                <div class="redirect-link-area">
                    <a class="redirect-link-area" href="admin_login.php">Login</a>
                </div>
            </form>
        </div>
    </div>
    </div>
</body>
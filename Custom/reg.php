<?php
include('../DB/conn.php');
session_start();


$email = $password = "";
$error = "";
$success = "";

if (isset($_SESSION["admin_permission"])) {
    header("location: admin.php");
    exit;
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (empty(trim($_POST["username"]))) {
        $error = "Please enter your Username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $error = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Validate credentials
    if (empty($error) && empty($error)) {
        // Prepare a select statement
        $stmt = $conn->prepare("INSERT INTO admin(username,password) VALUES(?,?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $stmt->close();

        header("Location: form_admin.php");
    }
    // Close connection
    mysqli_close($conn);
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="CSS/font.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabnine</title>
</head>

<body>
    <div id="bg-artwork"></div>
    <div class="wrapper">
        <div class="content">
            <div class="logo-area">
                <img src="../IMAGES/user.png" alt="User">
            </div>
            <div class="error-text" <?php if (empty($error)) echo "style='display:none;'" ?>>
                <span><?php echo $error ?></span>
            </div>
            <div class="succes" <?php if (empty($success)) echo "style='display:none;'" ?>>
                <span><?php echo $success ?></span>
            </div>
            <div class="input-field">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="form">
                    <div class="input">
                        <label for="email">username</label><br>
                        <input type="text" name="username" id="input-field" placeholder="Enter Your Username">
                    </div>
                    <div class="input pwd">
                        <label for="Password">Password</label><br>
                        <input type="password" name="password" id="input-field" class="password-input" placeholder="Enter Your Password">
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
            </div>


            <button type="submit" class="login-button" id="register_btn">
                <span class="text">Register</span>
            </button>
            </form>


        </div>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.querySelector(".password-input");
            const toggleBtn = document.querySelector(".toggle-password");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleBtn.innerHTML = '<i class="fa fa-eye-slash"></i>';
            } else {
                passwordInput.type = "password";
                toggleBtn.innerHTML = '<i class="fa fa-eye"></i>';
            }
        }
    </script>
</body>
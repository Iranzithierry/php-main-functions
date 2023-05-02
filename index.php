<?php
include('DB/conn.php');


$email = $password = "";
$error = "";
$success = "";

if (isset($_SESSION["loggedIn"])) {
    header("location: home.php");
    exit;
}
if(isset($_COOKIE['emailid']) && isset($_COOKIE['password'])) {
    $emailid = $_COOKIE['emailid'];
    $pwdid = $_COOKIE['password'];
}else {
    $emailid ="";
    $pwdid ="";
}
// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $error = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $error = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($error) && empty($error)) {
        // Prepare a select statement
        $sql = "SELECT id, email, password FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $loggedIn, $email, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedIn"] = $loggedIn;
                            $_SESSION["email"] = $email;
                            if(isset($_POST["rememberMe"])) {
                                setcookie('emailid', $_POST["email"],time()+50);
                                setcookie('password', $_POST["password"],time()+50);
                            }else {
                                setcookie('emailid', $_POST["email"],time()+50);
                                setcookie('password', $_POST["password"],time()+50);
                            }
                            $sql = "SELECT banned_until FROM users WHERE id = ?";
                            if ($stmt = mysqli_prepare($conn, $sql)) {
                                mysqli_stmt_bind_param($stmt, "i", $loggedIn);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_bind_result($stmt, $banned_until);

                                if (mysqli_stmt_fetch($stmt)) {
                                    if ($banned_until !== null && $banned_until >= date('Y-m-d H:i:s')) {
                                        // User is banned
                                        header("location: Banned.php");
                                        exit;
                                    }
                                }
                            }


                            // Redirect user to timeline page
                            $loader =  "<svg class='pl' width='240' height='240' viewBox='0 0 240 240'>
                            <circle class='pl__ring pl__ring--a' cx='120' cy='120' r='105' fill='none' stroke='#000' stroke-width='20' stroke-dasharray='0 660' stroke-dashoffset='-330' stroke-linecap='round'></circle>
                            <circle class='pl__ring pl__ring--b' cx='120' cy='120' r='35' fill='none' stroke='#000' stroke-width='20' stroke-dasharray='0 220' stroke-dashoffset='-110' stroke-linecap='round'></circle>
                            <circle class='pl__ring pl__ring--c' cx='85' cy='120' r='70' fill='none' stroke='#000' stroke-width='20' stroke-dasharray='0 440' stroke-linecap='round'></circle>
                            <circle class='pl__ring pl__ring--d' cx='155' cy='120' r='70' fill='none' stroke='#000' stroke-width='20' stroke-dasharray='0 440' stroke-linecap='round'></circle>
                            </svg>";
                            header("refresh:5;url=home.php");
                        } else {
                            // Display an error message if password is not valid
                            $error = "The password you entered is not valid.";
                        }
                    }
                } else {
                    // Display an error message if email doesn't exist
                    $error = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Close connection
    mysqli_close($conn);
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="CSS/font.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="CSS/style.css">
    <link rel="stylesheet" href="/CSS/style-loader.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabnine</title>
</head>

<body>
    <div class="loader" <?php if (empty($loader)) echo "style='display:none;'" ?>>
          <?php echo $loader ?> 
        </div>
    <div class="wrapper">
        <div class="content">
            <div class="logo-area">
                <img src="IMAGES/user.png" alt="User">
            </div>
            <div class="error-text" <?php if (empty($error)) echo "style='display:none;'" ?>>
                <span><?php echo $error ?></span>
            </div>
            
            <div class="input-field">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="form">
                    <div class="input">
                        <label for="email">Email</label><br>
                        <input type="text" name="email" id="input-field" placeholder="Enter Your E-mail" value="<?php echo $emailid ?>">
                    </div>
                    <div class="input pwd">
                        <label for="Password">Password</label><br>
                        <input type="password" name="password" id="input-field" class="password-input" placeholder="Enter Your Password"value="<?php echo $pwdid ?>">
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    <span class="chechbox">
                            <input type="checkbox" name="rememberMe" id="rememberMe">
                            <label for="rememberMe">Remember Me</label>
                        </span>
                    <div class="forget-pwd">
                        <span class="forget"><a href="Custom/forgot-password.php">Forget Password?</a></span>
                    </div>
            </div>


            <button type="submit" class="login-button" id="register_btn">
                <span class="text">Login Now</span>
            </button>
            </form>
            <div class="lines">
                <div class="line"> </div>
                Sign With
                <div class="line"></div>
            </div>
            <div class="social-icons">
                <button aria-label="Log in with Google" class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-5 h-5 fill-current">
                        <path d="M16.318 13.714v5.484h9.078c-0.37 2.354-2.745 6.901-9.078 6.901-5.458 0-9.917-4.521-9.917-10.099s4.458-10.099 9.917-10.099c3.109 0 5.193 1.318 6.38 2.464l4.339-4.182c-2.786-2.599-6.396-4.182-10.719-4.182-8.844 0-16 7.151-16 16s7.156 16 16 16c9.234 0 15.365-6.49 15.365-15.635 0-1.052-0.115-1.854-0.255-2.651z"></path>
                    </svg>
                </button>
                <button aria-label="Log in with Twitter" class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-5 h-5 fill-current">
                        <path d="M31.937 6.093c-1.177 0.516-2.437 0.871-3.765 1.032 1.355-0.813 2.391-2.099 2.885-3.631-1.271 0.74-2.677 1.276-4.172 1.579-1.192-1.276-2.896-2.079-4.787-2.079-3.625 0-6.563 2.937-6.563 6.557 0 0.521 0.063 1.021 0.172 1.495-5.453-0.255-10.287-2.875-13.52-6.833-0.568 0.964-0.891 2.084-0.891 3.303 0 2.281 1.161 4.281 2.916 5.457-1.073-0.031-2.083-0.328-2.968-0.817v0.079c0 3.181 2.26 5.833 5.26 6.437-0.547 0.145-1.131 0.229-1.724 0.229-0.421 0-0.823-0.041-1.224-0.115 0.844 2.604 3.26 4.5 6.14 4.557-2.239 1.755-5.077 2.801-8.135 2.801-0.521 0-1.041-0.025-1.563-0.088 2.917 1.86 6.36 2.948 10.079 2.948 12.067 0 18.661-9.995 18.661-18.651 0-0.276 0-0.557-0.021-0.839 1.287-0.917 2.401-2.079 3.281-3.396z"></path>
                    </svg>
                </button>
                <button aria-label="Log in with GitHub" class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-5 h-5 fill-current">
                        <path d="M16 0.396c-8.839 0-16 7.167-16 16 0 7.073 4.584 13.068 10.937 15.183 0.803 0.151 1.093-0.344 1.093-0.772 0-0.38-0.009-1.385-0.015-2.719-4.453 0.964-5.391-2.151-5.391-2.151-0.729-1.844-1.781-2.339-1.781-2.339-1.448-0.989 0.115-0.968 0.115-0.968 1.604 0.109 2.448 1.645 2.448 1.645 1.427 2.448 3.744 1.74 4.661 1.328 0.14-1.031 0.557-1.74 1.011-2.135-3.552-0.401-7.287-1.776-7.287-7.907 0-1.751 0.62-3.177 1.645-4.297-0.177-0.401-0.719-2.031 0.141-4.235 0 0 1.339-0.427 4.4 1.641 1.281-0.355 2.641-0.532 4-0.541 1.36 0.009 2.719 0.187 4 0.541 3.043-2.068 4.381-1.641 4.381-1.641 0.859 2.204 0.317 3.833 0.161 4.235 1.015 1.12 1.635 2.547 1.635 4.297 0 6.145-3.74 7.5-7.296 7.891 0.556 0.479 1.077 1.464 1.077 2.959 0 2.14-0.020 3.864-0.020 4.385 0 0.416 0.28 0.916 1.104 0.755 6.4-2.093 10.979-8.093 10.979-15.156 0-8.833-7.161-16-16-16z"></path>
                    </svg>
                </button>
            </div>
            <footer class="footer">
                Create Account Now <a href="register.php">Sign Up</a>
            </footer>




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
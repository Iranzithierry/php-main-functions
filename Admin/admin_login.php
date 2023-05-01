<?php
include('../DB/conn.php');
include("header-forms.php");
session_start();


$username = $password = "";
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
    }

    // Validate credentials
    if (empty($error) && empty($error)) {
        // Prepare a select statement
        $sql = "SELECT username, password FROM admin WHERE username = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["admin_permission"] = true;
                            $_SESSION["username"] = $username;


                            // Redirect user to timeline page
                            header("location: admin.php");
                        } else {
                            // Display an error message if password is not valid
                            $error = "The password you entered is not valid.";
                        }
                    }
                } else {
                    // Display an error message if email doesn't exist
                    $error = "Username Not Valid.";
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
                <div class="button-area">
                    <button class="btn" type="submit">
                        Sign In
                    </button>
                </div>
                <div class="redirect-link-area">
                    <a class="redirect-link-area" href="admin_register.php">Register</a>
                </div>
            </form>
        </div>
    </div>
</body>
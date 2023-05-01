<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

include('DB/conn.php');

session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home.php");
    exit;
}


$fname = $sname = $number = $email = $username = $password = $confirm_password = "";
$error = "";
$succes = "";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $fname = $_POST["fname"];
    $sname = $_POST["sname"];
    $number = $_POST["number"];
    $username = $_POST["username"];
    $code = substr(str_shuffle("0123456789"), 0, 4);
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare a SELECT query to fetch the user with the given email
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    // Fetch the result set
    $result = mysqli_stmt_get_result($stmt);

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result_username = mysqli_stmt_get_result($stmt);

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE mobile_number = ?");
    mysqli_stmt_bind_param($stmt, "s", $number);
    mysqli_stmt_execute($stmt);

    $result_number = mysqli_stmt_get_result($stmt);

    // Check if the user already exists

    if(empty($fname && $fname && $number && $email && $username && $password && $confirm_password)){
        $error = "Please Fill All Input Fields";
    }
    else if (mysqli_num_rows($result) > 0) {
        $error = "User with email " . $email . " already exists.";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter valid email address.";
    }
    else if (mysqli_num_rows($result_username) > 0) {
        $error = "User with username " . $username . " already exists.";
    }
    else if (!preg_match("/^[a-zA-Z ]+$/", $fname)) {
        $error = "Name must contain alphabets and space.";
    }
    else if (!preg_match("/^[a-zA-Z ]+$/", $sname)) {
        $error = "Name must contain alphabets and space.";
    }
    else if (mysqli_num_rows($result_number) > 0) {
        $error = "User with This Number " . $number . " already exists.";
    }
    else if (strlen(trim($_POST["number"])) > 12) {
        $error = "The Number You entered Is Not Valid. Please try again.";
    }
    elseif (empty($password && $confirm_password)) {
        $error = "Please Fill All Passwor Field";
    }
    else if (strlen(trim($_POST["password"])) < 6) {
        $error = "Password Must Be The 6 characters";
    }
    else if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    }
    if (empty($error)) {
        // Prepare an INSERT query to add the user to the database
        $stmt = $conn->prepare("INSERT INTO users(email,password, fname, sname, mobile_number, username,code) VALUES (?, ?, ?, ?, ?,?,?)");
        $stmt->bind_param("sssssss", $email, $hashed_password, $fname, $sname, $number, $username, $code);
        $stmt->execute();
        $stmt->close();
        echo "<div style='display:none'>";

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'vpnzoe27@gmail.com';                     //SMTP username
            $mail->Password   = 'mfgkejjgmqiurcbv';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients    
            $mail->setFrom('tabnineincome@yahoo.com');
            $mail->addAddress($email);   //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'No Reply';
            $mail->Body    = 'Welcome ' . $fname . ' To Tabnine Income <br>Increase Income By Daily Task And Spin And Even <h1>Tabnine</h1> Was One Of The Most Successful Income Company That Pays Their Clients By Work They Have Done ';
            //  $mail->AltBody = ' ';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        echo "</div>";
        $succes =  "Welcome " . $fname . " To Tabnine";
        header("refresh:5;url=index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="build/css/intlTelInput.css">
    <link rel="stylesheet" href="CSS/font.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="CSS/style-register-old-version.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Tabnine</title>
</head>

<body>
    <div class="wrapper">
        <div class="content">
            <div class="logo-area">
                <img src="IMAGES/user.png" alt="User">
            </div>
            <div class="error-text" <?php if (empty($error)) echo "style='display:none;'" ?>>
                <span><?php echo $error ?></span>
            </div>
            <div class="succes" <?php if (empty($succes)) echo "style='display:none;'" ?>>
                <span><?php echo $succes ?></span>
            </div>
            <div class="input-field">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="form">
                    <div class="register-form">
                        <div class="input">
                            <span for="email">Fast Name</span><br>
                            <input type="text" name="fname" id="input-field" placeholder="Enter Your First Name" value="<?php echo htmlspecialchars($fname) ?>">
                        </div>
                        <div class="input">
                            <span for="email">Last Name</span><br>
                            <input type="text" name="sname" id="input-field" placeholder="Enter Your Last Name" value="<?php echo htmlspecialchars($sname) ?>">
                        </div>


                        <div class="input number">
                            <span for="number">Enter Yor Number</span>
                            <br>
                            <input type="tel" name="number" id="input-field" placeholder="Enter Valid Number" value="<?php echo htmlspecialchars($number) ?>">
                        </div>

                    </div>
                    <div class="form">
                        <div class="input">
                            <span for="email">Email</span><br>
                            <input type="text" name="email" id="input-field" placeholder="Enter Your E-mail" value="<?php echo htmlspecialchars($email) ?>">
                        </div>

                        <div class="input">
                            <span for="email">Username</span><br>
                            <input type="text" name="username" id="input-field" placeholder="Enter Your Username" value="<?php echo htmlspecialchars($username) ?>">
                        </div>
                    </div>
                    <div class="form">
                        <div class="input">
                            <span for="Password">Password</span><br>
                            <input type="password" name="password" id="input-field" placeholder="Enter Your Password">
                        </div>
                        <div class="input">
                            <span for="Password">Confirm Password</span><br>
                            <input type="password" name="confirm_password" id="input-field" placeholder="Enter Your Password Again">
                        </div>
                    </div>
                    <hr style="opacity: 0;">
                    <button type="submit" class="login-button" id="register_btn">
                        <span class="text">Create Account</span>
                    </button>

                </form>
                <div class="lines">
                    <div class="line"> </div>
                    Sign Up With
                    <div class="line"></div>
                </div>
                <div class="social-icons">
                    <button aria-span="Log in with Google" class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-5 h-5 fill-current">
                            <path d="M16.318 13.714v5.484h9.078c-0.37 2.354-2.745 6.901-9.078 6.901-5.458 0-9.917-4.521-9.917-10.099s4.458-10.099 9.917-10.099c3.109 0 5.193 1.318 6.38 2.464l4.339-4.182c-2.786-2.599-6.396-4.182-10.719-4.182-8.844 0-16 7.151-16 16s7.156 16 16 16c9.234 0 15.365-6.49 15.365-15.635 0-1.052-0.115-1.854-0.255-2.651z"></path>
                        </svg>
                    </button>
                    <button aria-span="Log in with Twitter" class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-5 h-5 fill-current">
                            <path d="M31.937 6.093c-1.177 0.516-2.437 0.871-3.765 1.032 1.355-0.813 2.391-2.099 2.885-3.631-1.271 0.74-2.677 1.276-4.172 1.579-1.192-1.276-2.896-2.079-4.787-2.079-3.625 0-6.563 2.937-6.563 6.557 0 0.521 0.063 1.021 0.172 1.495-5.453-0.255-10.287-2.875-13.52-6.833-0.568 0.964-0.891 2.084-0.891 3.303 0 2.281 1.161 4.281 2.916 5.457-1.073-0.031-2.083-0.328-2.968-0.817v0.079c0 3.181 2.26 5.833 5.26 6.437-0.547 0.145-1.131 0.229-1.724 0.229-0.421 0-0.823-0.041-1.224-0.115 0.844 2.604 3.26 4.5 6.14 4.557-2.239 1.755-5.077 2.801-8.135 2.801-0.521 0-1.041-0.025-1.563-0.088 2.917 1.86 6.36 2.948 10.079 2.948 12.067 0 18.661-9.995 18.661-18.651 0-0.276 0-0.557-0.021-0.839 1.287-0.917 2.401-2.079 3.281-3.396z"></path>
                        </svg>
                    </button>
                    <button aria-span="Log in with GitHub" class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-5 h-5 fill-current">
                            <path d="M16 0.396c-8.839 0-16 7.167-16 16 0 7.073 4.584 13.068 10.937 15.183 0.803 0.151 1.093-0.344 1.093-0.772 0-0.38-0.009-1.385-0.015-2.719-4.453 0.964-5.391-2.151-5.391-2.151-0.729-1.844-1.781-2.339-1.781-2.339-1.448-0.989 0.115-0.968 0.115-0.968 1.604 0.109 2.448 1.645 2.448 1.645 1.427 2.448 3.744 1.74 4.661 1.328 0.14-1.031 0.557-1.74 1.011-2.135-3.552-0.401-7.287-1.776-7.287-7.907 0-1.751 0.62-3.177 1.645-4.297-0.177-0.401-0.719-2.031 0.141-4.235 0 0 1.339-0.427 4.4 1.641 1.281-0.355 2.641-0.532 4-0.541 1.36 0.009 2.719 0.187 4 0.541 3.043-2.068 4.381-1.641 4.381-1.641 0.859 2.204 0.317 3.833 0.161 4.235 1.015 1.12 1.635 2.547 1.635 4.297 0 6.145-3.74 7.5-7.296 7.891 0.556 0.479 1.077 1.464 1.077 2.959 0 2.14-0.020 3.864-0.020 4.385 0 0.416 0.28 0.916 1.104 0.755 6.4-2.093 10.979-8.093 10.979-15.156 0-8.833-7.161-16-16-16z"></path>
                        </svg>
                    </button>
                </div>
                <div class="footer">
                    Already Have An Account? <a href="index.php">Sign In</a>
                </div>

            </div>


        </div>
    </div>
    <!-- <div class="input-module--input--JwDu3 local-search-module--search--gngFZ"><svg class="input-module--input-icon--3iwZR" version="1.1" viewBox="0 0 24 24">
            <path d="m10.8 1.7c-2.34 0-4.69 0.89-6.47 2.67-3.56 3.56-3.56 9.38 0 12.9 3.2 3.2 8.22 3.52 11.8 0.971l4.03 4.03 2.12-2.12-4.03-4.03c2.55-3.57 2.23-8.59-0.971-11.8-1.78-1.78-4.12-2.67-6.47-2.67zm0 2.98c1.57 0 3.14 0.602 4.35 1.81 2.41 2.41 2.41 6.28 0 8.69-2.41 2.41-6.28 2.41-8.69 0-2.41-2.41-2.41-6.28 0-8.69 1.21-1.21 2.78-1.81 4.35-1.81z"></path>
        </svg><input placeholder="Search documents" class="input-module--input-input--d+-ch" aria-label="Search Documents" value=""><button class="input-module--input-clear--WtwB1" aria-label="Clear" disabled=""><svg viewBox="0 0 24 24">
                <path d="m4.22 1.39-2.83 2.83 7.78 7.78-7.78 7.78 2.83 2.83 7.78-7.78 7.78 7.78 2.83-2.83-7.78-7.78 7.78-7.78-2.83-2.83-7.78 7.78-7.78-7.78z"></path>
            </svg></button></div> -->
    <!-- Utf -->
    <script>

    </script>
</body>

</html>
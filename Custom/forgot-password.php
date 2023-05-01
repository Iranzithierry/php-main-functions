<!-- /* Author Iranzi Thierry ©️2023 */ -->
<?php

$error = "";
$success = "";

include("../DB/conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $code = substr(str_shuffle("0123456789"), 0, 4);

    if (empty($email)) {
        $error = "Please enter Email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter valid email address.";
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

           

            $sender = "Tabnine Income";
            $message = $code;
            $subject =  "This Is Your OTP CODE";

            if (mail($email, $subject, $message, $sender)) {

                session_start();

                $stmt = $conn->prepare("UPDATE users SET code = ? WHERE email = ?");
                $stmt->bind_param("ss", $code, $email);
                $stmt->execute();


                $_SESSION["otp"] = $code;
                $_SESSION["email"] = $email;

                $success = "We Have Sent OTP CODE To'" . $email . "'";
                header("refresh:4;url=otp-code.php");
            } else {
                $error = "Failed while sending your mail!";
            }
        } else {
            $error = "Email Address You Provided is not Registered";
        }
    }
} ?>

<link rel="stylesheet" href="../CSS/style-update-profile.css" type="text/css">
<style>
    .success {
        color: #000;
        font-size: 16px;
        font-weight: bold;
        transition: cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background-color: #d4edda;
        border: 2px solid #70bb82;
        padding: 10px;
        padding-left: 30px;
        padding-right: 30px;
        border-radius: 8px;
        margin: 0 auto;
        width: fit-content;
        max-width: 300px;
        max-height: 100px;

    }
</style>
<div class="wrapper">
    <div class="content">
        <div class="error-area">
            <div class="error-text" <?php if (empty($error)) echo "style='display:none;'" ?>>
                <span><?php echo $error ?></span>
            </div>
            <div class="success" <?php if (empty($success)) echo "style='display:none;'" ?>>
                <span><?php echo $success ?></span>
            </div>

        </div>
        <div class="info" style="text-align: center; line-height: 25px;">
            <h3>Enter Your Registered E-Mail <br> To Get OTP Code To Reset Password</h3>
        </div>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-field">
                <input type="email" name="email" id="email" placeholder="Enter Your Registered E-Mail">
            </div>
            <div class="submit-area">
                <button type="submit">Send Now</button>
            </div>
        </form>
    </div>
</div>
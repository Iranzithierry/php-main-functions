<?php
session_start();
include("../DB/conn.php");
if(isset($_SESSION["otp"])) {

}else {
    header("Location: ../index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = "";
    $success = "";

    $otp = $_POST["otp"];
    $email = $_SESSION["email"];
    $code = $_SESSION["otp"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND code = ?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();

    $result = $stmt->get_result();

    if (mysqli_num_rows($result) > 0) {
        $_SESSION["reset"] = "true";
        // OTP is correct, redirect the user to the reset password page
        $success = "OTP VERIFIED";
        header("refresh:5;url=reset-password.php");
    } else {
        $error = "Invalid OTP, please try again.";
    }
}
?>


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style-otp.css">
    <link rel="stylesheet" href="../CSS/style-spinner.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <script src="script.js" defer></script>
    <title>OTP</title>
    <div class="container">
        <header>
            <i class="bx bxs-check-shield"></i>
        </header>
        <div class="error-text" <?php if (empty($error)) echo "style='display:none;'" ?>>
            <span><?php echo $error ?></span>
        </div>
        <div class="success" <?php if (empty($success)) echo "style='display:none;'" ?>>
        <div class="spinner center">
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
                <div class="spinner-blade"></div>
            </div>
            <span><?php echo $success ?></span>
           
        </div>
        <h4>Enter OTP Code</h4>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-field">
                <input type="number" name="otp" id="waguan">
            </div>
            <button type="submit">Verify OTP</button>
        </form>

    </div>
</head>

<body>
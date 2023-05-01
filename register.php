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

    if (empty($fname && $fname && $number && $email && $username && $password && $confirm_password)) {
        $error = "Please Fill All Input Fields";
    } else if (mysqli_num_rows($result) > 0) {
        $error = "User with email " . $email . " already exists.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter valid email address.";
    }

    // else if (!preg_match('/^(\+?250|0)?([78])(\d{7})$/', $number)) {
    //     $error = "Please enter a valid mobile number.";
    // }
    else if (mysqli_num_rows($result_username) > 0) {
        $error = "User with username " . $username . " already exists.";
    } else if (!preg_match("/^[a-zA-Z ]+$/", $fname)) {
        $error = "Name must contain alphabets and space.";
    } else if (!preg_match("/^[a-zA-Z ]+$/", $sname)) {
        $error = "Name must contain alphabets and space.";
    } else if (mysqli_num_rows($result_number) > 0) {
        $error = "User with This Number " . $number . " already exists.";
    } else if (strlen(trim($_POST["number"])) > 12) {
        $error = "The Number You entered Is Not Valid. Please try again.";
    } elseif (empty($password && $confirm_password)) {
        $error = "Please Fill All Passwor Field";
    } else if (strlen(trim($_POST["password"])) < 6) {
        $error = "Password Must Be The 6 characters";
    }
    else if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    }    
    else if (isset($_POST['selected_country'])) {
         $country = $_POST["selected_country"];
    }
    else {
        $error = "Please select a country";
    }
    if (empty($error)) {
        // Prepare an INSERT query to add the user to the database
        $stmt = $conn->prepare("INSERT INTO users(email,password, fname, sname, mobile_number, username,country,code) VALUES (?,?, ?, ?, ?, ?,?,?)");
        $stmt->bind_param("ssssssss", $email, $hashed_password, $fname, $sname, $number, $username,$country, $code);
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
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/CSS/style-register.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/fontawesome.min.css" integrity="sha384-jLKHWM3JRmfMU0A5x5AkjWkw/EYfGUAGagvnfryNV3F9VqM98XiIH7VBGVoxVSc7" crossorigin="anonymous">

    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="error-text" <?php if (empty($error)) echo "style='display:none;'" ?>>
            <span><?php echo $error ?></span>
        </div>
        <div class="success" <?php if (empty($succes)) echo "style='display:none;'" ?>>
            <span><?php echo $succes ?></span>
        </div>
        <form action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="columns">
                <div class="col_1">

                    <div class="col_1_form_group">
                        <div class="welcome_message">
                            <div class="slider">
                                <div class="slide-track">
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/1.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/2.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/3.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/4.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/5.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/6.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/7.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/1.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/2.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/3.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/4.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/5.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/6.png" height="100" width="250" alt="" />
                                    </div>
                                    <div class="slide">
                                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/557257/7.png" height="100" width="250" alt="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="username">First Name</label>
                            <input type="text" class="form-control" id="fname" name="fname" placeholder="Enter Your First Name" value="<?php echo htmlspecialchars($fname) ?> "/>
                        </div>
                        <div class="form-group">
                            <label for="sname">Last Name</label>
                            <input type="text" class="form-control" name="sname" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($sname) ?>"/>
                        </div>
                        <div class="form-group">
                            <label for="number">Mobile Number</label>
                            <input type="tel" class="form-control" name="number" placeholder="Enter Your Mobile Number" value="<?php echo htmlspecialchars($number) ?>"/>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter Your Email" value="<?php echo htmlspecialchars($email) ?>"/>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Create Username" value="<?php echo htmlspecialchars($username) ?>"/>
                        </div>
                    </div>
                </div>
                <div class="col_2">
                    <div class="img-group">
                        <img src="IMAGES/user.png" alt="user.icon" />
                    </div>
                    <div class="col_2_form_group">
                        <!-- <div class="radios">
                            <label>
                                <input type="radio" name="gender" value="male" class="radio" checked>
                                Male
                            </label>
                            <label>
                                <input type="radio" name="gender" value="female" class="radio">
                                Female
                            </label>

                        </div> -->
                        <div class="form-group">
                            <label for="selected_country">Select Country</label>
                            <select name="selected_country" class="form-control" id="country">
                            <option value="0" label="Select a country ... " selected="selected" disabled hidden>Select a country ... </option>
                                <!-- AFRICA -->
                                <option value="DZ" label="Algeria">Algeria</option>
                                <option value="AO" label="Angola">Angola</option>
                                <option value="BJ" label="Benin">Benin</option>
                                <option value="BW" label="Botswana">Botswana</option>
                                <option value="BF" label="Burkina Faso">Burkina Faso</option>
                                <option value="BI" label="Burundi">Burundi</option>
                                <option value="CM" label="Cameroon">Cameroon</option>
                                <option value="CV" label="Cape Verde">Cape Verde</option>
                                <option value="CF" label="Central African Republic">Central African Republic</option>
                                <option value="TD" label="Chad">Chad</option>
                                <option value="KM" label="Comoros">Comoros</option>
                                <option value="CG" label="Congo - Brazzaville">Congo - Brazzaville</option>
                                <option value="CD" label="Congo - Kinshasa">Congo - Kinshasa</option>
                                <option value="CI" label="Côte d’Ivoire">Côte d’Ivoire</option>
                                <option value="DJ" label="Djibouti">Djibouti</option>
                                <option value="EG" label="Egypt">Egypt</option>
                                <option value="GQ" label="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="ER" label="Eritrea">Eritrea</option>
                                <option value="ET" label="Ethiopia">Ethiopia</option>
                                <option value="GA" label="Gabon">Gabon</option>
                                <option value="GM" label="Gambia">Gambia</option>
                                <option value="GH" label="Ghana">Ghana</option>
                                <option value="GN" label="Guinea">Guinea</option>
                                <option value="GW" label="Guinea-Bissau">Guinea-Bissau</option>
                                <option value="KE" label="Kenya">Kenya</option>
                                <option value="LS" label="Lesotho">Lesotho</option>
                                <option value="LR" label="Liberia">Liberia</option>
                                <option value="LY" label="Libya">Libya</option>
                                <option value="MG" label="Madagascar">Madagascar</option>
                                <option value="MW" label="Malawi">Malawi</option>
                                <option value="ML" label="Mali">Mali</option>
                                <option value="MR" label="Mauritania">Mauritania</option>
                                <option value="MU" label="Mauritius">Mauritius</option>
                                <option value="YT" label="Mayotte">Mayotte</option>
                                <option value="MA" label="Morocco">Morocco</option>
                                <option value="MZ" label="Mozambique">Mozambique</option>
                                <option value="NA" label="Namibia">Namibia</option>
                                <option value="NE" label="Niger">Niger</option>
                                <option value="NG" label="Nigeria">Nigeria</option>
                                <option value="RW" label="Rwanda" id="rwanda">Rwanda</option>
                                <option value="RE" label="Réunion">Réunion</option>
                                <option value="SH" label="Saint Helena">Saint Helena</option>
                                <option value="SN" label="Senegal">Senegal</option>
                                <option value="SC" label="Seychelles">Seychelles</option>
                                <option value="SL" label="Sierra Leone">Sierra Leone</option>
                                <option value="SO" label="Somalia">Somalia</option>
                                <option value="ZA" label="South Africa">South Africa</option>
                                <option value="SD" label="Sudan">Sudan</option>
                                <option value="SZ" label="Swaziland">Swaziland</option>
                                <option value="ST" label="São Tomé and Príncipe">São Tomé and Príncipe</option>
                                <option value="TZ" label="Tanzania">Tanzania</option>
                                <option value="TG" label="Togo">Togo</option>
                                <option value="TN" label="Tunisia">Tunisia</option>
                                <option value="UG" label="Uganda">Uganda</option>
                                <option value="EH" label="Western Sahara">Western Sahara</option>
                                <option value="ZM" label="Zambia">Zambia</option>
                                <option value="ZW" label="Zimbabwe">Zimbabwe</option>
                          
                                <!-- AMERICA -->
                                <option value="AI" label="Anguilla">Anguilla</option>
                                <option value="AG" label="Antigua and Barbuda">Antigua and Barbuda</option>
                                <option value="AR" label="Argentina">Argentina</option>
                                <option value="AW" label="Aruba">Aruba</option>
                                <option value="BS" label="Bahamas">Bahamas</option>
                                <option value="BB" label="Barbados">Barbados</option>
                                <option value="BZ" label="Belize">Belize</option>
                                <option value="BM" label="Bermuda">Bermuda</option>
                                <option value="BO" label="Bolivia">Bolivia</option>
                                <option value="BR" label="Brazil">Brazil</option>
                                <option value="VG" label="British Virgin Islands">British Virgin Islands</option>
                                <option value="CA" label="Canada">Canada</option>
                                <option value="KY" label="Cayman Islands">Cayman Islands</option>
                                <option value="CL" label="Chile">Chile</option>
                                <option value="CO" label="Colombia">Colombia</option>
                                <option value="CR" label="Costa Rica">Costa Rica</option>
                                <option value="CU" label="Cuba">Cuba</option>
                                <option value="DM" label="Dominica">Dominica</option>
                                <option value="DO" label="Dominican Republic">Dominican Republic</option>
                                <option value="EC" label="Ecuador">Ecuador</option>
                                <option value="SV" label="El Salvador">El Salvador</option>
                                <option value="FK" label="Falkland Islands">Falkland Islands</option>
                                <option value="GF" label="French Guiana">French Guiana</option>
                                <option value="GL" label="Greenland">Greenland</option>
                                <option value="GD" label="Grenada">Grenada</option>
                                <option value="GP" label="Guadeloupe">Guadeloupe</option>
                                <option value="GT" label="Guatemala">Guatemala</option>
                                <option value="GY" label="Guyana">Guyana</option>
                                <option value="HT" label="Haiti">Haiti</option>
                                <option value="HN" label="Honduras">Honduras</option>
                                <option value="JM" label="Jamaica">Jamaica</option>
                                <option value="MQ" label="Martinique">Martinique</option>
                                <option value="MX" label="Mexico">Mexico</option>
                                <option value="MS" label="Montserrat">Montserrat</option>
                                <option value="AN" label="Netherlands Antilles">Netherlands Antilles</option>
                                <option value="NI" label="Nicaragua">Nicaragua</option>
                                <option value="PA" label="Panama">Panama</option>
                                <option value="PY" label="Paraguay">Paraguay</option>
                                <option value="PE" label="Peru">Peru</option>
                                <option value="PR" label="Puerto Rico">Puerto Rico</option>
                                <option value="BL" label="Saint Barthélemy">Saint Barthélemy</option>
                                <option value="KN" label="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                <option value="LC" label="Saint Lucia">Saint Lucia</option>
                                <option value="MF" label="Saint Martin">Saint Martin</option>
                                <option value="PM" label="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                <option value="VC" label="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                <option value="SR" label="Suriname">Suriname</option>
                                <option value="TT" label="Trinidad and Tobago">Trinidad and Tobago</option>
                                <option value="TC" label="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                <option value="VI" label="U.S. Virgin Islands">U.S. Virgin Islands</option>
                                <option value="US" label="United States">United States</option>
                                <option value="UY" label="Uruguay">Uruguay</option>
                                <option value="VE" label="Venezuela">Venezuela</option>
                        
                                <!-- ASIA -->
                                <option value="AF" label="Afghanistan">Afghanistan</option>
                                <option value="AM" label="Armenia">Armenia</option>
                                <option value="AZ" label="Azerbaijan">Azerbaijan</option>
                                <option value="BH" label="Bahrain">Bahrain</option>
                                <option value="BD" label="Bangladesh">Bangladesh</option>
                                <option value="BT" label="Bhutan">Bhutan</option>
                                <option value="BN" label="Brunei">Brunei</option>
                                <option value="KH" label="Cambodia">Cambodia</option>
                                <option value="CN" label="China">China</option>
                                <option value="GE" label="Georgia">Georgia</option>
                                <option value="HK" label="Hong Kong SAR China">Hong Kong SAR China</option>
                                <option value="IN" label="India">India</option>
                                <option value="ID" label="Indonesia">Indonesia</option>
                                <option value="IR" label="Iran">Iran</option>
                                <option value="IQ" label="Iraq">Iraq</option>
                                <option value="IL" label="Israel">Israel</option>
                                <option value="JP" label="Japan">Japan</option>
                                <option value="JO" label="Jordan">Jordan</option>
                                <option value="KZ" label="Kazakhstan">Kazakhstan</option>
                                <option value="KW" label="Kuwait">Kuwait</option>
                                <option value="KG" label="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="LA" label="Laos">Laos</option>
                                <option value="LB" label="Lebanon">Lebanon</option>
                                <option value="MO" label="Macau SAR China">Macau SAR China</option>
                                <option value="MY" label="Malaysia">Malaysia</option>
                                <option value="MV" label="Maldives">Maldives</option>
                                <option value="MN" label="Mongolia">Mongolia</option>
                                <option value="MM" label="Myanmar [Burma]">Myanmar [Burma]</option>
                                <option value="NP" label="Nepal">Nepal</option>
                                <option value="NT" label="Neutral Zone">Neutral Zone</option>
                                <option value="KP" label="North Korea">North Korea</option>
                                <option value="OM" label="Oman">Oman</option>
                                <option value="PK" label="Pakistan">Pakistan</option>
                                <option value="PS" label="Palestinian Territories">Palestinian Territories</option>
                                <option value="YD" label="People's Democratic Republic of Yemen">People's Democratic Republic of Yemen</option>
                                <option value="PH" label="Philippines">Philippines</option>
                                <option value="QA" label="Qatar">Qatar</option>
                                <option value="SA" label="Saudi Arabia">Saudi Arabia</option>
                                <option value="SG" label="Singapore">Singapore</option>
                                <option value="KR" label="South Korea">South Korea</option>
                                <option value="LK" label="Sri Lanka">Sri Lanka</option>
                                <option value="SY" label="Syria">Syria</option>
                                <option value="TW" label="Taiwan">Taiwan</option>
                                <option value="TJ" label="Tajikistan">Tajikistan</option>
                                <option value="TH" label="Thailand">Thailand</option>
                                <option value="TL" label="Timor-Leste">Timor-Leste</option>
                                <option value="TR" label="Turkey">Turkey</option>
                                <option value="TM" label="Turkmenistan">Turkmenistan</option>
                                <option value="AE" label="United Arab Emirates">United Arab Emirates</option>
                                <option value="UZ" label="Uzbekistan">Uzbekistan</option>
                                <option value="VN" label="Vietnam">Vietnam</option>
                                <option value="YE" label="Yemen">Yemen</option>
                               <!-- EUROPE-->
                                <option value="AL" label="Albania">Albania</option>
                                <option value="AD" label="Andorra">Andorra</option>
                                <option value="AT" label="Austria">Austria</option>
                                <option value="BY" label="Belarus">Belarus</option>
                                <option value="BE" label="Belgium">Belgium</option>
                                <option value="BA" label="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                <option value="BG" label="Bulgaria">Bulgaria</option>
                                <option value="HR" label="Croatia">Croatia</option>
                                <option value="CY" label="Cyprus">Cyprus</option>
                                <option value="CZ" label="Czech Republic">Czech Republic</option>
                                <option value="DK" label="Denmark">Denmark</option>
                                <option value="DD" label="East Germany">East Germany</option>
                                <option value="EE" label="Estonia">Estonia</option>
                                <option value="FO" label="Faroe Islands">Faroe Islands</option>
                                <option value="FI" label="Finland">Finland</option>
                                <option value="FR" label="France">France</option>
                                <option value="DE" label="Germany">Germany</option>
                                <option value="GI" label="Gibraltar">Gibraltar</option>
                                <option value="GR" label="Greece">Greece</option>
                                <option value="GG" label="Guernsey">Guernsey</option>
                                <option value="HU" label="Hungary">Hungary</option>
                                <option value="IS" label="Iceland">Iceland</option>
                                <option value="IE" label="Ireland">Ireland</option>
                                <option value="IM" label="Isle of Man">Isle of Man</option>
                                <option value="IT" label="Italy">Italy</option>
                                <option value="JE" label="Jersey">Jersey</option>
                                <option value="LV" label="Latvia">Latvia</option>
                                <option value="LI" label="Liechtenstein">Liechtenstein</option>
                                <option value="LT" label="Lithuania">Lithuania</option>
                                <option value="LU" label="Luxembourg">Luxembourg</option>
                                <option value="MK" label="Macedonia">Macedonia</option>
                                <option value="MT" label="Malta">Malta</option>
                                <option value="FX" label="Metropolitan France">Metropolitan France</option>
                                <option value="MD" label="Moldova">Moldova</option>
                                <option value="MC" label="Monaco">Monaco</option>
                                <option value="ME" label="Montenegro">Montenegro</option>
                                <option value="NL" label="Netherlands">Netherlands</option>
                                <option value="NO" label="Norway">Norway</option>
                                <option value="PL" label="Poland">Poland</option>
                                <option value="PT" label="Portugal">Portugal</option>
                                <option value="RO" label="Romania">Romania</option>
                                <option value="RU" label="Russia">Russia</option>
                                <option value="SM" label="San Marino">San Marino</option>
                                <option value="RS" label="Serbia">Serbia</option>
                                <option value="CS" label="Serbia and Montenegro">Serbia and Montenegro</option>
                                <option value="SK" label="Slovakia">Slovakia</option>
                                <option value="SI" label="Slovenia">Slovenia</option>
                                <option value="ES" label="Spain">Spain</option>
                                <option value="SJ" label="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                <option value="SE" label="Sweden">Sweden</option>
                                <option value="CH" label="Switzerland">Switzerland</option>
                                <option value="UA" label="Ukraine">Ukraine</option>
                                <option value="SU" label="Union of Soviet Socialist Republics">Union of Soviet Socialist Republics</option>
                                <option value="GB" label="United Kingdom">United Kingdom</option>
                                <option value="VA" label="Vatican City">Vatican City</option>
                                <option value="AX" label="Åland Islands">Åland Islands</option>
                           
                            
                        </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Create Strong Password" value="<?php echo htmlspecialchars($password) ?>" />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" value="<?php echo htmlspecialchars($confirm_password) ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="login-button" id="register_btn">
                <span class="text">Create Account</span>
            </button>
        </form>
        <div class="lines">
            <div class="line"></div>
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
    <script src="SCRIPT/script.js"></script>
</body>

</html>
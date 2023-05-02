<?php
session_start();
include("DB/conn.php");
if (isset($_SESSION['loggedIn']) && $_SESSION['not_banned'] = true) {
} else {
  header("location: index.php");
}
$sql = "SELECT banned_until FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
  mysqli_stmt_bind_param($stmt, "i", $loggedIn);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $banned_until);

  if (mysqli_stmt_fetch($stmt)) {
    if ($banned_until != null && $banned_until > date("Y-m-d H:i:s")) {
      header("Location: banned.php");
      exit();
    }
  }

  mysqli_stmt_close($stmt);
}
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['loggedIn']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows <= 0) {
  header("Location: logout.php");
}
$user = $result->fetch_assoc();

// Display user information

// Add more fields as needed
// echo '<h1> Welcome ' .$_SESSION['email'].'</h1>';
// echo ' this is your mobile number '.$user['mobile_number'] . '';
// $test = str_replace("john","Muakarujaga",$user);
// echo $test;

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get user ID from session
  $user_id = $_SESSION['loggedIn'];

  // Get form data
  $answer_1 = $_POST['answer_1'];
  $answer_2 = $_POST['answer_2'];
  $answer_3 = $_POST['answer_3'];

  // Calculate result based on answers
  $result = 'Result based on answers'; // Replace with your actual code to calculate the result

  // Check if connection was successful
  if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
  }

  // Insert result into user_results table
  $sql = "INSERT INTO `user_results` (`user_id`, `question_1`, `question_2`, `question_3`, `result`)
          VALUES ('$user_id', '$answer_1', '$answer_2', '$answer_3', '$result')";
  if (mysqli_query($conn, $sql)) {
    // Redirect to results page
    header('Location: Custom/results.php');
    exit();
  } else {
    echo 'Error: ' . $sql . '<br>' . mysqli_error($conn);
  }

  // Close database connection
  mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="style-home.css">
  <link rel="stylesheet" href="FONT/font.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <div class="container">
    <div class="side_nav">
      <nav>
        <div class="logo_area">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_129_9347)">
              <path d="M19.5386 23.4366C19.3904 23.5849 19.2144 23.7025 19.0208 23.7828C18.8271 23.863 18.6195 23.9043 18.4099 23.9043C18.2003 23.9043 17.9927 23.863 17.799 23.7828C17.6053 23.7025 17.4294 23.5849 17.2812 23.4366L8.09921 14.2561C6.77491 12.9048 6.03741 11.0854 6.04697 9.19342C6.05652 7.30139 6.81236 5.48959 8.15024 4.15171C9.48812 2.81383 11.2999 2.05799 13.192 2.04843C15.084 2.03888 16.9033 2.77638 18.2547 4.10067L24.0578 9.90378C24.3563 10.2039 24.5235 10.6103 24.5224 11.0337C24.5213 11.457 24.3521 11.8626 24.052 12.1612C23.7518 12.4598 23.3454 12.6269 22.9221 12.6258C22.4987 12.6247 22.0931 12.4555 21.7945 12.1554L15.9987 6.35228C15.2499 5.60352 14.2344 5.18287 13.1755 5.18287C12.1166 5.18287 11.101 5.60352 10.3523 6.35228C9.6035 7.10104 9.18285 8.11658 9.18285 9.17549C9.18285 10.2344 9.6035 11.2499 10.3523 11.9987L19.5342 21.1807C19.8338 21.4793 20.0026 21.8847 20.0034 22.3078C20.0042 22.7308 19.837 23.1368 19.5386 23.4366Z" fill="#7C5CFC"></path>
              <path d="M24.0012 27.8992C22.6533 29.2436 20.8272 29.9987 18.9234 29.9987C17.0197 29.9987 15.1936 29.2436 13.8457 27.8992L8.04986 22.0961C7.88902 21.951 7.75941 21.7747 7.66891 21.5779C7.57841 21.3811 7.52892 21.1679 7.52346 20.9514C7.51799 20.7348 7.55667 20.5194 7.63713 20.3183C7.71758 20.1172 7.83814 19.9346 7.99145 19.7816C8.14477 19.6286 8.32762 19.5084 8.52889 19.4283C8.73015 19.3482 8.94561 19.31 9.16214 19.3158C9.37867 19.3217 9.59173 19.3716 9.78835 19.4625C9.98497 19.5534 10.161 19.6833 10.3058 19.8445L16.1017 25.6476C16.8516 26.3875 17.8637 26.8008 18.9172 26.7972C19.9707 26.7937 20.98 26.3737 21.7249 25.6287C22.4698 24.8838 22.8899 23.8745 22.8934 22.821C22.8969 21.7675 22.4837 20.7554 21.7437 20.0055L12.5618 10.8192C12.2624 10.5198 12.0942 10.1138 12.0942 9.69047C12.0942 9.26712 12.2624 8.86111 12.5618 8.56176C12.8611 8.26241 13.2671 8.09424 13.6905 8.09424C14.1138 8.09424 14.5198 8.26241 14.8192 8.56176L24.0012 17.7437C25.346 19.0914 26.1013 20.9176 26.1013 22.8215C26.1013 24.7253 25.346 26.5515 24.0012 27.8992Z" fill="#7C5CFC"></path>
            </g>
            <defs>
              <clipPath id="clip0_129_9347">
                <rect width="20.1005" height="28" fill="currentColor" transform="translate(6 2)"></rect>
              </clipPath>
            </defs>
          </svg>
          <h3>THIERRY.Inc</h3>
        </div>
        <ul>
          <li class="nav_item"><i class="fa-solid fa-house"></i>&nbsp;HOME</li>
          <li class="nav_item"><i class="fa-solid fa-user"></i>&nbsp;ACCOUNT</li>
          <li class="nav_item"><i class="fa-solid fa-money-bill-trend-up"></i>&nbsp;EARNINGS</li>
          <li class="nav_item"><svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.1668 9.66671V14.25C20.1668 18.8334 18.3335 20.6667 13.7502 20.6667H8.25016C3.66683 20.6667 1.8335 18.8334 1.8335 14.25V8.75004C1.8335 4.16671 3.66683 2.33337 8.25016 2.33337H12.8335" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M20.1668 9.66671H16.5002C13.7502 9.66671 12.8335 8.75004 12.8335 6.00004V2.33337L20.1668 9.66671Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M6.4165 12.4166H11.9165" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M6.4165 16.0834H10.0832" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>&nbsp;SURVEYS</li>
          <li class="nav_item"><svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2.2915 12.6367V11.0509C2.2915 9.15336 3.84067 7.60419 5.73817 7.60419H16.2615C18.159 7.60419 19.7082 9.15336 19.7082 11.0509V12.3709H17.8565C17.3432 12.3709 16.8757 12.5725 16.5365 12.9208C16.1515 13.2967 15.9315 13.8375 15.9865 14.415C16.069 15.405 16.9765 16.1292 17.9665 16.1292H19.7082V17.22C19.7082 19.1175 18.159 20.6667 16.2615 20.6667H11.2382" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M2.2915 11.8758V7.6867C2.2915 6.59587 2.96067 5.62416 3.97817 5.23916L11.2565 2.48916C12.3932 2.05833 13.6123 2.90169 13.6123 4.12085V7.60418" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M20.6787 13.306V15.1944C20.6787 15.6985 20.2754 16.111 19.7621 16.1293H17.9654C16.9754 16.1293 16.0679 15.4052 15.9854 14.4152C15.9304 13.8377 16.1504 13.2968 16.5354 12.921C16.8746 12.5727 17.3421 12.371 17.8554 12.371H19.7621C20.2754 12.3894 20.6787 12.8018 20.6787 13.306Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M6.4165 11.5H12.8332" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M2.75 15.625H7.645C8.23166 15.625 8.70833 16.1016 8.70833 16.6883V17.8617" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M3.86833 14.5067L2.75 15.625L3.86833 16.7433" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M8.70833 20.4651H3.81334C3.22667 20.4651 2.75 19.9884 2.75 19.4017V18.2284" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M7.59131 21.5837L8.70964 20.4653L7.59131 19.347" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>&nbsp;WITHDRAWAL</li>
          <li class="nav_item"><svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6.30664 17.1375V15.24" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
              <path d="M11 17.1375V13.3425" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
              <path d="M15.6934 17.1375V11.4358" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
              <path d="M15.6933 5.86255L15.2716 6.35755C12.9341 9.08922 9.79914 11.0234 6.30664 11.8942" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
              <path d="M13.0073 5.86255H15.6932V8.53922" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M8.25016 20.6667H13.7502C18.3335 20.6667 20.1668 18.8334 20.1668 14.25V8.75004C20.1668 4.16671 18.3335 2.33337 13.7502 2.33337H8.25016C3.66683 2.33337 1.8335 4.16671 1.8335 8.75004V14.25C1.8335 18.8334 3.66683 20.6667 8.25016 20.6667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>&nbsp;DAILY TASKS</li>
          <li class="nav_item"><svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2.75 8.85081V14.14C2.75 16.0833 2.75 16.0833 4.58333 17.3208L9.625 20.2358C10.3858 20.6758 11.6233 20.6758 12.375 20.2358L17.4167 17.3208C19.25 16.0833 19.25 16.0833 19.25 14.1491V8.85081C19.25 6.91664 19.25 6.91664 17.4167 5.67914L12.375 2.76414C11.6233 2.32414 10.3858 2.32414 9.625 2.76414L4.58333 5.67914C2.75 6.91664 2.75 6.91664 2.75 8.85081Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M11 14.25C12.5188 14.25 13.75 13.0188 13.75 11.5C13.75 9.98122 12.5188 8.75 11 8.75C9.48122 8.75 8.25 9.98122 8.25 11.5C8.25 13.0188 9.48122 14.25 11 14.25Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>&nbsp;SETTING</li>

         <a href="logout.php"> <li class="nav_item"><svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8.8999 8.05999C9.2099 4.45999 11.0599 2.98999 15.1099 2.98999H15.2399C19.7099 2.98999 21.4999 4.77999 21.4999 9.24999V15.77C21.4999 20.24 19.7099 22.03 15.2399 22.03H15.1099C11.0899 22.03 9.2399 20.58 8.9099 17.04" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M15.0001 12.5H3.62012" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M5.85 9.14999L2.5 12.5L5.85 15.85" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>&nbsp;LOGOUT</li></a>
        </ul>
      </nav>
    </div>
    <div class="body_content">
      <div class="header_content">
        <div class="input_bar">
          <div class="search">
            <input types="text" class="search__input" placeholder="Type your text">
            <div class="search__button">
              <svg class="search__icon" aria-hidden="true" viewBox="0 0 24 24">
                <g>
                  <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                </g>
              </svg>
            </div>
          </div>

        </div>
      </div>
      <div id="demo">
      </div>
      <button onclick="getLocation()">Try It</button>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.1/chart.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const nav_items = document.querySelectorAll('.nav_item');

    nav_items.forEach(nav_item => {
      nav_item.addEventListener('click', (event) => {
        if (event.target.classList.contains('active')) {
          event.target.classList.remove('active');
        } else {
          nav_items.forEach(item => item.classList.remove('active'));
          event.target.classList.add('active');
        }
      });
    });
  </script>
  <?php echo $user["fname"]; ?>
 
  <script>
var x = document.getElementById("demo");
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else {
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
  x.innerHTML = "Latitude: " + position.coords.latitude +
  "<br>Longitude: " + position.coords.longitude;
}
</script>

</body>

</html>
<?php
session_start();
include("DB/conn.php");

$error = "";
// Retrieve the user's information from the database using their ID or email
$stmt = $conn->prepare("SELECT banned_until , fname FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['loggedIn']);

if ($stmt->execute()) {
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  // Check if the user is banned
  if ($user && $user['banned_until'] > time()) {
    // The user is banned until a specific time
    $banned_until = date('Y-m-d H:i:s', strtotime($user['banned_until']));
    $fname = $user['fname'];
    $error = "Hello <b>".$fname."</b> &nbsp;Your account has been banned until {$banned_until}. Please contact the administrator for more information.";
    header("refresh:10;url=logout.php");
    session_unset();
    session_destroy();

   
    
  } else {
    $_SESSION['not_banned'] = true;
    $error ="Unable To Access Banned Account";

    // The user is not banned, continue with login process
    /* code to handle the login process */
  }
} else {
  echo "There was an error processing your request.";
}

?>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap");
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;

}
body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #892929;
}
.container {
    position: relative;
    max-width: 350px;
    width: 100%;
    background-color: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="container">
    <h3><i class="fa-solid fa-circle-exclamation" style="color: #e61942;"></i>
        <?PHP echo $error ?>
    </h3>
</div>
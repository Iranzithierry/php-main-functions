<?php
include("../DB/conn.php");
session_start();
if(isset($_SESSION['loggedIn'])) {
   
} else {
    header("location: ../index.php");
}
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the user ID from the session or URL parameter
$user_id = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : $_GET['loggedIn'];

// Retrieve the user's results from the user_results table
$sql = "SELECT * FROM user_results WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Display the user's results
    $row = mysqli_fetch_assoc($result);
    echo "Question 1: " . $row['question_1'] . "<br>";
    echo "Question 2: " . $row['question_2'] . "<br>";
    echo "Question 3: " . $row['question_3'] . "<br>";
    echo "Result: " . $row['result'] . "<br>";
} else {
    echo "Error retrieving results: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>

<?php
session_start();

// Database credentials
$servername = 'localhost';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($servername, $user, $pass, $db);

// Check if the user is logged in
if (!isset($_SESSION['lecturer_email'])) {
    echo 'not_logged_in';
    exit();
}

$email = $_SESSION['lecturer_email'];

// Get the new full name from POST request
if (isset($_POST['full_name'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    // Update the full name in the database
    $query = "UPDATE Lecturer SET full_name = '$full_name' WHERE email = '$email'";
    

    if (mysqli_query($conn, $query)) {
        echo 'success';
        $_SESSION['lecturer_full_name'] = $full_name;
    } else {
        echo 'error';
    }
}

mysqli_close($conn);
?>

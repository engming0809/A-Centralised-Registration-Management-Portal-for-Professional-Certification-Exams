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
if (!isset($_SESSION['student_email'])) {
    echo 'not_logged_in';
    exit();
}

$email = $_SESSION['student_email'];

// Get the new full name from POST request
if (isset($_POST['full_name'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    // Update the full name in the database
    $query = "UPDATE Student SET full_name = '$full_name' WHERE email = '$email'";
    

    if (mysqli_query($conn, $query)) {
        echo 'success';
        $_SESSION['student_full_name'] = $full_name;
    } else {
        echo 'error';
    }
}elseif (isset($_POST['email'])){
    
    $emailStudent = mysqli_real_escape_string($conn, $_POST['email']);

    // Update the full name in the database
    $query = "UPDATE Student SET email = '$emailStudent' WHERE email = '$email'";
    

    if (mysqli_query($conn, $query)) {
        echo 'success';
        $_SESSION['student_email'] = $emailStudent;
    } else {
        echo 'error';
    }
}elseif (isset($_POST['biography'])){
    
    $biography = mysqli_real_escape_string($conn, $_POST['biography']);

    // Update the full name in the database
    $query = "UPDATE Student SET biography = '$biography' WHERE email = '$email'";
    

    if (mysqli_query($conn, $query)) {
        echo 'success';
    } else {
        echo 'error';
    }
}

mysqli_close($conn);
?>

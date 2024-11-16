<?php
session_start();

// Database connection
$servername = 'localhost';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';
$conn = new mysqli($servername, $user, $pass, $db);

// Check if the user is logged in
if (!isset($_SESSION['student_email'])) {
    echo 'not_logged_in';
    exit();
}

// Check if an image was uploaded
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $image = $_FILES['profile_image'];
    $image_name = $image['name'];
    $image_tmp = $image['tmp_name'];
    $image_size = $image['size'];
    
    // Define allowed file types and size limit (e.g., 2MB max)
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 10 * 1024 * 1024;  // 10MB
    
    if (in_array($image['type'], $allowed_types) && $image_size <= $max_size) {
        // Generate a unique name for the image
        $image_new_name = uniqid('profile_', true) . '.' . pathinfo($image_name, PATHINFO_EXTENSION);
        $upload_dir = 'uploads/profile_images/';
        
        // Move the uploaded image to the server directory
        if (move_uploaded_file($image_tmp, $upload_dir . $image_new_name)) {
            // Update the lecturer's profile image path in the database
            $email = $_SESSION['student_email'];
            $query = "UPDATE Student SET profileimg = '$upload_dir$image_new_name' WHERE email = '$email'";
            
            if ($conn->query($query) === TRUE) {
                echo 'success';
            } else {
                echo 'error_updating_db';
            }
        } else {
            echo 'error_uploading_file';
        }
    } else {
        echo 'invalid_file';
    }
} else {
    echo 'no_file';
}
?>

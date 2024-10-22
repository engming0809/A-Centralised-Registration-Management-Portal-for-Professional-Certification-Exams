<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Composer's autoloader
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connect to the database
$conn = new mysqli("localhost", "root", "", "cert_reg_management_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully.<br>"; // Debugging message
}

// Check if the token is provided
if (isset($_GET['token'])) {
    $token = trim($_GET['token']); // Trim whitespace
    echo "Token from URL: " . htmlspecialchars($token) . "<br>"; // Debug line

    if (empty($token)) {
        echo "The verification token cannot be empty.";
        exit;
    }

    // Prepare a statement to select the user by the verification token
    $stmt = $conn->prepare("SELECT * FROM Student WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    
    if (!$stmt->execute()) {
        echo "SQL Error: " . $stmt->error; // Error checking
        $stmt->close();
        $conn->close();
        exit;
    }
    
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Optionally log $row data instead of printing it
        // file_put_contents('log.txt', print_r($row, true), FILE_APPEND);

        // Token is valid, update user status to 'active'
        $update_stmt = $conn->prepare("UPDATE Student SET status = 'active', verification_token = NULL WHERE verification_token = ?");
        $update_stmt->bind_param("s", $token);
        
        if ($update_stmt->execute()) {
            echo "Your email has been verified successfully! You can now log in.";
        } else {
            echo "Error updating user status: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        echo "Invalid verification token.";
    }

    $stmt->close();
} else {
    echo "No token provided.";
}

// Close database connection
$conn->close();
?>

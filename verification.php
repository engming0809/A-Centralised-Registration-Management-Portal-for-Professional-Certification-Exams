<?php
// // Enable error reporting
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// // Load Composer's autoloader
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';
// require 'PHPMailer/src/Exception.php';

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// // Connect to the database
// $conn = new mysqli("localhost", "root", "", "cert_reg_management_db");

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// } else {
//     echo "Connected successfully.<br>"; // Debugging message
// }

// // Check if the token is provided
// if (isset($_GET['token'])) {
//     $token = trim($_GET['token']); // Trim whitespace
//     echo "Token from URL: " . htmlspecialchars($token) . "<br>"; // Debug line

//     if (empty($token)) {
//         echo "The verification token cannot be empty.";
//         exit;
//     }

//     // Prepare a statement to select the user by the verification token
//     $stmt = $conn->prepare("SELECT * FROM Student WHERE verification_token = ?");
//     $stmt->bind_param("s", $token);
    
//     if (!$stmt->execute()) {
//         echo "SQL Error: " . $stmt->error; // Error checking
//         $stmt->close();
//         $conn->close();
//         exit;
//     }
    
//     $result = $stmt->get_result();

//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         // Optionally log $row data instead of printing it
//         // file_put_contents('log.txt', print_r($row, true), FILE_APPEND);

//         // Token is valid, update user status to 'active'
//         $update_stmt = $conn->prepare("UPDATE Student SET status = 'active', verification_token = NULL WHERE verification_token = ?");
//         $update_stmt->bind_param("s", $token);
        
//         if ($update_stmt->execute()) {
//             echo "Your email has been verified successfully! You can now log in.";
//         } else {
//             echo "Error updating user status: " . $update_stmt->error;
//         }
//         $update_stmt->close();
//     } else {
//         echo "Invalid verification token.";
//     }

//     $stmt->close();
// } else {
//     echo "No token provided.";
// }

// // Close database connection
// $conn->close();
?>



<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Composer's autoloader
require 'vendor/autoload.php'; // Include Mailjet library (install it with Composer)

use \Mailjet\Resources;

// Database credentials
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connected successfully.<br>"; // Debug message

    // Mailjet API credentials
    $mailjetApiKey = 'f3a4ec9a624b3edc801f0260aa17d9d6';
    $mailjetApiSecret = 'd87dc2068c46050e544b2111350caa8e';
    $mj = new \Mailjet\Client($mailjetApiKey, $mailjetApiSecret, true, ['version' => 'v3.1']);

    // Check if the token is provided in the URL
    if (isset($_GET['token'])) {
        $token = trim($_GET['token']); // Remove whitespace
        echo "Token received: " . htmlspecialchars($token) . "<br>"; // Debug line

        if (empty($token)) {
            echo "The verification token cannot be empty.";
            exit;
        }

        // Prepare statement to find the user by the verification token
        $stmt = $pdo->prepare("SELECT * FROM Student WHERE verification_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            // Token is valid; update user status to 'active'
            $updateStmt = $pdo->prepare("UPDATE Student SET status = 'active', verification_token = NULL WHERE verification_token = :token");
            $updateStmt->bindParam(':token', $token);

            if ($updateStmt->execute()) {
                echo "Your email has been verified successfully! You can now log in.";

                // Send confirmation email using Mailjet
                $email = $student['email'];
                $fullName = $student['full_name'];

                $body = [
                    'Messages' => [
                        [
                            'From' => [
                                'Email' => "cckiat2002@gmail.com",
                                'Name' => "Certification Portal"
                            ],
                            'To' => [
                                [
                                    'Email' => $email,
                                    'Name' => $fullName
                                ]
                            ],
                            'Subject' => "Email Verification Successful",
                            'TextPart' => "Dear $fullName, your email verification is successful. You can now log in to your account.",
                            'HTMLPart' => "<h3>Dear $fullName,</h3><p>Your email verification is successful. You can now log in to your account.</p>"
                        ]
                    ]
                ];

                $response = $mj->post(Resources::$Email, ['body' => $body]);

                if ($response->success()) {
                    echo "Confirmation email sent successfully!";
                } else {
                    echo "Failed to send confirmation email. Error: " . $response->getReasonPhrase();
                }
            } else {
                echo "Error updating user status: " . $updateStmt->errorInfo()[2];
            }
        } else {
            echo "Invalid verification token.";
        }
    } else {
        echo "No token provided.";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
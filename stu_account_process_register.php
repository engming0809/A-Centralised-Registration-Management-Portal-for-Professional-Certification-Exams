<?php
// process_register.php

// Load Composer's autoloader for PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize error array
$errors = [];
$success_message = '';
$error_status = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection parameters
    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $dbname = "cert_reg_management_db"; 

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $fullname = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate Name
    if (empty($fullname) || !preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errors[] = "Invalid name. Only letters and white spaces allowed.";
    }

    // Validate Email Format for Swinburne
    if (empty($email) || !preg_match("/^\d{9}@students\.swinburne\.edu\.my$/", $email)) {
        $errors[] = "Please enter email in Swinburne email format.";
    }

    // Validate Password
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must contain at least 8 characters.";
    }

    // Confirm Password Match
    if ($password !== trim($_POST['confirm_password'])) {
        $errors[] = "Passwords do not match.";
    }

    // Only proceed if there are no errors
    if (empty($errors)) {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT status FROM Student WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Email exists, check status
            $stmt->bind_result($status);
            $stmt->fetch();
            if ($status === 'pending') {
                $error_status[] = "Please confirm the validation link in your email.";
            } elseif ($status === 'active') {
                $error_status[] = "User account exists and status is active.";
            }
        } else {
            // Email doesn't exist, proceed with registration
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Set student account status to 'pending'
            $account_status = 'pending';

            // Generate verification token
            $verification_token = bin2hex(random_bytes(16));

            // Prepare and execute statement
            $stmt = $conn->prepare("INSERT INTO Student (full_name, email, password, status, verification_token) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fullname, $email, $hashed_password, $account_status, $verification_token);

            if ($stmt->execute()) {
                // Send verification email
                sendVerificationEmail($email, $verification_token);
                $success_message = "Registration successful! Please check your email to verify your account.";
            } else {
                $error_status[] = "Error: " . $stmt->error;
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
}

// Function to send verification email
function sendVerificationEmail($email, $verification_token) {
    $verification_link = "http://localhost/fyptest/verification.php?token=$verification_token";

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'adolf4201989@gmail.com'; // Replace with your SMTP username
        $mail->Password = 'upcu ewmg inmo wrxb'; // Replace with your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('adolf4201989@gmail.com', 'Mailer');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "Click on this link to verify your email: <a href='$verification_link'>$verification_link</a>";

        $mail->send();
        echo "Verification email has been sent.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <main class="landing_main">
        <!-- Header  -->
        <?php
            $pageTitle = "Student Registration";
            $pageHeaderTitle = "Student Registration";
            include 'include/landing_header.php';
        ?>
        <!-- Main Content -->
        <section class="container registration_container">
            <h2>Registration Form</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                    <?php if (in_array("Invalid name. Only letters and white spaces allowed.", $errors)): ?>
                        <p class='error_message alert-danger'>*Invalid name. Only letters and white spaces allowed.</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="text" name="email" class="form-control" placeholder="Email" required>
                    <?php if (in_array("Please enter email in Swinburne email format.", $errors)): ?>
                        <p class='error_message alert-danger'>*Please enter email in Swinburne email format.</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <?php if (in_array("Password must contain at least 8 characters.", $errors)): ?>
                        <p class='error_message alert-danger'>*Password must contain at least 8 characters.</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                    <?php if (in_array("Passwords do not match.", $errors)): ?>
                        <p class='error_message alert-danger'>*Passwords do not match.</p>
                    <?php endif; ?>
                </div>

                <!-- Display errors, if any -->
<?php if (!empty($error_status)): ?>
    <div class="error_message alert-success">
        <?php echo implode("<br>", $error_status); ?>
    </div>
    <!-- Display success message, if any -->
<?php endif; ?>

<?php if (!empty($success_message)): ?>
    <div class="error_message alert-success">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>




                <div class="registration_button_container">
                    <input type="reset" class="btn btn-primary" value="Reset">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
                <a href="stu_account_login.php" class="main_menu_link d-block mt-3">Back to Login</a>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'include/footer.php'; ?>
</body>
</html>

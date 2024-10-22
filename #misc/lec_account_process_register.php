<?php
// Initialize an empty array to store error messages
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
        $errors[] = "Connection failed: " . $conn->connect_error;
    }

    // Get form data
    $fullname = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate Name
    if (empty($fullname) || !preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errors[] = "Invalid name. Only letters and white spaces allowed.";
    }

    // Validate Email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate Password
if (empty($password) || strlen($password) < 8) {
    $errors[] = "Password must contain at least 8 characters.";
}

// Confirm Password Match
if ($password !== trim($_POST['confirm_password'])) {
    $errors[] = "Passwords do not match.";
}

if (empty($errors)) {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists in the Lecturer table
    $sql = "SELECT status FROM Lecturer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($status);
        $stmt->fetch();

        if ($status == 'active') {
            $error_status[] = "Email already registered and active.";
        } elseif ($status == 'inactive') {
            // Update status to pending if inactive
            $update_sql = "UPDATE Lecturer SET status = 'pending', password = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $hashed_password, $email);
            if ($update_stmt->execute()) {
                $success_message = "Your request is currently pending administrator approval. In the meantime, please return to the login page.";
            } else {
                $error_status[] = "Error updating status: " . $update_stmt->error;
            }
        } elseif ($status == 'pending') {
            $error_status[] = "Please wait for admin approval.";
        }
    } else {
        // Email does not exist, insert new lecturer with pending status
        $insert_sql = "INSERT INTO Lecturer (full_name, email, password, status) VALUES (?, ?, ?, 'pending')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sss", $fullname, $email, $hashed_password);

        if ($insert_stmt->execute()) {
            // Set success message
            $success_message = "Your request is currently pending administrator approval. In the meantime, please return to the login page.";
        } else {
            $error_status[] = "Error: " . $insert_stmt->error;
        }
    }

    // Close connections
    $stmt->close();
    $conn->close();
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
        <!-- Header -->
        <?php
            $pageTitle = "Lecturer Registration";
            $pageHeaderTitle = "Lecturer Registration";
            include 'include/landing_header.php';
        ?>

        <!-- Main Content -->
        <section class="container registration_container">
            <h2>Registration Form</h2>

            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" value="<?php echo htmlspecialchars($fullname); ?>" required>
                    <?php if (in_array("Invalid name. Only letters and white spaces allowed.", $errors)): ?>
                        <p class='error_message alert-danger'>*Invalid name. Only letters and white spaces allowed.</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <?php if (in_array("Invalid email format.", $errors)): ?>
                        <p class='error_message alert-danger'>*Invalid email format.</p>
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

                <!-- Display success message, if any -->
<?php if (!empty($success_message)): ?>
    <div class="error_message alert-success">
        <h3><?php echo htmlspecialchars($success_message); ?></h3>
    </div>
<?php endif; ?>


                <!-- Display status-related errors at the bottom -->
<?php if (!empty($error_status)): ?>
    <div class="error_message alert-success">
        <?php 
            // Loop through the error status array and display relevant messages
            foreach ($error_status as $error) {
                if ($error === "Email already registered and active." || $error === "Please wait for admin approval.") {
                    echo "<h3>" . htmlspecialchars($error) . "</h3>";
                }
            }
        ?>
    </div>
<?php endif; ?>

                <div class="registration_button_container">
                    <input type="reset" class="btn btn-primary" value="Reset">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
                <a href="lec_account_login.php" class="main_menu_link d-block mt-3">Back to Login</a>

                

            </form>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'include/footer.php'; ?>
</body>
</html>

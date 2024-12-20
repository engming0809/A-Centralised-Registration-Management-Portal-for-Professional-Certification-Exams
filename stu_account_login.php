<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
<main class="landing_main">
    <!-- Header -->
    <?php
        $pageTitle = "Student Login";
        $pageHeaderTitle = "Student Login";
        include 'include/landing_header.php';
    ?>

    <!-- Main Content -->
    <section class="container">
        <section class="row login_container">
            <!-- Image Section -->
            <div class="col-md-6 d-none d-md-block p-0">
                <img src="image/stu_login.jpg" alt="Sample Image" class="img-fluid">
            </div>

            <!-- Form Section -->
            <div class="col-md-6 form-section">
                <h2>Please Log In</h2>

                <?php
                date_default_timezone_set('Asia/Kuala_Lumpur');
                session_start(); 
                $errors = '';

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

                    $email = trim($_POST['email']);
                    $password = trim($_POST['password']);

                    // Validate email format
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
                        // Prepare and execute statement
                        // Select student_id along with full_name, password, and status
                        $stmt = $conn->prepare("SELECT student_id, full_name, password, status FROM Student WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $stmt->store_result();

                        // Check if email exists
                        if ($stmt->num_rows > 0) {
                            $stmt->bind_result($student_id, $fullname, $hashed_password, $status); // Bind student_id
                            $stmt->fetch();

                            // Check user status
                            if ($status == 'pending') {
                                $errors = "Your registration is pending. Please verify the link.";
                            } elseif ($status == 'inactive') {
                                $errors = "Your account is inactive. Please register again.";
                            } elseif ($status == 'active') {
                                // Verify password
                                if (password_verify($password, $hashed_password)) {
                                    // Store user information in session
                                    $_SESSION['student_id'] = $student_id; // Store student_id in session
                                    $_SESSION['student_email'] = $email;
                                    $_SESSION['student_full_name'] = $fullname;

                                    header("Location: stu_dashboard.php");
                                    exit(); 
                                } else {
                                    $errors = "Login Failed. Invalid email or password. Please try again.";
                                }
                            }
                        } else {
                            $errors = "Login Failed. Invalid email or password. Please try again.";
                        }

                        $stmt->close();
                    } else {
                        $errors = "Login Failed. Invalid email or password. Please try again.";
                    }

                    // Close connection
                    $conn->close();
                }
                ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>

                <p class="mt-3 text-center">Don't have an account? <a class="register_link" href="stu_account_registration.php">Register here</a></p>

                <?php
                if (!empty($errors)) {
                    echo "<div class='error_message alert-success'>$errors</div>";
                }
                ?>
            </div>
        </section>
    </section>
</main>

<!-- Footer -->
<?php
    include 'include/footer.php';
?>
</body>
</html>

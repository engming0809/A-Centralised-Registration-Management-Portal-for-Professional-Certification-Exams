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
        $pageTitle = "Admin Login";
        $pageHeaderTitle = "Admin Login";
        include 'include/landing_header.php';
    ?>

    <!-- Main Content -->
    <section class="container">
        <section class="row login_container">
            <!-- Image Section -->
            <div class="col-md-6 d-none d-md-block p-0">
                <img src="image/admin_login.jpg" alt="Sample Image" class="img-fluid">
                <!-- https://c0.wallpaperflare.com/preview/140/698/423/security-computer-web-virus.jpg -->
            </div>

            <!-- Form Section -->
            <div class="col-md-6 form-section">
                <h2>Please Log In</h2>

                <?php
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
                        $stmt = $conn->prepare("SELECT full_name, password FROM Admin WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $stmt->store_result();

                        // Check if email exists
                        if ($stmt->num_rows > 0) {
                            $stmt->bind_result($fullname, $stored_password);
                            $stmt->fetch();

                            // Verify password
                            if ($password === $stored_password) {
                                // Store user information in session
                                $_SESSION['user_email'] = $email;
                                $_SESSION['user_full_name'] = $fullname;

                                header("Location: admin_dashboard.php");
                                exit(); 
                            } else {
                                $errors = "Login Failed. Invalid email or password. Please try again.";
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

                <?php
                if (!empty($errors)) {
                    echo "<div class='error_message alert-danger'>$errors</div>";
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

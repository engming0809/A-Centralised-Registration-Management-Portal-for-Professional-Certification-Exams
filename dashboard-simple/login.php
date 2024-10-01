<?php
// login.php
session_start();
include 'include/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Try to find the user in each table based on the username
    $tables = ['user_student', 'user_lecturer', 'user_admin'];
    $userFound = false;
    $userRole = '';

    foreach ($tables as $table) {
        $sql = "SELECT * FROM $table WHERE username='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username;
                // Determine the role based on the table
                switch ($table) {
                    case 'user_admin':
                        $_SESSION['role'] = 'admin';
                        header("Location: admin_dashboard.php");
                        break;
                    case 'user_lecturer':
                        $_SESSION['role'] = 'lecturer';
                        header("Location: lecturer_dashboard.php");
                        break;
                    case 'user_student':
                    default:
                        $_SESSION['role'] = 'student';
                        header("Location: student_dashboard.php");
                        break;
                }
                $userFound = true;
                exit();
            } else {
                echo "Invalid password.";
                $userFound = true; 
                break;
            }
        }
    }

    if (!$userFound) {
        echo "No user found with that username.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>


<body>

<!-- Introduction Content Start -->
    <div class="main-content d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "LMAO";
    include 'main_header.php';
    ?>


<!-- Something else -->
        <section class="container d-flex justify-content-center ">
            <div class="row w-100 mx-auto accountlogin">

                <!-- Image Section -->
                <div class="col-md-6 d-flex align-items-center justify-content-center loginimage">
                    <img src="image/login_image.png" alt="Sample Image" class="img-fluid">
                </div>

                <!-- Form Section -->
            <div class="col-md-6 loginform">
            <h2>Login</h2>
            <form method="POST" action="">
                <label for="username">Username:</label>
                <input type="text" name="username" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" name="password" required>
                <br>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p> 

            </div>
        </section>

    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website. All rights reserved.</p>
    </footer>

    </div>
</body>



</html>

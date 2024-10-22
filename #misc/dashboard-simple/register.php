<?php
// register.php
include 'include/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password
    $role = $_POST['role']; // Get the selected role

    // Determine which table to insert into based on the role
    switch ($role) {
        case 'student':
            $sql = "INSERT INTO user_student (username, password) VALUES ('$username', '$password')";
            break;
        case 'lecturer':
            $sql = "INSERT INTO user_lecturer (username, password) VALUES ('$username', '$password')";
            break;
        case 'admin':
            $sql = "INSERT INTO user_admin (username, password) VALUES ('$username', '$password')";
            break;
        default:
            echo "Invalid role selected.";
            exit;
    }

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
                    <h2>Register</h2>
                    <form method="POST" action="">
                        <label for="username">Username:</label>
                        <input type="text" name="username" required>
                        <br>
                        <label for="password">Password:</label>
                        <input type="password" name="password" required>
                        <br>
                        <label for="role">Role:</label>
                        <select name="role" required>
                            <option value="student">Student</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="admin">Admin</option>
                        </select>
                        <br>
                        <button type="submit">Register</button>
                    </form>
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>

            </div>
        </section>

    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website. All rights reserved.</p>
    </footer>

    </div>



    
</body>
</html>

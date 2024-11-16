<!DOCTYPE html>
<html lang="en">

<head>
	<title>My First PHP Webpage</title>
	<meta charset="utf-8">
	<meta name="description" content="Web development">
	<meta name="keywords" content="HTML, CSS, JavaScript">
	<meta name="author" content="your name">
</head>

<body>
	<h1>User Registration and Login</h1>

	<?php
	// Start a session
	session_start();

	// Database connection setup
	$servername = "localhost"; // Change as per your setup
	$username = "root"; // Change as per your setup
	$password = ""; // Change as per your setup
	$dbname = "cert_reg_management_db"; // Change as per your setup

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	// Create Student table if it does not exist
	$sql = "CREATE TABLE IF NOT EXISTS Student (
		student_id INT AUTO_INCREMENT PRIMARY KEY,
		full_name VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL,
		status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
		password VARCHAR(255) NOT NULL,
		verification_token VARCHAR(255),
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	)";
	$conn->query($sql);

	// Handling form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        // Registration logic
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

        // Insert user into the Student table
        $sql = "INSERT INTO Student (full_name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $full_name, $email, $password);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['login'])) {
        // Login logic
        $email = $_POST['login_email'];
        $password = $_POST['login_password'];

        $sql = "SELECT * FROM Student WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['student_email'] = $row['student_email'];
                $_SESSION['student_full_name'] = $row['full_name'];

                // Redirect to dashboard after login
                header("Location: stu_dashboard.php");
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No user found with that email.";
        }

        $stmt->close();
    }
}

// Logout logic (destroy session)
if (isset($_POST['logout'])) {
    session_destroy();
    echo "You have been logged out.";
}
?>

	<h2>Register</h2>
	<form method="POST">
		Full Name: <input type="text" name="full_name" required><br>
		Email: <input type="email" name="email" required><br>
		Password: <input type="password" name="password" required><br>
		<button type="submit" name="register">Register</button>
	</form>

	<h2>Login</h2>
	<form method="POST">
		Email: <input type="email" name="login_email" required><br>
		Password: <input type="password" name="login_password" required><br>
		<button type="submit" name="login">Login</button>
	</form>

	<?php
	// If user is logged in, show the logout button
	if (isset($_SESSION['student_id'])) {
		echo '<form method="POST">
				<button type="submit" name="logout">Logout</button>
			</form>';
	}
	?>

</body>
</html>

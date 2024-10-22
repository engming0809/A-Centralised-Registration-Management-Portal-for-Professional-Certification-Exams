<!DOCTYPE html>
<html lang="en">

<head>
	<title>My First PHP webpage</title>
	<meta charset="utf-8">
	<meta name="description" content="Web development">
	<meta name="keywords" content="HTML, CSS, JavaScript">
	<meta name="author" content="your name">
</head>
	<body>
		<h1>AS</h1>
		
	<?php
$servername = "localhost";  
$username = "root";         
$password = "";             

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
	
$conn->select_db("cert_reg_management_db");

$conn->query("INSERT INTO Lecturer (full_name, email, password) 
    VALUES ('John Doe', 'john.doe@university.edu', 'password123')");

$conn->query("INSERT INTO Student (full_name, email, password) 
    VALUES ('Jane Smith', 'jane.smith@student.edu', 'studentpass')");

echo "Dummy data inserted successfully\n";

$conn->close();
?>
	
	
	</body>
</html>
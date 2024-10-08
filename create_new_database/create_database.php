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
		<?php
$servername = "localhost";  
$username = "root";         
$password = "";             

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS cert_reg_management_db";
if ($conn->query($sql) === TRUE) {
    echo "Database 'cert_reg_management_db' created successfully\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

$conn->select_db("cert_reg_management_db");

$sql = "CREATE TABLE IF NOT EXISTS Lecturer (
    lecturer_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Student (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Certifications (
    certification_id INT AUTO_INCREMENT PRIMARY KEY,
    certification_name VARCHAR(255) NOT NULL,
    description TEXT,
    requirements TEXT,
    schedule DATETIME,
    cost DECIMAL(10, 2),
    lecturer_id INT,
    FOREIGN KEY (lecturer_id) REFERENCES Lecturer(lecturer_id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS CertificationRegistrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    registration_status VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    student_id INT,
    certification_id INT,
    FOREIGN KEY (student_id) REFERENCES Student(student_id),
    FOREIGN KEY (certification_id) REFERENCES Certifications(certification_id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_PaymentInvoice (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_TransactionSlip (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_PaymentReceipt (
    receipt_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_ExamConfirmationLetter (
    confirmation_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_ExamResult (
    examresult_id INT AUTO_INCREMENT PRIMARY KEY,
    result VARCHAR(255) NOT NULL,
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_Certificate (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id)
)";
$conn->query($sql);

$conn->close();
?>

	<form action="insertnewdata.php" method="POST">
        <button type="submit">add new user</button>
    </form>
</html>
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
    status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
    profileimg VARCHAR(255) NOT NULL,
    biography VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS Student (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
    profileimg VARCHAR(255) NOT NULL,
    biography VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    verification_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create Admin table if not exists
$sql = "CREATE TABLE IF NOT EXISTS Admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Check if an admin account exists
$adminEmail = 'admin@swinburne.edu.my'; // Replace with the desired admin email
$adminPassword = '123456'; // Replace with the desired password

$sql = "SELECT * FROM Admin WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $adminEmail);
$stmt->execute();
$result = $stmt->get_result();

// If admin account doesn't exist, insert it
if ($result->num_rows == 0) {
    $sql = "INSERT INTO Admin (full_name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $adminName, $adminEmail, $adminPassword);

    $adminName = 'Admin'; // Replace with the desired admin full name
    if ($stmt->execute()) {
        echo "Admin account created successfully.";
    } else {
        echo "Error creating admin account: " . $conn->error;
    }
} else {
    echo "Admin account already exists.";
}

// Create Certifications table if not exists
$sql = "CREATE TABLE IF NOT EXISTS Certifications (
    certification_id INT AUTO_INCREMENT PRIMARY KEY,
    certification_name VARCHAR(255) NOT NULL,
    description TEXT,
    requirements TEXT,
    schedule DATETIME,
    cost INT NOT NULL,
    status ENUM('available', 'expired') NOT NULL DEFAULT 'available'
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS CertificationRegistrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    registration_status VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    student_id INT,
    certification_id INT,
    notification TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (student_id) REFERENCES Student(student_id),
    FOREIGN KEY (certification_id) REFERENCES Certifications(certification_id) ON DELETE CASCADE
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_RegistrationForm (
    form_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    registration_id INT,
    status ENUM('pending', 'accept', 'reject') NOT NULL DEFAULT 'pending',
    reason VARCHAR(255),
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id) ON DELETE CASCADE
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_PaymentInvoice (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    status ENUM('pending', 'accept', 'reject') NOT NULL DEFAULT 'pending',
    reason VARCHAR(255),
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id) ON DELETE CASCADE
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_TransactionSlip (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    status ENUM('pending', 'accept', 'reject') NOT NULL DEFAULT 'pending',
    reason VARCHAR(255),
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id) ON DELETE CASCADE
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_PaymentReceipt (
    receipt_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    status ENUM('pending', 'accept', 'reject') NOT NULL DEFAULT 'pending',
    reason VARCHAR(255),
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id) ON DELETE CASCADE
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_ExamConfirmationLetter (
    confirmation_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    status ENUM('pending', 'accept', 'reject') NOT NULL DEFAULT 'pending',
    reason VARCHAR(255),
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id) ON DELETE CASCADE
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_ExamResult (
    examresult_id INT AUTO_INCREMENT PRIMARY KEY,
    result VARCHAR(255) NOT NULL,
    registration_id INT,
    publish ENUM('published', 'not_published') NOT NULL DEFAULT 'not_published',
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id) ON DELETE CASCADE
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS reg_Certificate (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    status ENUM('pending', 'accept', 'reject') NOT NULL DEFAULT 'pending',
    reason VARCHAR(255),
    registration_id INT,
    FOREIGN KEY (registration_id) REFERENCES CertificationRegistrations(registration_id) ON DELETE CASCADE
)";
$conn->query($sql);


$conn->close();
?>

	<form action="insertnewdata.php" method="POST">
        <button type="submit">add new user</button>
    </form>
</html>
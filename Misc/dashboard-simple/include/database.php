<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "user_role"; 

// Check connection to MySQL server
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it does not exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    echo "Error creating database: " . $conn->error;
}

// Check connection to the newly created database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create user_student table if it doesn't exist
$sql_student = "CREATE TABLE IF NOT EXISTS user_student (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
if ($conn->query($sql_student) === FALSE) {
    echo "Error creating user_student table: " . $conn->error;
}

// Create user_lecturer table if it doesn't exist
$sql_lecturer = "CREATE TABLE IF NOT EXISTS user_lecturer (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
if ($conn->query($sql_lecturer) === FALSE) {
    echo "Error creating user_lecturer table: " . $conn->error;
}

// Create user_admin table if it doesn't exist
$sql_admin = "CREATE TABLE IF NOT EXISTS user_admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
if ($conn->query($sql_admin) === FALSE) {
    echo "Error creating user_admin table: " . $conn->error;
}

?>

<?php
// Database connection parameters
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "cert_reg_management_db"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lecturer_id'])) {
    $lecturer_id = intval($_POST['lecturer_id']);

    // Update the status to 'inactive' or delete the record
    $stmt = $conn->prepare("UPDATE Lecturer SET status = 'inactive' WHERE lecturer_id = ?");
    $stmt->bind_param("i", $lecturer_id);

    if ($stmt->execute()) {
        echo "Lecturer rejected successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
header("Location: admin_request.php"); // Redirect back to the admin page
exit();
?>

<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

// Database connection details
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_invoice'])) {
        $invoiceId = $_POST['invoice_id'];
        $file = $_FILES['payment_invoice'];

        // Define allowed MIME types
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
        
        // Check if file type is allowed
        if (!in_array($file['type'], $allowedTypes)) {
            echo "Only PDF and image files are allowed.";
            exit();
        }

        // Define the upload directory and file path
        $uploadDir = 'uploads/payment_invoices/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
        }

        $filePath = $uploadDir . basename($file['name']);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Update the database with the new file path
            $stmt = $pdo->prepare("UPDATE reg_paymentinvoice SET filepath = :filepath WHERE invoice_id = :invoice_id");
            $stmt->bindParam(':invoice_id', $invoiceId);
            $stmt->bindParam(':filepath', $filePath);
            $stmt->execute();

            echo "Invoice uploaded successfully!";
            header("Location: http://localhost/FYP/dashboard-simple/certification_list.php"); // Redirect back to dashboard
            exit();
        } else {
            echo "Error uploading the file.";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

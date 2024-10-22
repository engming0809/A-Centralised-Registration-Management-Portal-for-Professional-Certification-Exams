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

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_receipt'])) {
        $receiptId = $_POST['receipt_id'];
        $file = $_FILES['payment_receipt'];

        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($file['type'], $allowedTypes)) {
            echo "Only PDF and image files are allowed.";
            exit();
        }

        $uploadDir = 'uploads/payment_receipt/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . uniqid() . '_' . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $stmt = $pdo->prepare("UPDATE reg_paymentreceipt SET filepath = :filepath WHERE receipt_id = :receipt_id");
            $stmt->bindParam(':receipt_id', $receiptId);
            $stmt->bindParam(':filepath', $filePath);
            $stmt->execute();

            echo "Receipt uploaded successfully!";
            header("Location: http://localhost/FYP/dashboard-simple/registration_overview.php"); // Redirect back to dashboard
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

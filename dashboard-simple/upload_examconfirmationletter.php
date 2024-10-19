<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['exam_confirmation_letter'])) {
        $confirmationId = $_POST['confirmation_id'];
        $file = $_FILES['exam_confirmation_letter'];

        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            echo "Only PDF and image files are allowed.";
            exit();
        }

        $uploadDir = 'uploads/exam_confirmation_letter/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); 
        }

        $filePath = $uploadDir . uniqid() . '_' . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $stmt = $pdo->prepare("UPDATE reg_examconfirmationletter SET filepath = :filepath WHERE confirmation_id = :confirmation_id");
            $stmt->bindParam(':confirmation_id', $confirmationId);
            $stmt->bindParam(':filepath', $filePath);
            $stmt->execute();

            echo "Exam Confirmation Letter uploaded successfully!";
            header("Location: http://localhost/FYP/dashboard-simple/registration_overview.php"); // Redirect back to dashboard
            exit(); 
            echo "Error uploading the file.";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

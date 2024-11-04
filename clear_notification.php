<?php
session_start();
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $registrationId = $_POST['registration_id'];
        $stmt = $pdo->prepare("UPDATE certificationregistrations SET notification = 0 WHERE registration_id = :registration_id");
        $stmt->bindParam(':registration_id', $registrationId);
        $stmt->execute();

    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

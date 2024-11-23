<?php
session_start();
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $certificateStatusOption = $_POST['certificateStatusOption'];
        $certificateReason = $_POST['certificate_reason'];
        $certificateID = $_POST['certificate_id'];

        // Check if the exam result already exists
        if ($certificateID) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE reg_certificate SET status = :status, reason = :reason WHERE certificate_id = :certificate_id");
            $stmt->bindParam(':status', $certificateStatusOption);
            $stmt->bindParam(':reason', $certificateReason);
            $stmt->bindParam(':certificate_id', $certificateID);
            $stmt->execute();
            echo "Certificate updated successfully!";
        } 
        header("Location: stu_overview_reg.php"); 
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

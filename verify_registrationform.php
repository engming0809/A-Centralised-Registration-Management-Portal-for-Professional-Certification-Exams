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
        $regformStatusOption = $_POST['regformStatusOption'];
        $regformReason = $_POST['registration_form_reason'];
        $regformID = $_POST['form_id'];

        // Check if the exam result already exists
        if ($regformID) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE reg_registrationform SET status = :status, reason = :reason WHERE form_id = :form_id");
            $stmt->bindParam(':status', $regformStatusOption);
            $stmt->bindParam(':reason', $regformReason);
            $stmt->bindParam(':form_id', $regformID);
            $stmt->execute();
            echo "Exam result updated successfully!";
        } 
        header("Location: lec_overview_reg.php"); 
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

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
        $examResult = $_POST['examResult'];
        $registrationId = $_POST['registration_id'];  // Certification registration ID
        $examResultId = isset($_POST['examresult_id']) ? $_POST['examresult_id'] : null; // Optional, for updating

        // Determine publish status based on the radio button selection
        // Expecting '1' for published and '0' for not published
        $publish = $_POST['publish'] == '1' ? 'published' : 'not_published';

        // Check if the exam result already exists
        if ($examResultId) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE reg_ExamResult SET result = :result, publish = :publish WHERE examresult_id = :examresult_id");
            $stmt->bindParam(':examresult_id', $examResultId);
            $stmt->bindParam(':result', $examResult);
            $stmt->bindParam(':publish', $publish);
            $stmt->execute();
            echo "Exam result updated successfully!";
        } else {
            // Insert a new exam result
            $stmt = $pdo->prepare("INSERT INTO reg_ExamResult (result, registration_id, publish) VALUES (:result, :registration_id, :publish)");
            $stmt->bindParam(':result', $examResult);
            $stmt->bindParam(':registration_id', $registrationId);
            $stmt->bindParam(':publish', $publish);
            $stmt->execute();
            echo "Exam result submitted successfully!";

            // Update the registration status to 'invoice_submitted'
            $stmt = $pdo->prepare("UPDATE CertificationRegistrations SET registration_status = 'result_submitted' WHERE registration_id = :registration_id");
            $stmt->bindParam(':registration_id', $registrationId);
            $stmt->execute();
        }
        $stmt = $pdo->prepare("UPDATE certificationregistrations SET notification = 1 WHERE registration_id = :registration_id");
        $stmt->bindParam(':registration_id', $registrationId);
        $stmt->execute();
        header("Location: lec_overview_reg.php"); 
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

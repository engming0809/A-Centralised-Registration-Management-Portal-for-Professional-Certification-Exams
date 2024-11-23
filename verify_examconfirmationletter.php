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
        $examletterStatusOption = $_POST['examletterStatusOption'];
        $examletterReason = $_POST['exam_confirmation_letter_reason'];
        $confirmationID = $_POST['confirmation_id'];

        // Check if the exam result already exists
        if ($confirmationID) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE reg_examconfirmationletter SET status = :status, reason = :reason WHERE confirmation_id = :confirmation_id");
            $stmt->bindParam(':status', $examletterStatusOption);
            $stmt->bindParam(':reason', $examletterReason);
            $stmt->bindParam(':confirmation_id', $confirmationID);
            $stmt->execute();
            echo "Exam result updated successfully!";
        } 
        header("Location: stu_overview_reg.php"); 
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

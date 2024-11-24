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
        $receiptStatusOption = $_POST['receiptStatusOption'];
        $receiptReason = $_POST['payment_receipt_reason'];
        $invoiceID = $_POST['receipt_id'];

        // Check if the exam result already exists
        if ($invoiceID) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE reg_PaymentReceipt SET status = :status, reason = :reason WHERE receipt_id = :receipt_id");
            $stmt->bindParam(':status', $receiptStatusOption);
            $stmt->bindParam(':reason', $receiptReason);
            $stmt->bindParam(':receipt_id', $invoiceID);
            $stmt->execute();
            echo "Payment Receipt updated successfully!";
        } 
        header("Location: stu_overview_reg.php"); 
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

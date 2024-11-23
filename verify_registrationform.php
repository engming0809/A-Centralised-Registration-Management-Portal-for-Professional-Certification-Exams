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
        $invoiceStatusOption = $_POST['invoiceStatusOption'];
        $invoiceReason = $_POST['payment_invoice_reason'];
        $invoiceID = $_POST['invoice_id'];

        // Check if the exam result already exists
        if ($invoiceID) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE reg_PaymentInvoice SET status = :status, reason = :reason WHERE invoice_id = :invoice_id");
            $stmt->bindParam(':status', $invoiceStatusOption);
            $stmt->bindParam(':reason', $invoiceReason);
            $stmt->bindParam(':invoice_id', $invoiceID);
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

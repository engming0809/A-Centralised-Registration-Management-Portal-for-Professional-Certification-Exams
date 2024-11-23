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
        $transactionStatusOption = $_POST['transactionStatusOption'];
        $transactionReason = $_POST['transaction_slip_reason'];
        $transactionID = $_POST['transaction_id'];

        // Check if the exam result already exists
        if ($transactionID) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE reg_transactionslip SET status = :status, reason = :reason WHERE transaction_id = :transaction_id");
            $stmt->bindParam(':status', $transactionStatusOption);
            $stmt->bindParam(':reason', $transactionReason);
            $stmt->bindParam(':transaction_id', $transactionID);
            $stmt->execute();
            echo "Transaction Slip updated successfully!";
        } 
        header("Location: lec_overview_reg.php"); 
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

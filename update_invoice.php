<?php
$servername = 'localhost';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

$conn = new mysqli($servername, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentInvoiceId = $_POST['payment_invoice_id'];
    $action = $_POST['action'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;

    try {
        if ($action === 'accept') {
            // Update the payment invoice status to 'accept'
            $query = "UPDATE reg_paymentinvoice SET status = 'accept' WHERE invoice_id = :payment_invoice_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':payment_invoice_id', $paymentInvoiceId, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($action === 'reject') {
            // Update the payment invoice status to 'reject' and insert the reason if provided
            if ($reason) {
                // Ensure the reason is not empty
                $query = "UPDATE reg_paymentinvoice 
                          SET status = 'reject', reason = :reason 
                          WHERE invoice_id = :payment_invoice_id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
                $stmt->bindParam(':payment_invoice_id', $paymentInvoiceId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // In case the reason is empty, handle it
                echo json_encode(['message' => 'Reason is required for rejection.']);
                exit;
            }
        }

        echo json_encode(['message' => 'Payment invoice status updated successfully']);
    } catch (Exception $e) {
        // Handle any exceptions or errors
        echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
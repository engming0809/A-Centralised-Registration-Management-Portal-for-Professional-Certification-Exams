<?php
session_start();
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //////////////////////////////////// Different section for handling invoice uploads /////////////////////////////////////
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_invoice'])) {
        $invoiceId = $_POST['invoice_id'];  // Assuming this is the ID of the invoice being updated
        $registrationId = $_POST['registration_id'];  // This is the certification registration ID
        $file = $_FILES['payment_invoice'];

        // Allowed file types
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            echo "Only PDF and image files are allowed.";
            exit();
        }

        // Set upload directory and create it if it doesn't exist
        $uploadDir = 'uploads/payment_invoices/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); 
        }

        // Generate a unique file path to avoid overwriting
        $filePath = $uploadDir . uniqid('', true) . '-' . basename($file['name']);

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Check if invoice exists
            $stmt = $pdo->prepare("SELECT * FROM reg_PaymentInvoice WHERE invoice_id = :invoice_id");
            $stmt->bindParam(':invoice_id', $invoiceId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // If invoice exists, update the filepath
                $stmt = $pdo->prepare("UPDATE reg_PaymentInvoice SET filepath = :filepath, status = 'pending' WHERE invoice_id = :invoice_id");
                $stmt->bindParam(':invoice_id', $invoiceId);
                $stmt->bindParam(':filepath', $filePath);
                $stmt->execute();
                echo "Invoice updated successfully!";
            } else {
                // If invoice doesn't exist, insert a new record
                $stmt = $pdo->prepare("INSERT INTO reg_PaymentInvoice (filepath, registration_id, status) VALUES (:filepath, :registration_id, 'pending')");
                $stmt->bindParam(':filepath', $filePath);
                $stmt->bindParam(':registration_id', $registrationId);
                $stmt->execute();
                echo "Invoice uploaded successfully!";

                // Update the registration status to 'invoice_submitted'
                $stmt = $pdo->prepare("UPDATE CertificationRegistrations SET registration_status = 'invoice_submitted' WHERE registration_id = :registration_id");
                $stmt->bindParam(':registration_id', $registrationId);
                $stmt->execute();
            }
            
            $stmt = $pdo->prepare("UPDATE certificationregistrations SET notification = 1 WHERE registration_id = :registration_id");
            $stmt->bindParam(':registration_id', $registrationId);
            $stmt->execute();

            

            header("Location: lec_overview_reg.php"); 
            exit();
        } else {
            echo "Error uploading the file.";
        }
    }
    //////////////////////////////////// End of section for handling invoice uploads /////////////////////////////////////

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

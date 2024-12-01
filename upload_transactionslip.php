<?php
session_start();
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //////////////////////////////////// Different section for handling Transaction uploads /////////////////////////////////////
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['transaction_slip'])) {
        $transactionId = $_POST['transaction_id'];  // ID of the Transaction being updated
        $registrationId = $_POST['registration_id'];  // Certification registration ID
        $file = $_FILES['transaction_slip'];

        // Allowed file types
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            echo "<script>
                    alert('Only PDF and image files are allowed.');
                    window.location.href = 'lec_overview_reg.php'; 
                </script>";
            exit();
        }

        // Set upload directory and create it if it doesn't exist
        $uploadDir = 'uploads/transaction_slip/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate a unique file path to avoid overwriting
        $filePath = $uploadDir . uniqid('', true) . '-' . basename($file['name']);

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Check if the Transaction exists
            $stmt = $pdo->prepare("SELECT * FROM reg_transactionslip WHERE transaction_id = :transaction_id");
            $stmt->bindParam(':transaction_id', $transactionId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // If Transaction exists, update the filepath
                $stmt = $pdo->prepare("UPDATE reg_transactionslip SET filepath = :filepath, status = 'pending' WHERE transaction_id = :transaction_id");
                $stmt->bindParam(':transaction_id', $transactionId);
                $stmt->bindParam(':filepath', $filePath);
                $stmt->execute();
                echo "Transaction updated successfully!";
            } else {
                // If Transaction doesn't exist, insert a new record
                $stmt = $pdo->prepare("INSERT INTO reg_transactionslip (filepath, registration_id, status) VALUES (:filepath, :registration_id, 'pending')");
                $stmt->bindParam(':filepath', $filePath);
                $stmt->bindParam(':registration_id', $registrationId);
                $stmt->execute();
                echo "Transaction uploaded successfully!";

                // Update the registration status to 'invoice_submitted'
                $stmt = $pdo->prepare("UPDATE CertificationRegistrations SET registration_status = 'transaction_submitted' WHERE registration_id = :registration_id");
                $stmt->bindParam(':registration_id', $registrationId);
                $stmt->execute();
            }
            $stmt = $pdo->prepare("UPDATE certificationregistrations SET notification = 1 WHERE registration_id = :registration_id");
            $stmt->bindParam(':registration_id', $registrationId);
            $stmt->execute();
            

            header("Location: stu_overview_reg.php"); 
            exit();
        } else {
            echo "Error uploading the file.";
        }
    }
    //////////////////////////////////// End of section for handling Transaction uploads /////////////////////////////////////

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

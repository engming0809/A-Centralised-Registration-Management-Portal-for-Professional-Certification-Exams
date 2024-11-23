<?php
session_start();
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //////////////////////////////////// Different section for handling exam confirmation letter uploads /////////////////////////////////////
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['exam_confirmation_letter'])) {
        $confirmationId = $_POST['confirmation_id'];  // Assuming this is the ID of the confirmation letter being updated
        $registrationId = $_POST['registration_id'];  // This is the certification registration ID
        $file = $_FILES['exam_confirmation_letter'];

        // Allowed file types
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            echo "Only PDF and image files are allowed.";
            exit();
        }

        // Set upload directory and create it if it doesn't exist
        $uploadDir = 'uploads/exam_confirmation_letter/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate a unique file path to avoid overwriting
        $filePath = $uploadDir . uniqid('', true) . '-' . basename($file['name']);

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Check if confirmation letter exists
            $stmt = $pdo->prepare("SELECT * FROM reg_examconfirmationletter WHERE confirmation_id = :confirmation_id");
            $stmt->bindParam(':confirmation_id', $confirmationId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // If confirmation letter exists, update the filepath
                $stmt = $pdo->prepare("UPDATE reg_examconfirmationletter SET filepath = :filepath, status = 'pending' WHERE confirmation_id = :confirmation_id");
                $stmt->bindParam(':confirmation_id', $confirmationId);
                $stmt->bindParam(':filepath', $filePath);
                $stmt->execute();
                echo "Exam Confirmation Letter updated successfully!";
            } else {
                // If invoice doesn't exist, insert a new record
                $stmt = $pdo->prepare("INSERT INTO reg_examconfirmationletter (filepath, registration_id, status) VALUES (:filepath, :registration_id, 'pending')");
                $stmt->bindParam(':filepath', $filePath);
                $stmt->bindParam(':registration_id', $registrationId);
                $stmt->execute();
                echo "Exam Confirmation Letter uploaded successfully!";

                // Update the registration status to 'invoice_submitted'
                $stmt = $pdo->prepare("UPDATE CertificationRegistrations SET registration_status = 'examletter_submitted' WHERE registration_id = :registration_id");
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
    //////////////////////////////////// End of section for handling exam confirmation letter uploads /////////////////////////////////////

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

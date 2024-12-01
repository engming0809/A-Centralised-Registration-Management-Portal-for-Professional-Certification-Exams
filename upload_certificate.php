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
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['certificate'])) {
        $certificateId = $_POST['certificate_id'];  // Assuming this is the ID of the confirmation letter being updated
        $registrationId = $_POST['registration_id'];  // This is the certification registration ID
        $file = $_FILES['certificate'];

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
        $uploadDir = 'uploads/certificate/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate a unique file path to avoid overwriting
        $filePath = $uploadDir . uniqid('', true) . '-' . basename($file['name']);

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Check if confirmation letter exists
            $stmt = $pdo->prepare("SELECT * FROM reg_certificate WHERE certificate_id = :certificate_id");
            $stmt->bindParam(':certificate_id', $certificateId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // If confirmation letter exists, update the filepath
                $stmt = $pdo->prepare("UPDATE reg_certificate SET filepath = :filepath, status = 'pending' WHERE certificate_id = :certificate_id");
                $stmt->bindParam(':certificate_id', $certificateId);
                $stmt->bindParam(':filepath', $filePath);
                $stmt->execute();
                echo "Certificate updated successfully!";
            } else {
                // If invoice doesn't exist, insert a new record
                $stmt = $pdo->prepare("INSERT INTO reg_certificate (filepath, registration_id, status) VALUES (:filepath, :registration_id, 'pending')");
                $stmt->bindParam(':filepath', $filePath);
                $stmt->bindParam(':registration_id', $registrationId);
                $stmt->execute();
                echo "Certificate uploaded successfully!";

                // Update the registration status to 'invoice_submitted'
                $stmt = $pdo->prepare("UPDATE CertificationRegistrations SET registration_status = 'certificate_submitted' WHERE registration_id = :registration_id");
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

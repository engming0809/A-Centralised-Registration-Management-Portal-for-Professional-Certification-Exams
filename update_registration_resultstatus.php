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
        $regresultStatusOption = $_POST['regresultStatusOption'];
        $registration_id = $_POST['registration_id'];

        if ($registration_id) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE certificationregistrations SET result_status = :result_status WHERE registration_id = :registration_id");
            $stmt->bindParam(':result_status', $regresultStatusOption);
            $stmt->bindParam(':registration_id', $registration_id);
            $stmt->execute();
            echo "Registration Result updated successfully!";
        } 
        header("Location: lec_overview_reg.php"); 
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

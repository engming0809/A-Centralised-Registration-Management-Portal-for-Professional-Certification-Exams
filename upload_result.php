<?php
session_start();
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

require 'vendor/autoload.php'; // Include Mailjet library (install it with Composer)

use \Mailjet\Resources;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mailjet API credentials
    $mailjetApiKey = 'f3a4ec9a624b3edc801f0260aa17d9d6';
    $mailjetApiSecret = 'd87dc2068c46050e544b2111350caa8e';
    $mj = new \Mailjet\Client($mailjetApiKey, $mailjetApiSecret, true, ['version' => 'v3.1']);

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $examResult = $_POST['examResult'];
        $registrationId = $_POST['registration_id'];  // Certification registration ID
        $examResultId = isset($_POST['examresult_id']) ? $_POST['examresult_id'] : null; // Optional, for updating

        // Determine publish status based on the radio button selection
        $publish = $_POST['publish'] == 'published' ? 'published' : 'not_published';
        $examStatus = $_POST['examstatus'] ?? '';

        // Check if the exam result already exists
        if ($examResultId) {
            // Update the existing result
            $stmt = $pdo->prepare("UPDATE reg_ExamResult SET result = :result, publish = :publish, status = :status WHERE examresult_id = :examresult_id");
            $stmt->bindParam(':examresult_id', $examResultId);
            $stmt->bindParam(':result', $examResult);
            $stmt->bindParam(':publish', $publish);
            $stmt->bindParam(':status', $examStatus);
            $stmt->execute();
            echo "Exam result updated successfully!";
        } else {
            // Insert a new exam result
            $stmt = $pdo->prepare("INSERT INTO reg_ExamResult (result, registration_id, publish, status) VALUES (:result, :registration_id, :publish, :status)");
            $stmt->bindParam(':result', $examResult);
            $stmt->bindParam(':registration_id', $registrationId);
            $stmt->bindParam(':publish', $publish);
            $stmt->bindParam(':status', $examStatus);
            $stmt->execute();
            echo "Exam result submitted successfully!";

            // Update the registration status to 'result_submitted'
            $stmt = $pdo->prepare("UPDATE CertificationRegistrations SET registration_status = 'result_submitted' WHERE registration_id = :registration_id");
            $stmt->bindParam(':registration_id', $registrationId);
            $stmt->execute();
        }

        $stmt = $pdo->prepare("UPDATE certificationregistrations SET notification = 1 WHERE registration_id = :registration_id");
        $stmt->bindParam(':registration_id', $registrationId);
        $stmt->execute();

        // Check if the result is published and send an email
        if ($publish === 'published') {
            // Retrieve the student email
            $stmt = $pdo->prepare("SELECT s.email, s.full_name FROM CertificationRegistrations cr
                                   JOIN Student s ON cr.student_id = s.student_id
                                   WHERE cr.registration_id = :registration_id");
            $stmt->bindParam(':registration_id', $registrationId);
            $stmt->execute();
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                $email = $student['email'];
                $fullName = $student['full_name'];

                // Send email using Mailjet
                $body = [
                    'Messages' => [
                        [
                            'From' => [
                                'Email' => "cckiat2002@gmail.com",
                                'Name' => "Certification Registration Management Portal"
                            ],
                            'To' => [
                                [
                                    'Email' => $email,
                                    'Name' => $fullName
                                ]
                            ],
                            'Subject' => "Your Exam Result is Published",
                            'TextPart' => "Dear $fullName, your exam result has been published. Please log in to your account to view the details.",
                            'HTMLPart' => "<h3>Dear $fullName,</h3><p>Your exam result has been published. Please log in to your account to view the details.</p>"
                        ]
                    ]
                ];

                $response = $mj->post(Resources::$Email, ['body' => $body]);
                if ($response->success()) {
                    echo "Email sent successfully!";
                } else {
                    echo "Failed to send email. Error: " . $response->getReasonPhrase();
                }
            }
        }

        header("Location: lec_overview_reg.php");
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

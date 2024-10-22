<?php
// lecturer_dashboard.php

session_start();
require_once 'db_operations.php';
require_once 'file_handler.php';
require_once 'utilities.php';

// Check if the user is logged in and is a lecturer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header('Location: lecturer_dashboard.php');
    exit();
}

$db = getDatabaseConnection();

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['generate_pdf'])) {
        generatePDF($_POST['student_id']);
    } elseif (isset($_POST['upload_file'])) {
        $message = uploadFile($_POST['file_type'], $_POST['student_id']);
    } elseif (isset($_POST['update_result'])) {
        $success = updateExamResult($db, $_POST['student_id'], $_POST['exam_result']);
    }
}

// Get registered students
$students = getRegisteredStudents($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link rel="stylesheet" href="../dashboard-simple/style/style.css">
    </head>
<body>
    <h1>Lecturer Dashboard</h1>
    
    <h2>Registered Students</h2>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Registration Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
            <td><?php echo htmlspecialchars($student['name']); ?></td>
            <td><?php echo htmlspecialchars($student['registration_date']); ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                    <button type="submit" name="generate_pdf">Generate PDF</button>
                </form>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                    <select name="file_type">
                        <option value="invoice">Invoice</option>
                        <option value="receipt">Receipt</option>
                        <option value="confirmation">Exam Confirmation</option>
                        <option value="certificate">Certificate</option>
                    </select>
                    <input type="file" name="fileToUpload">
                    <button type="submit" name="upload_file">Upload</button>
                </form>
                <form method="post">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                    <input type="text" name="exam_result" placeholder="Exam Result">
                    <button type="submit" name="update_result">Update Result</button>
                </form>
                <a href="download.php?file=transaction_slip_<?php echo htmlspecialchars($student['student_id']); ?>.pdf">Download Transaction Slip</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
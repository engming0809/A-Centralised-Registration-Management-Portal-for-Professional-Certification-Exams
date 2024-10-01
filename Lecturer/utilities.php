<?php
// utilities.php

function generatePDF($studentId) {
    // You'll need to implement this function using a PDF library like FPDF
    // For now, we'll just return a message
    return "PDF generated for student ID: " . $studentId;
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function validateStudentId($studentId) {
    // Implement your validation logic here
    return is_numeric($studentId) && strlen($studentId) == 8; // Example: 8-digit student ID
}

function validateExamResult($result) {
    // Implement your validation logic here
    return is_numeric($result) && $result >= 0 && $result <= 100; // Example: 0-100 score
}
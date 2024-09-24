<?php
// db_operations.php

function getDatabaseConnection() {
    $db = new mysqli('localhost', 'username', 'password', 'certification_portal');
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    return $db;
}

function getRegisteredStudents($db) {
    $query = "SELECT * FROM registrations ORDER BY registration_date DESC";
    $result = $db->query($query);
    if (!$result) {
        die("Query failed: " . $db->error);
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

function updateExamResult($db, $studentId, $result) {
    $query = "UPDATE registrations SET exam_result = ? WHERE student_id = ?";
    $stmt = $db->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }
    $stmt->bind_param("si", $result, $studentId);
    $success = $stmt->execute();
    if (!$success) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
    return $success;
}
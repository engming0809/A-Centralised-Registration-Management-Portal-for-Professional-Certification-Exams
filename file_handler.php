<?php
// file_handler.php

function uploadFile($fileType, $studentId) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . $fileType . "_" . $studentId . "_" . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($targetFile)) {
        return "Sorry, file already exists.";
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        return "Sorry, your file is too large.";
    }

    // Allow certain file formats
    if($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx") {
        return "Sorry, only PDF, DOC & DOCX files are allowed.";
    }

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
        return "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        return "Sorry, there was an error uploading your file.";
    }
}

function downloadFile($fileName) {
    $file = "uploads/" . $fileName;
    
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        return "File not found.";
    }
}
<?php
// This file should be placed in the same directory as client_approve.php

// Basic security check
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("No file specified");
}

$file = '../' . $_GET['file'];

// Validate that the file exists and is within the uploads directory
if (!file_exists($file) || strpos($file, '../uploads/') !== 0) {
    die("File not found or invalid path");
}

// Get file extension
$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

// Set appropriate content type
switch ($extension) {
    case 'pdf':
        header('Content-Type: application/pdf');
        break;
    case 'jpg':
    case 'jpeg':
        header('Content-Type: image/jpeg');
        break;
    case 'png':
        header('Content-Type: image/png');
        break;
    default:
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
}

// Output the file
readfile($file);
exit;
?>
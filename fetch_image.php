<?php
require 'db.php';

if (isset($_GET['id'])) {
    $fileId = new MongoDB\BSON\ObjectId($_GET['id']);

    $gridFSBucket = $database->selectGridFSBucket();
    $stream = $gridFSBucket->openDownloadStream($fileId);

    if ($stream) {
        header("Content-Type: image/jpeg"); // Change based on stored image type
        fpassthru($stream);
        exit;
    } else {
        echo "Image not found.";
    }
} else {
    echo "Invalid request.";
}
?>

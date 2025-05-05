<?php
require '../db.php'; // Include database connection
require '../check_role.php';
checkRole(["admin"]);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['image'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_FILES['image'];

    // Validate image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($image['type'], $allowedTypes)) {
        $error = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
    } elseif ($image['size'] > 5 * 1024 * 1024) { // 5MB limit
        $error = "Image size exceeds 5MB.";
    } else {
        // Open the uploaded file as a stream
        $imageStream = fopen($image['tmp_name'], 'rb');

        // Upload the file to GridFS
        $fileId = $gridFSBucket->uploadFromStream($image['name'], $imageStream, [
            'metadata' => [
                'title' => $title,
                'description' => $description,
                'uploadDate' => new MongoDB\BSON\UTCDateTime()
            ]
        ]);

        fclose($imageStream);

        $message = "Image uploaded successfully. File ID: " . $fileId;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Land Photo</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background: white;
    padding: 20px;
    border: 2px solid black; /* Black border */
    border-radius: 5px;
    width: 400px;
    text-align: center;
}

h1 {
    font-size: 22px;
    color: #333;
}

form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

input, textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 14px;
}

input[type="submit"] {
    background-color: black;
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #333;
}
.message {
    color:rgb(101, 206, 52);
}
.error {
    color: red;
}
.back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            background: black;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
           
        }
        .back-btn:hover {
            background: #333;
        }
        </style>
</head>
<body>
<a href="javascript:history.back()" class="back-btn">← Back</a>
    <div class="container">
        <h1>Upload Land Photo</h1>
        <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>

            <label for="image">Select Image:</label>
            <input type="file" name="image" id="image" accept="image/*" required>

            <input type="submit" name="submit" value="Upload">
        </form>
    </div>
</body>
</html>

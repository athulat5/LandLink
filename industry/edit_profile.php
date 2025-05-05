<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../db.php';

// Check if industry user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'industry') {
    echo '<script>alert("Access Denied!"); window.location.href="/parksystem/login.html";</script>';
    exit();
}

$user_id = $_SESSION['user_id'];
$industryData = $industriesCollection->findOne(["user_id" => new MongoDB\BSON\ObjectId($user_id)]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $industry_name = trim($_POST['industry_name']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $updateFields = [
        'industry_name' => $industry_name,
        'contact' => $contact,
        'address' => $address,
    ];

    // Handle image upload
    if (!empty($_FILES['logo']['name'])) {
        $targetDir = "uploads/";
        $filename = basename($_FILES["logo"]["name"]);
        $targetFile = $targetDir . uniqid() . "_" . $filename;

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed)) {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {
                $updateFields['logo'] = $targetFile;
            } else {
                $error = "Error uploading the image.";
            }
        }
    }

    // Validate & update
    if (empty($error)) {
        $industriesCollection->updateOne(
            ["user_id" => new MongoDB\BSON\ObjectId($user_id)],
            ['$set' => $updateFields]
        );

        if (!empty($password)) {
            if ($password === $confirm_password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $usersCollection->updateOne(
                    ["_id" => new MongoDB\BSON\ObjectId($user_id)],
                    ['$set' => ['password' => $hashedPassword]]
                );
            } else {
                $error = "Passwords do not match.";
            }
        }

        if (empty($error)) {
            echo '<script>alert("Profile updated successfully!"); window.location.href="./industrydashboard.php";</script>';
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Industry Profile</title>
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
        }
        form input[type="text"], 
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #555;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
        a.back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
        .logo-preview img {
            max-width: 100px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Industry Profile</h2>

    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Industry Name:</label>
        <input type="text" name="industry_name" value="<?= htmlspecialchars($industryData['industry_name'] ?? '') ?>" required>

        <label>Contact:</label>
        <input type="text" name="contact" value="<?= htmlspecialchars($industryData['contact'] ?? '') ?>" required>

        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($industryData['address'] ?? '') ?>" required>

        <label>New Password:</label>
        <input type="password" name="password">

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password">

        <label>Upload Logo/Image:</label>
        <input type="file" name="logo" accept="image/*">

        <?php if (!empty($industryData['logo'])): ?>
            <div class="logo-preview">
                <p>Current Logo:</p>
                <img src="<?= $industryData['logo'] ?>" alt="Logo">
            </div>
        <?php endif; ?>

        <button class="btn" type="submit">Update Profile</button>
    </form>

    <a href="industrydashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>

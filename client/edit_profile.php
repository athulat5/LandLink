<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../db.php'; // MongoDB connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    echo '<script>alert("Access Denied!"); window.location.href="/parksystem/login.html";</script>';
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch existing client data
$clientData = $clientsCollection->findOne(["user_id" => new MongoDB\BSON\ObjectId($user_id)]);

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($address) || empty($phone)) {
        $error = "Please fill all the required fields.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Update client info
        $updateFields = [
            'name' => $name,
            'address' => $address,
            'phone' => $phone,
        ];

        $clientsCollection->updateOne(
            ["user_id" => new MongoDB\BSON\ObjectId($user_id)],
            ['$set' => $updateFields]
        );

        // If password is changed
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $usersCollection->updateOne(
                ["_id" => new MongoDB\BSON\ObjectId($user_id)],
                ['$set' => ['password' => $hashedPassword]]
            );
        }

        echo '<script>alert("Profile updated successfully!"); window.location.href="./clientdashboard.php";</script>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a6bff;
            --error-color: #ff4757;
            --light-color: #ffffff;
            --dark-color: #333333;
            --border-color: #e0e0e0;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: var(--light-color);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            border-top: 4px solid var(--primary-color);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary-color);
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 16px;
            transition: var(--transition);
        }

        form input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.2);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            color: var(--light-color);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }

        .btn:hover {
            background: #3a56e8;
            transform: translateY(-2px);
        }

        .error {
            color: var(--error-color);
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background-color: rgba(255, 71, 87, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--error-color);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            margin-top: 25px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-link:hover {
            color: #3a56e8;
            text-decoration: underline;
        }

        .back-link i {
            margin-right: 8px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .password-note {
            font-size: 14px;
            color: #666;
            margin-top: -15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Profile</h2>

    <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($clientData['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" value="<?= htmlspecialchars($clientData['address'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($clientData['phone'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" name="password" id="password">
            <p class="password-note">Leave blank if you don't want to change your password</p>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password">
        </div>

        <button type="submit" class="btn">Update Profile</button>
    </form>

    <a href="clientdashboard.php" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

</body>
</html>
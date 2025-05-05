<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["admin"]); // Only Admin can access this page

// Fetch staff ID from query parameter
$staffId = $_GET['id'] ?? null;

if (!$staffId) {
    echo 'Invalid staff ID.';
    exit;
}

// Convert staff ID to MongoDB ObjectId
$staffObjectId = new MongoDB\BSON\ObjectId($staffId);

// Fetch staff details
$staff = $staffCollection->findOne(['_id' => $staffObjectId]);
if (!$staff) {
    echo 'Staff member not found.';
    exit;
}

// Fetch user details
$user = $usersCollection->findOne(['_id' => $staff['user_id']]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $age = (int)$_POST["age"];
    $phone = htmlspecialchars($_POST["phone"]);
    $address = htmlspecialchars($_POST["address"]);
    $status = htmlspecialchars($_POST["status"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"] ? password_hash($_POST["password"], PASSWORD_BCRYPT) : null;

    if (!$email) {
        echo '<script>alert("Invalid email address.");</script>';
    } else {
        // Check if email already exists
        $existingUser = $usersCollection->findOne(['email' => $email, '_id' => ['$ne' => $user['_id']]]);
        if ($existingUser) {
            echo '<script>alert("Email already exists. Please use another email.");</script>';
            exit;
        }

        // Handle photo upload
        if ($_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
            $photoDir = __DIR__ . "/uploads/staffimag/";
            if (!is_dir($photoDir)) {
                mkdir($photoDir, 0777, true);
            }

            $photoName = time() . "_" . basename($_FILES["photo"]["name"]);
            $photoPath = $photoDir . $photoName;

            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath)) {
                $photoPath = "uploads/staffimag/" . $photoName;
            } else {
                echo '<script>alert("Failed to upload image!");</script>';
                exit;
            }
        } else {
            $photoPath = $staff["photo"];
        }

        // Update user details
        $updateUserFields = ['email' => $email, 'status' => $status];
        if ($password) {
            $updateUserFields['password'] = $password;
        }
        $usersCollection->updateOne(
            ['_id' => $staff['user_id']],
            ['$set' => $updateUserFields]
        );

        // Update staff details
        $staffCollection->updateOne(
            ['_id' => $staffObjectId],
            ['$set' => [
                'name' => $name,
                'age' => $age,
                'phone' => $phone,
                'address' => $address,
                'photo' => $photoPath
            ]]
        );

        echo '<script>alert("Staff details updated successfully!"); window.location.href="staffView.php";</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff Details</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        color: #343a40;
        margin: 0;
        padding: 0;
        line-height: 1.6;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        background: #ffffff;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
        color:rgb(4, 7, 10);
    }

    input, textarea, select, button {
        width: 100%;
        margin-bottom: 15px;
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        box-sizing: border-box;
    }

    input:focus, textarea:focus, select:focus {
        border-color:rgb(4, 6, 8);
        outline: none;
        box-shadow: 0 0 4px rgba(2, 7, 12, 0.25);
    }

    textarea {
        resize: vertical;
    }

    button {
        background-color:rgb(10, 10, 10);
        color: #fff;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }

    button:hover {
        background-color:rgb(4, 7, 10);
    }

    img {
        display: block;
        margin: 0 auto 10px;
        max-width: 100px;
        border-radius: 50%;
        border: 2px solid #ced4da;
    }

    @media (max-width: 768px) {
        .container {
            width: 90%;
            padding: 15px;
        }

        h2 {
            font-size: 20px;
        }

        input, textarea, select, button {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 10px;
        }

        h2 {
            font-size: 18px;
        }

        input, textarea, select, button {
            font-size: 12px;
            padding: 10px;
        }
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
        <h2>Edit Staff Details</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($staff['name']) ?>" required placeholder="Name">
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="Email">
            <input type="number" name="age" value="<?= htmlspecialchars($staff['age']) ?>" required placeholder="Age">
            <input type="text" name="phone" value="<?= htmlspecialchars($staff['phone']) ?>" required placeholder="Phone">
            <textarea name="address" required placeholder="Address"> <?= htmlspecialchars($staff['address']) ?></textarea>
            <input type="password" name="password" placeholder="New Password (Optional)">
            <select name="status" required>
                <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
            <input type="file" name="photo" accept="image/*">
            <?php if ($staff['photo']): ?>
                <img src="<?= htmlspecialchars($staff['photo']) ?>" alt="Current Photo" width="100">
            <?php endif; ?>
            <button type="submit">Update Staff</button>
        </form>
    </div>
</body>
</html>

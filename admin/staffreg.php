<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["admin"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = "staff"; 
    $name = $_POST["name"];
    $age = $_POST["age"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $status = $_POST["status"]; 

    // Check if email already exists
    $existingUser = $usersCollection->findOne(["email" => $email]);
    if ($existingUser) {
        echo '<script>alert("Email already exists! Please use a different email.");</script>';
    } else {
        // Handle file upload
        $photoDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "staffimag" . DIRECTORY_SEPARATOR;

        // Create directory if it doesn't exist
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


        // Insert into users collection
        $userInsertResult = $usersCollection->insertOne([
            "email" => $email,
            "password" => $password,
            "role" => $role,
            "status" => $status 
        ]);

        if ($userInsertResult->getInsertedCount() > 0) {
            $userId = $userInsertResult->getInsertedId();

            // Insert into staff collection
            $staffInsertResult = $staffCollection->insertOne([
                "user_id" => $userId,
                "name" => $name,
                "age" => $age,
                "phone" => $phone,
                "address" => $address,
                "photo" => $photoPath
            ]);

            if ($staffInsertResult->getInsertedCount() > 0) {
                echo '<script>alert("Staff Registered Successfully!"); window.location.href="admin_dashboard.php";</script>';
            } else {
                echo '<script>alert("Error storing staff details!");</script>';
            }
        } else {
            echo '<script>alert("Error registering user!");</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Staff</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #000000, #444444);
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 400px;
            animation: fadeIn 1s ease-in-out;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        input, textarea, select {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: 0.3s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #000;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        textarea {
            resize: none;
            height: 80px;
        }
        button {
            background: black;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: 0.3s;
        }
        button:hover {
            background: #333;
            transform: scale(1.05);
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
            transition: background 0.3s;
        }
        .back-btn:hover {
            background: #333;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        function validateForm() {
            let email = document.forms["staffForm"]["email"].value;
            let phone = document.forms["staffForm"]["phone"].value;
            let age = document.forms["staffForm"]["age"].value;
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let phoneRegex = /^[0-9]{10}$/;
            let ageRegex = /^[1-9][0-9]?$/;

            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }
            if (!phoneRegex.test(phone)) {
                alert("Please enter a valid 10-digit phone number.");
                return false;
            }
            if (!ageRegex.test(age)) {
                alert("Please enter a valid age (between 1-99).");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<a href="javascript:history.back()" class="back-btn">← Back</a>
    <div class="container">
        <h2>Register Staff</h2>
        <form name="staffForm" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="number" name="age" placeholder="Age" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <textarea name="address" placeholder="Address" required></textarea>
            <input type="file" name="photo"  accept=".jpg,.jpeg,.png" required>
            <select name="status" required>
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button type="submit">Register Staff</button>
        </form>
    </div>
</body>
</html>

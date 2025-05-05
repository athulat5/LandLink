<?php
require '../db.php'; // MongoDB connection


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $industryName = $_POST["industry_name"];
    $landArea = $_POST["land_area"];
    $contact = $_POST["contact"];
    $address = $_POST["address"];
    $description = $_POST["description"];
    $status = "inactive"; // Default status
    $approvel_status = "pending"; // Default status
    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");
    

    // Check if email already exists
    $existingUser = $usersCollection->findOne(["email" => $email]);
    if ($existingUser) {
        echo '<script>alert("Email already exists! Please use a different email.");</script>';
    } else {
        // Handle logo upload
        $logoDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "logos" . DIRECTORY_SEPARATOR;
        if (!is_dir($logoDir)) {
            mkdir($logoDir, 0777, true);
        }
        $logoName = time() . "_" . basename($_FILES["logo"]["name"]);
        $logoPath = $logoDir . $logoName;
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $logoPath)) {
            $logoPath = "uploads/logos/" . $logoName;
        } else {
            echo '<script>alert("Failed to upload logo!");</script>';
            exit;
        }

        // Handle certificate upload
        $certificateDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "certificates" . DIRECTORY_SEPARATOR;
        if (!is_dir($certificateDir)) {
            mkdir($certificateDir, 0777, true);
        }
        $certificateName = time() . "_" . basename($_FILES["certificate"]["name"]);
        $certificatePath = $certificateDir . $certificateName;
        if (move_uploaded_file($_FILES["certificate"]["tmp_name"], $certificatePath)) {
            $certificatePath = "uploads/certificates/" . $certificateName;
        } else {
            echo '<script>alert("Failed to upload certificate!");</script>';
            exit;
        }

        // Insert into users collection
        $userInsertResult = $usersCollection->insertOne([
            "email" => $email,
            "password" => $password,
            "role" => "industry",
            "status" => $status
        ]);

        if ($userInsertResult->getInsertedCount() > 0) {
            $userId = $userInsertResult->getInsertedId();

            // Insert into industries collection
            $industryInsertResult = $industriesCollection->insertOne([
                "user_id" => $userId,
                "industry_name" => $industryName,
                "land_area" => $landArea,
                "contact" => $contact,
                "address" => $address,
                "description" => $description,
                "logo" => $logoPath,
                "certificate" => $certificatePath,
                "created_at" => $created_at, 
                "updated_at" => $updated_at
            ]);

            if ($industryInsertResult->getInsertedCount() > 0) {
                echo '<script>alert("Industry Registered Successfully!!! and Waite For Approval to Login"); window.location.href="../home.php";</script>';
            } else {
                echo '<script>alert("Error storing industry details!");</script>';
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
    <title>Industry Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --light-color: #ffffff;
            --dark-color: #1a1a1a;
            --border-color: #e0e0e0;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            background: var(--primary-color);
            color: var(--light-color);
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: var(--shadow);
            border: 2px solid var(--primary-color);
        }

        .back-btn:hover {
            background: var(--light-color);
            color: var(--primary-color);
            transform: translateX(-5px);
        }

        form {
            background: var(--light-color);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 500px;
            margin-top: 40px;
            border-top: 4px solid var(--accent-color);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--primary-color);
            font-size: 1.8rem;
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
            background-color: var(--accent-color);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
            color: var(--primary-color);
        }

        input, textarea, select {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: var(--transition);
        }

        input:focus, textarea:focus, select:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: -10px;
            margin-bottom: 15px;
            display: block;
        }

        button {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            margin-top: 20px;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        input[type="file"] {
            padding: 10px;
            background: #f8f9fa;
            border: 1px dashed var(--border-color);
        }

        @media (max-width: 600px) {
            form {
                padding: 30px 20px;
                margin-top: 60px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .back-btn {
                top: 15px;
                left: 15px;
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <a href="../home.php" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <form id="registrationForm" method="POST" enctype="multipart/form-data">
        <h2>Industry Registration</h2>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <span id="passwordError" class="error"></span>

        <label for="industry_name">Industry Name:</label>
        <input type="text" id="industry_name" name="industry_name" required>

        <label for="land_area">Land Area Required (in sq ft):</label>
        <input type="number" id="land_area" name="land_area" required>

        <label for="contact">Contact Number:</label>
        <input type="tel" id="contact" name="contact" required>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required></textarea>

        <label for="description">Industry Description:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="logo">Upload Logo:</label>
        <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png" required>

        <label for="certificate">Upload Industry Certificate:</label>
        <input type="file" id="certificate" name="certificate" accept=".pdf,.doc,.docx" required>

        <button type="submit">Register</button>
    </form>

    <script>
        const form = document.getElementById('registrationForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordError = document.getElementById('passwordError');

        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                passwordError.textContent = 'Passwords do not match.';
                return false;
            } else {
                passwordError.textContent = '';
                return true;
            }
        }

        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);

        form.addEventListener('submit', function(event) {
            if (!validatePassword()) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
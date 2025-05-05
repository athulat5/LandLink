<?php
require '../db.php'; // Connect to MongoDB

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = "client";
    $status = "inactive";
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Password match validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists in USERS collection
        $existingUser = $usersCollection->findOne(['email' => $email]);
        if ($existingUser) {
            $error = "Email already registered.";
        } else {
            // Handle file upload first
            if (isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] === 0) {
                $targetDir = "uploads/id_proofs/";
                
                // Create directory if it doesn't exist
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true); // Create directory with full permissions
                }
            
                $fileName = uniqid() . "_" . basename($_FILES['id_proof']['name']);
                $targetFilePath = $targetDir . $fileName;
            
                // Check file type (allow only images and PDFs)
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                if (in_array($fileType, ['jpg', 'jpeg', 'png', 'pdf'])) {
                    if (move_uploaded_file($_FILES['id_proof']['tmp_name'], $targetFilePath)) {
                        // File uploaded successfully, now create user and client
                        
                        // Hash password
                        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                        
                        // Insert user first
                        $userInsertResult = $usersCollection->insertOne([
                            "email" => $email,
                            "password" => $hashedPassword,
                            "role" => $role,
                            "status" => $status
                        ]);
                
                        if ($userInsertResult->getInsertedCount() > 0) {
                            $userId = $userInsertResult->getInsertedId();
                            
                            // Insert client with reference to user
                            $clientsCollection->insertOne([
                                "user_id" => $userId,  // Reference to user document
                                "name" => $name,
                                "email" => $email,  // Duplicate email for easy querying
                                "phone" => $phone,
                                "address" => $address,
                                "id_proof" => $targetFilePath,
                                "approval_status" => "Pending",
                                "created_at" => new UTCDateTime()
                            ]);
                            
                            $success = "Registration successful! Wait for approval.";
                        } else {
                            $error = "Failed to create user account.";
                        }
                    } else {
                        $error = "File upload failed.";
                    }
                } else {
                    $error = "Invalid file type. Only JPG, PNG, and PDF allowed.";
                }
            } else {
                $error = "ID Proof is required.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Registration</title>
    <style>
       body { 
    font-family: Arial, sans-serif; 
    background: linear-gradient(to right,rgb(176, 178, 180),rgb(56, 57, 58)); 
    color: #333; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    height: 100vh; 
    margin: 0; 
}

.container { 
    width: 100%; 
    max-width: 400px; 
    background: #fff; 
    padding: 25px; 
    border-radius: 10px; 
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); 
    text-align: center; 
}

h2 { 
    color: #333; 
    margin-bottom: 15px; 
}

input, button , textarea { 
    width: calc(100% - 20px); 
    padding: 12px; 
    margin: 8px 0; 
    border: 1px solid #ccc; 
    border-radius: 8px; 
    font-size: 16px; 
}

input:focus { 
    border-color: #2a5298; 
    outline: none; 
    box-shadow: 0px 0px 5px rgba(42, 82, 152, 0.5); 
}

button { 
    background: #28a745; 
    color: white; 
    font-weight: bold; 
    border: none; 
    cursor: pointer; 
    transition: 0.3s ease; 
}

button:hover { 
    background: #218838; 
    transform: scale(1.05); 
}

.error { 
    color: red; 
    font-size: 14px; 
    margin-bottom: 10px; 
}

.success { 
    color: green; 
    font-size: 14px; 
    font-weight: bold; 
    margin-bottom: 10px; 
}

p { 
    margin-top: 10px; 
    font-size: 14px; 
}

a { 
    color: #2a5298; 
    text-decoration: none; 
    font-weight: bold; 
}

.upload {  
    color:rgb(65, 65, 65); 
}
.upload p { 
    font-size: 13px; 
    margin-top: 0px; 
    margin-left: 6px;
    text-align: left;
    
}
a:hover { 
    text-decoration: underline; 
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


    </style>
</head>
<body>

<a href="../home.php" class="back-btn">← Back</a>
<div class="container">
    <h2>Client Registration</h2>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validatePasswords()">
        <input type="text" name="name" required placeholder="Full Name">
        <input type="email" name="email" required placeholder="Email">
        <input type="text" name="phone" required placeholder="Phone">
        <textarea name="address" required placeholder="Address" rows="3"></textarea>
        <input type="password" id="password" name="password" required placeholder="Password">
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password">
        <input type="file" name="id_proof" required accept="image/*,application/pdf" placeholder="ID Proof">
        <div class="upload"><p>Upload your ID proof (JPG, PNG, PDF)</p></div>
        <button type="submit">Register</button>
    </form>

    <p>Already registered? <a href="../login.html">Login here</a></p>
</div>

<script>
    function validatePasswords() {
        let password = document.getElementById("password").value;
        let confirmPassword = document.getElementById("confirm_password").value;
        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return false;
        }
        return true;
    }
</script>

</body>
</html>
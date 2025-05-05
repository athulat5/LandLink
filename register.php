<?php
require 'db.php'; // Include MongoDB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $role = $_POST["role"];

    // Check if email already exists
    $existingUser = $usersCollection->findOne(["email" => $email]);
    if ($existingUser) {
        echo "Email already registered!";
        exit;
    }

    // Insert user into database
    $insertResult = $usersCollection->insertOne([
        "name" => $name,
        "email" => $email,
        "password" => $password,
        "role" => $role,
        "createdAt" => new MongoDB\BSON\UTCDateTime()
    ]);

    if ($insertResult->getInsertedCount() > 0) {
        echo "User registered successfully!";
    } else {
        echo "Registration failed!";
    }
}
?>

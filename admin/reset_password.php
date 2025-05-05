<?php
require '../db.php';
require '../check_role.php';
checkRole(["admin"]);

use MongoDB\BSON\ObjectId;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // You can customize this logic — reset password or send email, etc.
    $newPassword = password_hash("default123", PASSWORD_DEFAULT);

    $updateResult = $usersCollection->updateOne(
        ['_id' => new ObjectId($userId)],
        ['$set' => ['password' => $newPassword]]
    );

    header("Location: viewindustry.php?reset=success");
    exit;
}

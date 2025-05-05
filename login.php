<?php
require 'db.php'; // Include MongoDB connection
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $user = $usersCollection->findOne(["email" => $email]);

    if ($user && password_verify($password, $user["password"])) {
        // Check status (only staff can log in if inactive)
        if ($user["role"] !== "admin" && isset($user["status"]) && $user["status"] !== "active") {
            echo '<script type="text/javascript">
                    alert("Your account is inactive. Please contact support.");
                    window.location.href = "login.html";
                  </script>';
            exit;
        }

        // Set session and redirect based on role
        $_SESSION["user_id"] = (string) $user["_id"];
        $_SESSION["role"] = $user["role"];

        switch ($user["role"]) {
            case "admin":
                header("Location: admin/admin_dashboard.php");
                break;
            case "staff":
                header("Location: staff/staffdashboard.php");
                break;
            case "industry":
                header("Location: industry/industrydashboard.php");
                break;
            case "client":
                header("Location: client/clientdashboard.php");
                break;
            default:
                echo "Invalid role!";
        }
        exit;
    } else {
        echo '<script type="text/javascript">
                alert("Invalid email or password!");
                window.location.href = "login.html";
              </script>';
        exit;
    }
}
?>

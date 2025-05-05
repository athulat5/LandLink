<?php
require '../check_role.php';
require '../db.php'; // MongoDB connection
checkRole(["industry"]);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;

// Initialize industry data
$industryName = 'N/A';
$industryContact = 'N/A';
$industryPhone = 'N/A';
$industryAddress = 'N/A';

try {
    if ($user_id) {
        // Find the industry by user_id
        $industryData = $industriesCollection->findOne([
            "user_id" => new MongoDB\BSON\ObjectId($user_id)
        ]);

        if ($industryData) {
            $industryName = $industryData['industry_name'] ?? 'N/A';
            $industryContact = $industryData['contact'] ?? 'N/A';
            $industryPhone = $industryData['phone'] ?? 'N/A';
            $industryAddress = $industryData['address'] ?? 'N/A';
        }
    }
} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industry Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            padding: 50px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            justify-items: center; 
            gap: 10px;
            margin-left: 280px; 
        }

        .box {
            background: white;
            padding: 60px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .box:hover {
            background: #ddd;
            transform: scale(1.05);
        }

        .sidebar { 
            width: 250px; 
            background: black; 
            color: white; 
            padding: 20px; 
            height: 100vh; 
            position: fixed; 
            top: 0; 
            left: 0; 
            text-align: center; 
        }

        .count-box, .count-box1 { 
            background: white; 
            color: black; 
            padding: 10px; 
            border-radius: 8px; 
            font-size: 15px; 
            font-weight: bold; 
            margin-top: 20px; 
            animation: fadeIn 1s ease-in-out; 
            cursor: pointer;
        }

        .count-box1 {
            background: rgb(221, 42, 42);
            color: white;
        }

        .count-box:hover, .count-box1:hover {
            transform: scale(1.05);
        }

        .header {
            color: black;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-left: 280px;
        }

        .user-info {
            background: linear-gradient(to right, #ffffff, #f9f9f9);
            padding: 30px;
            border-radius: 15px;
            margin: 30px 50px 20px 280px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .user-info:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .user-info h3 {
            font-size: 26px;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }

        .user-info p {
            margin: 10px 0;
            font-size: 18px;
            color: #555;
            line-height: 1.6;
        }

        .user-info p strong {
            color: #000;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Industry Dashboard</h1>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <div class="count-box" onclick="window.location.href='edit_profile.php'">👥 Account</div>
        <div class="count-box" onclick="window.location.href='addproduct.php'">➕ Add Product</div>
        <div class="count-box" onclick="window.location.href='manageproduct.php'">👥 Manage Product</div>
        <div class="count-box" onclick="window.location.href='raw_materials_list.php'">🛒 Buy Materials</div>
        <div class="count-box" onclick="window.location.href='industry_orders.php'">📊 View Product Orders</div> 
        <div class="count-box" onclick="window.location.href='view_raw_orders.php'">📊 My Orders</div> 
        <div class="count-box" onclick="window.location.href='industry_support.php'">💬 Messages</div> 
        <div class="count-box1" onclick="window.location.href='/parksystem/logout.php'">🔒 Sign Out</div> 
    </div>

    <!-- Industry Info Section -->
    <div class="user-info">
        <h3>Industry Info</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($industryName) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($industryContact) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($industryAddress) ?></p>
    </div>

    <!-- Optional Summary Section -->
    <!-- 
    <div class="container">
        <div class="box">🛒 Materials Ordered: N/A</div>
        <div class="box">📦 Products Listed: N/A</div>
    </div>
    -->

</body>
</html>

<?php
session_start(); // Start the session to access $_SESSION variables
error_reporting(E_ALL); // Enable error reporting
ini_set('display_errors', 1); // Display errors on the screen

function checkRole($allowedRoles) {
    if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], $allowedRoles)) {
        echo '<script>alert("Access Denied!!.."); window.location.href="/parksystem/login.html";</script>';
        exit();
    }
}

require '../db.php'; // MongoDB connection
checkRole(["client"]); // Ensure the logged-in user is a client

// Fetch the logged-in user's ID from the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Initialize client details with default values
$clientName = 'N/A';
$clientEmail = 'N/A';
$clientPhone = 'N/A';
$clientAddress = 'N/A';

// Initialize cart and order counts
$cartItemsCount = 0;
$ordersCount =  0;

try {
    if ($user_id) {
        // Fetch the user data from the 'users' collection
        $userData = $usersCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($user_id)]);
        
        if ($userData) {
            // Fetch the client data from the 'client' collection
            $clientData = $clientsCollection->findOne(["user_id" => new MongoDB\BSON\ObjectId($user_id)]);
            
            if ($clientData) {
                // Update client details if data exists
                $clientName = $clientData['name'] ?? 'N/A';
                $clientEmail = $clientData['email'] ?? 'N/A';
                $clientPhone = $clientData['phone'] ?? 'N/A';
                $clientAddress = $clientData['address'] ?? 'N/A';
            }
            $ordersCount = $ordersCollection->countDocuments([
                "client_id" => new MongoDB\BSON\ObjectId($user_id) // Changed from user_id to client_id if needed
            ]);
        }
    }
} catch (MongoDB\Driver\Exception\Exception $e) {
    // Handle MongoDB exceptions
    die("Error accessing database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #f8f9fa;
            --accent-color: #ff6b6b;
            --dark-color: #333;
            --light-color: #fff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            padding: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-left: 280px;
            margin-right: 30px;
        }

        .box {
            background: var(--light-color);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border-left: 4px solid var(--primary-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
        }

        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
            background-color: #f0f4ff;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #3a4b8c, #2c3e50);
            color: white;
            padding: 25px 15px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            text-align: center;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            color: var(--light-color);
            margin-bottom: 30px;
            font-size: 1.5rem;
            position: relative;
            padding-bottom: 10px;
        }

        .sidebar h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background-color: var(--accent-color);
        }

        .header {
            background: var(--light-color);
            color: var(--dark-color);
            padding: 25px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            margin-left: 280px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 1;
        }

        .user-info {
            background: var(--light-color);
            padding: 30px;
            border-radius: 12px;
            margin: 30px 50px 20px 280px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border-top: 3px solid var(--primary-color);
        }

        .user-info:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .user-info h3 {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .user-info p {
            margin: 12px 0;
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
        }

        .user-info p strong {
            color: var(--dark-color);
            font-weight: 600;
            min-width: 100px;
            display: inline-block;
        }

        .count-box {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light-color);
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            margin-top: 15px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .count-box:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .count-box1 {
            background: var(--accent-color);
            color: var(--light-color);
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            margin-top: 20px;
            margin-bottom: 30px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .count-box1:hover {
            background: #ff5252;
            transform: translateX(5px);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .box i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .emoji {
            font-size: 1.5rem;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Client Dashboard</h1>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <div class="count-box" onclick="window.location.href='edit_profile.php'">
            <i class="bi bi-person"></i> Account
        </div>
        <div class="count-box" onclick="window.location.href='view_product.php'">
            <i class="bi bi-cart-plus"></i> Buy Product
        </div>
        <div class="count-box" onclick="window.location.href='view_ind.php'">
            <i class="bi bi-building"></i> Companies
        </div>
        <div class="count-box" onclick="window.location.href='order_history.php'">
            <i class="bi bi-list-check"></i> View Orders
        </div> 
        <div class="count-box1" onclick="window.location.href='/parksystem/logout.php'">
            <i class="bi bi-box-arrow-right"></i> Sign Out
        </div> 
    </div>

    <!-- Client Info Section -->
    <div class="user-info">
        <h3>Client Information</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($clientName) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($clientEmail) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($clientPhone) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($clientAddress) ?></p>
    </div>

    <!-- Orders & Cart Summary Section -->
    <div class="container">
        <div class="box" onclick="window.location.href='order_history.php'">
            <i class="bi bi-box-seam"></i>
            <div>Orders: <?= $ordersCount > 0 ? $ordersCount : 'N/A' ?></div>
        </div>
        <div class="box">
            <i class="bi bi-shield-check"></i>
            <div>Account Status: Active</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
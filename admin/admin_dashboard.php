<?php
require dirname(__DIR__) . '/check_role.php';
require __DIR__ . '/../db.php'; // MongoDB connection
checkRole(["admin"]);

$staffCount = $staffCollection->countDocuments();
$industryCount = $industriesCollection->countDocuments(['approval_status' => 'approved']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            top: 0; left: 0; 
            text-align: center; 
        }
        .count-box { 
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
        .header {
            color: black;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-left: 280px;
        }

        .count-box1 { 
            background:rgb(221, 42, 42); 
            color: black; 
            padding: 10px; 
            border-radius: 8px; 
            font-size: 15px; 
            font-weight: bold; 
            margin-top: 20px;
            margin-bottom: 30px; 
            animation: fadeIn 1s ease-in-out; 
            cursor: pointer;
        }
        .count-box:hover, .count-box1:hover {
            
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <div class="count-box" onclick="window.location.href='staffreg.php'">➕ Add Staff</div>
        <div class="count-box" onclick="window.location.href='staffView.php'">👥 Manage Staff</div>
        <div class="count-box" onclick="window.location.href='industry_approve.php'">✅ Approve Industry</div>
        <div class="count-box" onclick="window.location.href='viewindustry.php'">🏭 View Industry</div>
        <div class="count-box" onclick="window.location.href='view_materials.php'">🛒 View Materials</div>
        <div class="count-box" onclick="window.location.href='view_orders.php'">📊 View Orders</div>
        <div class="count-box" onclick="window.location.href='admin_support.php'">💬 Messages</div>  
        <div class="count-box" onclick="window.location.href='land.php'">🌆 Add LandPhoto</div>
        <div class="count-box1" onclick="window.location.href='../logout.php'">🔒 Sign Out</div> 

    </div>
   

    <div class="container">
        <div class="box">👨🏻‍💼 Staff: <?= $staffCount > 0 ? $staffCount : 'N/A' ?></div>
        <div class="box">🏭 Industry: <?= $industryCount > 0 ? $industryCount : 'N/A' ?></div>
        <div class="box">👨🏻‍💼 Client: N/A</div>
    </div>

</body>
</html>

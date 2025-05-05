<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["admin"]); // Only Admin can access this page

// Ensure collections are properly initialized
$staffCollection = $database->staff; 
$usersCollection = $database->users;

$staffList = $staffCollection->find([]); // Fetch all staff
$totalStaff = $staffCollection->countDocuments(); // Get total staff count
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff List</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 20px; display: flex; }
        
        /* Sidebar */
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
            padding: 15px; 
            border-radius: 8px; 
            font-size: 20px; 
            font-weight: bold; 
            margin-top: 20px; 
            animation: fadeIn 1s ease-in-out; 
        }

        /* Main Content */
        .content { 
            margin-left: 270px;
            width: calc(100% - 270px); 
        }
        h2 { text-align: center; }

        /* Table Styling */
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: black; color: white; }
        img { width: 50px; height: 50px; border-radius: 5px; }
        .edit-btn { 
            background: black; 
            color: white; 
            padding: 5px 10px; 
            text-decoration: none; 
            border-radius: 5px; 
        }
        .edit-btn:hover { background: #333; }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <div class="count-box">👨🏻‍💼 Total Staff: <?= $totalStaff ?></div>
        <div class="count-box" onclick="window.location.href='admin_dashboard.php'" style="cursor: pointer;">🏠 Home</div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Staff List</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Address</th>
                <th>Email</th>
                <th>Photo</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($staffList as $staff): ?>
                <?php
                    $user = $usersCollection->findOne(["_id" => $staff["user_id"]]);
                    $email = $user["email"] ?? "N/A";
                    $status = $user["status"] ?? "N/A";
                    $photo = !empty($staff["photo"]) ? htmlspecialchars($staff["photo"]) : 'default.jpg';
                ?>
                <tr>
                    <td><?= htmlspecialchars($staff["name"]) ?></td>    
                    <td><?= htmlspecialchars($staff["age"]) ?></td>
                    <td><?= htmlspecialchars($staff["address"]) ?></td>
                    <td><?= htmlspecialchars($email) ?></td>
                    <td><img src="<?= $photo ?>" alt="Photo"></td>
                    <td><?= htmlspecialchars($status) ?></td>
                    <td><a class="edit-btn" href="../staff/staffedit.php?id=<?= $staff['_id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>

<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["staff"]);

// Fetch all clients
$clients = $clientsCollection->find()->toArray();


// Handle Active/Inactive action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["set_status"])) {
    $clientId = new MongoDB\BSON\ObjectId($_POST["client_id"]);
    $client = $clientsCollection->findOne(["_id" => $clientId]);
    
    // Check if client exists and has a user_id
    if (isset($client["user_id"])) {
        $userId = $client["user_id"];
        
        // Get the current user status
        $user = $usersCollection->findOne(["_id" => $userId]);
        $currentStatus = $user['status'] ?? 'inactive';
        
        // Toggle the status
        $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

        // Update the user status
        $usersCollection->updateOne(
            ["_id" => $userId],
            ['$set' => ["status" => $newStatus]]
        );
        
        echo '<script>alert("Client status updated successfully."); window.location.href="manage_client.php";</script>';
    }
}

// Handle Delete Client action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_client"])) {
    $clientId = new MongoDB\BSON\ObjectId($_POST["client_id"]);

    // Fetch client details
    $client = $clientsCollection->findOne(["_id" => $clientId]);
    
    if ($client) {
        // Check if client has a user_id
        if (isset($client["user_id"])) {
            $userId = $client["user_id"];
            
            // Delete related user document
            $usersCollection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($userId)]);
        }
        
        // Delete client document
        $clientsCollection->deleteOne(["_id" => $clientId]);
        
        echo '<script>alert("Client and related user deleted successfully."); window.location.href="manage_client.php";</script>';
    } else {
        echo '<script>alert("Client not found."); window.location.href="manage_client.php";</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients</title>
    <style>/* General Body Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fc;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    color: #333;
    margin-top: 20px;
}

/* Back Button */
.back-btn {
    display: inline-block;
    padding: 10px 20px;
    margin: 20px;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

.back-btn:hover {
    background-color: #0056b3;
}

/* Table Styling */
table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #007bff;
    color: white;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #f1f1f1;
}

table a {
    color: #007bff;
    text-decoration: none;
}

table a:hover {
    text-decoration: underline;
}

/* Action Buttons */
button {
    padding: 6px 12px;
    margin: 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

button:hover {
    opacity: 0.8;
}

.status {
    background-color: #28a745;
    color: white;
}

.status:hover {
    background-color: #218838;
}

.delete {
    background-color: #dc3545;
    color: white;
}

.delete:hover {
    background-color: #c82333;
}

/* Input fields and hidden inputs */
input[type="hidden"] {
    display: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    table {
        width: 100%;
        margin: 10px;
    }

    table th, table td {
        padding: 8px;
        font-size: 12px;
    }

    button {
        font-size: 12px;
    }

    .back-btn {
        font-size: 12px;
        padding: 8px 16px;
    }
}
    </style>

</head>
<body>
<a href="../staff/staffdashboard.php" class="back-btn">← Back</a>

<h2>Manage Clients</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>ID Proof</th>
            <th>Approval Status</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clients as $client): ?>
            <tr>
                <td><?php echo htmlspecialchars($client['name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($client['email'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($client['phone'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($client['address'] ?? 'N/A'); ?></td>
                <td>
                    <?php if (!empty($client['id_proof']) && file_exists($client['id_proof'])): ?>
                        <a href="<?php echo htmlspecialchars($client['id_proof']); ?>" target="_blank">View</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?php echo ucfirst($client['approval_status'] ?? 'Pending'); ?></td>

                <?php
                // Get user status from the user table
                $userId = $client['user_id'] ?? null;
                $userStatus = 'N/A';
                if ($userId) {
                    $user = $usersCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($userId)]);
                    $userStatus = $user['status'] ?? 'inactive';
                }
                ?>
                <td><?php echo ucfirst($userStatus); ?></td>

                <td>
                    

                    <!-- Set Active/Inactive Button -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="client_id" value="<?php echo $client['_id']; ?>">
                        <input type="hidden" name="status" value="<?php echo $userStatus; ?>">
                        <button type="submit" name="set_status" class="status"><?php echo $userStatus === 'active' ? 'Deactivate' : 'Activate'; ?></button>
                    </form>

                    <!-- Delete Button -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="client_id" value="<?php echo $client['_id']; ?>">
                        <button type="submit" name="delete_client" class="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>

<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["staff"]);

// Fetch all clients
$clients = $clientsCollection->find()->toArray();

// Handle Approve/Reject actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["client_id"]) && isset($_POST["action"])) {
        $clientId = new MongoDB\BSON\ObjectId($_POST["client_id"]);
        $action = $_POST["action"];

        // Find the client document
        $client = $clientsCollection->findOne(["_id" => $clientId]);

        if ($client) {
            $userId = $client["user_id"] ?? null; // Get the ObjectId of the user

            if ($userId) {
                // $userId is already an ObjectId object from MongoDB
                
                if ($action === "approve") {
                    $newUserStatus = "active";
                    $newApprovalStatus = "approved";
                } else {
                    $newUserStatus = "inactive";
                    $newApprovalStatus = "rejected";
                }

                // Update user collection status
                $usersCollection->updateOne(
                    ["_id" => $userId],
                    ['$set' => ["status" => $newUserStatus]]
                );

                // Update client collection approval_status
                $clientsCollection->updateOne(
                    ["_id" => $clientId],
                    ['$set' => ["approval_status" => $newApprovalStatus]]
                );

                echo '<script>alert("Client status updated successfully."); window.location.href="client_approve.php";</script>';
            } else {
                echo '<script>alert("User ID not found in client collection.");</script>';
            }
        } else {
            echo '<script>alert("Client not found.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff- Approve Clients</title>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #f4f6f8;
}

h2 {
    text-align: center;
    color: #333;
}

.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    color: #3498db;
    font-weight: bold;
    font-size: 16px;
}

.back-btn:hover {
    text-decoration: underline;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: center;
}

th {
    background-color: #3498db;
    color: #ffffff;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

.approve, .reject {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
}

.approve {
    background-color: #2ecc71;
    color: white;
}

.approve:hover {
    background-color: #27ae60;
}

.reject {
    background-color: #e74c3c;
    color: white;
}

.reject:hover {
    background-color: #c0392b;
}

a {
    color: #2980b9;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
/* Status color classes */
.status-approved {
    color: #2ecc71; /* Green */
    font-weight: bold;
}

.status-rejected {
    color: #e74c3c; /* Red */
    font-weight: bold;
}

.status-pending {
    color: #f39c12; /* Orange */
    font-weight: bold;
}

    </style>
</head>
<body>
<a href="../staff/staffdashboard.php" class="back-btn">← Back</a>

<h2>Approve Clients</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>ID Proof</th>
            <th>Approval Status</th>
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
            <?php
                $status = strtolower($client['approval_status'] ?? 'pending');
                $statusClass = 'status-' . $status;
            ?>
            <td class="<?php echo $statusClass; ?>">
                <?php echo ucfirst($status); ?>
            </td>
            <td>
                <?php if ($client['approval_status'] === 'Pending'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="client_id" value="<?php echo $client['_id']; ?>">
                        <button type="submit" name="action" value="approve" class="approve">Approve</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="client_id" value="<?php echo $client['_id']; ?>">
                        <button type="submit" name="action" value="reject" class="reject">Reject</button>
                    </form>
                <?php else: ?>
                    <?php echo ucfirst($client['approval_status']); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

</table>

</body>
</html>
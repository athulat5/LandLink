<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["admin"]);

// Fetch all industries and their associated user information
$industries = $industriesCollection->aggregate([
    [
        '$lookup' => [
            'from' => 'users',
            'localField' => 'user_id',
            'foreignField' => '_id',
            'as' => 'user_info'
        ]
    ]
]);

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $industryId = new MongoDB\BSON\ObjectId($_POST["industry_id"]);
    $action = $_POST["action"];

    if ($action === "approve" || $action === "reject") {
        $newIndustryStatus = $action === "approve" ? "active" : "inactive";
        $newApprovalStatus = $action === "approve" ? "approved" : "rejected";
        $newUserStatus = $newIndustryStatus; // Align user status with industry status

        // Update industry status
        $updateIndustryResult = $industriesCollection->updateOne(
            ["_id" => $industryId],
            ['$set' => [
                "status" => $newIndustryStatus,
                "approval_status" => $newApprovalStatus,
                "updated_at" => date("Y-m-d H:i:s")
            ]]
        );

        if ($updateIndustryResult->getModifiedCount() > 0) {
            // Retrieve the associated user_id
            $industry = $industriesCollection->findOne(["_id" => $industryId]);
            $userId = $industry['user_id'];

            // Update user status
            $updateUserResult = $usersCollection->updateOne(
                ["_id" => $userId],
                ['$set' => [
                    "status" => $newUserStatus,
                    "updated_at" => date("Y-m-d H:i:s")
                ]]
            );

            if ($updateUserResult->getModifiedCount() > 0) {
                echo '<script>alert("Industry and user status updated successfully."); window.location.href="industry_approve.php";</script>';
            } else {
                echo '<script>alert("Industry status updated, but failed to update user status.");</script>';
            }
        } else {
            echo '<script>alert("Failed to update industry status.");</script>';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Industries</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        button {
            padding: 8px 12px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            
        }
        .approve {
            background-color: #28a745;
            color: white;
        }
        .reject {
            background-color: #dc3545;
            color: white;
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

        }
        .back-btn:hover {
            background: #333;
        }
        h2 {
            margin-top: 40px;
            font-size: 24px;
            color: #333;
        }
    </style>
</head>
<body>
<a href="admin_dashboard.php" class="back-btn">← Back</a>

<h2>Manage Industries</h2>

<table>
    <thead>
        <tr>
            <th>Industry Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Description</th>
            <th>Logo</th>
            <th>Certificate</th>
            <th>Status</th>
            <th>Approval Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($industries as $industry): ?>
            <?php $user = $industry['user_info'][0] ?? null; ?>
            <tr>
                <td><?php echo htmlspecialchars($industry['industry_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($industry['contact']); ?></td>
                <td><?php echo htmlspecialchars($industry['address']); ?></td>
                <td><?php echo htmlspecialchars($industry['description']); ?></td>
                <td>
                    <?php if (!empty($industry['logo'])): ?>
                        <img src="<?php echo htmlspecialchars('../industry/' .$industry['logo']); ?>" alt="Logo">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($industry['certificate'])): ?>
                        <a href="<?php echo htmlspecialchars('../industry/' .$industry['certificate']); ?>" target="_blank">View Certificate</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($industry['status'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($industry['approval_status'] ?? 'N/A'); ?></td>
                
                <td>
    <?php if (!isset($industry['approval_status']) || $industry['approval_status'] === 'pending'): ?>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="industry_id" value="<?php echo $industry['_id']; ?>">
            <button type="submit" name="action" value="approve" class="approve">Approve</button>
        </form>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="industry_id" value="<?php echo $industry['_id']; ?>">
            <button type="submit" name="action" value="reject" class="reject">Reject</button>
        </form>
    <?php else: ?>
        <?php echo ucfirst($industry['approval_status'] ?? 'N/A'); ?>
    <?php endif; ?>
</td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>

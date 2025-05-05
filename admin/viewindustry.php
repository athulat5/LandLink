<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["admin"]);

// Fetch approved industries
$approvedIndustries = $industriesCollection->find(['approval_status' => 'approved']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approved Industries</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
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
        }

        .reset-btn {
            padding: 6px 12px;
            background: black;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .reset-btn:hover {
            background: #444;
        }
    </style>
</head>
<body>

<a href="javascript:history.back()" class="back-btn">← Back</a>
<h2>Approved Industries</h2>

<table>
    <thead>
        <tr>
            <th>Industry Name</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Description</th>
            <th>Logo</th>
            <th>Certificate</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($approvedIndustries as $industry): ?>
            <tr>
                <td><?php echo htmlspecialchars($industry['industry_name']); ?></td>
                <td><?php echo htmlspecialchars($industry['contact']); ?></td>
                <td><?php echo htmlspecialchars($industry['address']); ?></td>
                <td><?php echo htmlspecialchars($industry['description']); ?></td>
                <td>
                    <?php if (!empty($industry['logo'])): ?>
                        <img src="<?php echo htmlspecialchars('../industry/' . $industry['logo']); ?>" alt="Logo">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($industry['certificate'])): ?>
                        <a href="<?php echo htmlspecialchars('../industry/' . $industry['certificate']); ?>" target="_blank">View Certificate</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo isset($industry['status']) && strtolower($industry['status']) === 'inactive' ? 'Inactive' : 'Active'; ?>
                </td>
                <td>
                    <form method="POST" action="reset_password.php" onsubmit="return confirm('Are you sure you want to reset this industry\'s password?');">
                        <input type="hidden" name="user_id" value="<?php echo $industry['user_id']; ?>">
                        <button type="submit" class="reset-btn">Reset Password</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>

<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["staff"]);


// Fetch approved industries
$approvedIndustries = $industriesCollection->find(['approval_status' => 'approved']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        </tr>
    </thead>
    <tbody>
        <?php foreach ($approvedIndustries as $industry): ?>
            <tr>
                <td><?php echo htmlspecialchars($industry['industry_name']); ?></td>
                <td><?php echo htmlspecialchars($industry['contact']); ?></td>
                <td><?php echo htmlspecialchars($industry['address']); ?></td>
                <td><?php echo htmlspecialchars($industry['description']); ?></td>
               
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>

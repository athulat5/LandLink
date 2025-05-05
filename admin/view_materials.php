<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["admin"]);

// Fetch all raw materials
$rawMaterials = $rawMaterialCollection->find([]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Raw Materials</title>
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
<h2>Raw Materials List</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock Quantity</th>
            <th>Photo</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rawMaterials as $material): ?>
            <tr>
                <td><?php echo htmlspecialchars($material['name']); ?></td>
                <td><?php echo htmlspecialchars($material['description']); ?></td>
                <td><?php echo htmlspecialchars($material['price']); ?></td>
                <td><?php echo htmlspecialchars($material['stock_quantity']); ?></td>
                <td>
                    <?php if (!empty($material['photo'])): ?>
                        <img src="<?php echo htmlspecialchars($material['photo']); ?>" alt="Material Image">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($material['status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>

<?php
session_start();
require '../db.php';

use MongoDB\BSON\ObjectId;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'industry') {
    echo '<script>alert("Access Denied!"); window.location.href="/parksystem/login.html";</script>';
    exit();
}

$industryId = new ObjectId($_SESSION['user_id']);
$products = $productsCollection->find(['industry_id' => $industryId]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        .product {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        img {
            max-width: 150px;
            height: auto;
        }
        .back-btn {
            text-decoration: none;
            background: black;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <a href="industrydashboard.php" class="back-btn">← Back</a>
    <h2>My Products</h2>
    <?php foreach ($products as $product): ?>
        <div class="product">
            <h3><?= htmlspecialchars($product['product_name']) ?></h3>
            <p><strong>Price:</strong> <?= $product['price'] ?></p>
            <p><strong>Quantity:</strong> <?= $product['quantity'] ?></p>
            <p><strong>Category:</strong> <?= $product['category'] ?></p>
            <img src="<?= htmlspecialchars($product['product_image']) ?>" alt="Product Image">
        </div>
    <?php endforeach; ?>
</body>
</html>

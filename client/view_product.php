<?php
require '../db.php';

require '../check_role.php';
checkRole(["client"]);



// Fetch all products with quantity > 0
$products = $productsCollection->find([
    'quantity' => ['$gt' => 0]
]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
     <!-- Back button -->
     <a href="clientdashboard.php" class="btn btn-outline-secondary back-btn">
        <i class="bi bi-arrow-left"></i> Back to Dashboard </a>
    <h2 class="mb-4">Available Products</h2>
    <div class="row">
        <?php foreach ($products as $product): 
            $industry = $industriesCollection->findOne([
                'industry_id' => new MongoDB\BSON\ObjectId($product['industry_id'])
            ]);
        ?>
            <div class="col-md-4">
                <div class="product-card">
                <img src="/parksystem/industry/<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="img-fluid">
                    <h5 class="mt-2"><?= htmlspecialchars($product['product_name']) ?></h5>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p><strong>Industry:</strong> <?= htmlspecialchars($product['industry_name']) ?></p>
                    <p><strong>Price:</strong> ₹<?= number_format($product['price']) ?></p>
                    <p><strong>Available:</strong> <?= $product['quantity'] ?></p>
                    <a href="buyproduct.php?id=<?= $product['_id'] ?>" class="btn btn-primary btn-sm">Buy Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>

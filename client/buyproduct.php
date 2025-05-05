<?php
require '../db.php';
session_start();

if (!isset($_GET['id'])) {
    echo "Product not found.";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "Client not logged in.";
    exit;
}

$productId = new MongoDB\BSON\ObjectId($_GET['id']);
$product = $productsCollection->findOne(['_id' => $productId]);

if (!$product) {
    echo "Product not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buy Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Buy: <?= htmlspecialchars($product['product_name']) ?></h3>
    <form action="payment.php" method="POST">
        <input type="hidden" name="product_id" value="<?= $product['_id'] ?>">
        <input type="hidden" name="client_id" value="<?= $_SESSION['user_id'] ?>">

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" pattern="[0-9]{10}" required>
        </div>
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" min="1" max="<?= $product['quantity'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Proceed to Payment</button>
    </form>
</div>
</body>
</html> 
<?php
require '../db.php'; // This should initialize $database connection
session_start();

if (!isset($_SESSION['order'])) {
    echo "Invalid request.";
    exit;
}

// Initialize collections
$productsCollection = $database->products; // Add this line
$ordersCollection = $database->orders;    // Add this line

$orderData = $_SESSION['order'];
unset($_SESSION['order']); // Clear after use

// Validate product_id
if (empty($orderData['product_id'])) {
    die("Product ID is missing");
}

try {
    $product_id = new MongoDB\BSON\ObjectId($orderData['product_id']);
    $product = $productsCollection->findOne(['_id' => $product_id]);

    if (!$product) {
        echo "Product no longer available.";
        exit;
    }

    // Validate client_id
    if (empty($orderData['client_id'])) {
        die("Client ID is missing");
    }

    $client_id = new MongoDB\BSON\ObjectId($orderData['client_id']);
    
    $quantity = (int)$orderData['quantity'];
    if ($quantity > $product['quantity']) {
        echo "Insufficient quantity available.";
        exit;
    }

    $total_price = $quantity * $product['price'];

    // Create order document
    $order = [
        'product_id' => $product_id,
        'industry_id' => $product['industry_id'],
        'client_id' => $client_id,
        'name' => $orderData['name'],
        'address' => $orderData['address'],
        'phone' => $orderData['phone'],
        'quantity' => $quantity,
        'total_price' => $total_price,
        'order_date' => new MongoDB\BSON\UTCDateTime(),
        'status' => 'completed'
    ];

    // Insert order
    $result = $ordersCollection->insertOne($order);

    // Update product quantity
    $productsCollection->updateOne(
        ['_id' => $product_id],
        ['$inc' => ['quantity' => -$quantity]]
    );

} catch (MongoDB\Driver\Exception\InvalidArgumentException $e) {
    die("Invalid ID format: " . $e->getMessage());
} catch (Exception $e) {
    die("Error processing order: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Placed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-success">
        <h4>Payment Successful!</h4>
        <p>Your order has been placed successfully.</p>
        <p>Order ID: <?= $result->getInsertedId() ?></p>
        <a href="view_product.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</div>
</body>
</html>
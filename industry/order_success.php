<?php
require '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$orderId = $_GET['id'] ?? '';
$order = $database->raworders->findOne(['_id' => new MongoDB\BSON\ObjectId($orderId)]);

if (!$order) {
    header("Location: raw_materials_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h2><i class="bi bi-check-circle"></i> Order Placed Successfully</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h4 class="alert-heading">Thank you for your order!</h4>
                        <p>Your order has been placed successfully and will be processed shortly.</p>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Order Details</h5>
                        </div>
                        <div class="card-body text-start">
                            <p><strong>Order ID:</strong> <?= $order['_id'] ?></p>
                            <p><strong>Material:</strong> <?= htmlspecialchars($order['material_name']) ?></p>
                            <p><strong>Quantity:</strong> <?= $order['quantity'] ?> <?= $order['unit'] ?></p>
                            <p><strong>Total Amount:</strong> ₹<?= number_format($order['total_amount'], 2) ?></p>
                            <p><strong>Order Date:</strong> <?= $order['order_date']->toDateTime()->format('d M Y, h:i A') ?></p>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Delivery Information</h5>
                        </div>
                        <div class="card-body text-start">
                            <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                            <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['customer_address'])) ?></p>
                            <p><strong>Mobile:</strong> <?= htmlspecialchars($order['customer_mobile']) ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="raw_materials_list.php" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Materials
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
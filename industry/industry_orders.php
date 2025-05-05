<?php
require '../db.php';
session_start();

function checkRole($allowedRoles) {
    if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], $allowedRoles)) {
        echo '<script>alert("Access Denied!!.."); window.location.href="/parksystem/login.html";</script>';
        exit();
    }
}

checkRole(["industry"]);

// Check if industry is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: industry_login.php");
    exit();
}

$industryId = new MongoDB\BSON\ObjectId($_SESSION['user_id']);

// Find orders for this industry directly using industry_id field
$orders = $database->orders->find([
    'industry_id' => $industryId
], [
    'sort' => ['order_date' => -1] // Newest first
]);

// Count orders for display
$orderCount = $database->orders->countDocuments(['industry_id' => $industryId]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industry Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .order-card {
            border-left: 4px solid #3498db;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background-color: #1a252f;
            color: white;
            transform: translateX(-3px);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Back Button -->
        <a href="industrydashboard.php" class="back-btn">
            <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
        </a>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Your Product Orders</h2>
            <span class="badge bg-primary"><?= $orderCount ?> orders</span>
        </div>

        <?php if ($orderCount === 0): ?>
            <div class="alert alert-info">
                No orders found for your products yet.
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card order-card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Order #<?= $order['_id'] ?></h5>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                    <span class="ms-3 text-muted">
                                        <?= $order['order_date']->toDateTime()->format('d M Y, h:i A') ?>
                                    </span>
                                </div>
                                
                                <p><strong>Customer:</strong> <?= htmlspecialchars($order['name']) ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
                            </div>
                            <div class="col-md-4">
                                <div class="border-start ps-3">
                                    <p><strong>Product ID:</strong> <?= $order['product_id'] ?></p>
                                    <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
                                    <p><strong>Total Price:</strong> ₹<?= number_format($order['total_price'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
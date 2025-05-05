<?php
require '../db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

try {
    // Fetch orders only for the logged-in client
    $orders = $database->orders->find(
        ['client_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['sort' => ['order_date' => -1]]
    );
    
    // Convert cursor to array for easier handling
    $ordersArray = iterator_to_array($orders);
} catch (Exception $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Orders</h2>
        <a href="view_product.php" class="btn btn-primary">Continue Shopping</a>
    </div>
    
    <?php if (empty($ordersArray)): ?>
        <div class="empty-state">
            <i class="bi bi-box-seam" style="font-size: 3rem;"></i>
            <h4 class="mt-3">No orders yet</h4>
            <p>You haven't placed any orders yet. Start shopping to see your orders here.</p>
            <a href="view_product.php" class="btn btn-primary mt-2">Browse Products</a>
        </div>
    <?php else: ?>
        <?php foreach ($ordersArray as $order): 
            // Get product details (you might want to consider doing this in a single aggregation query)
            try {
                $product = $database->products->findOne(['_id' => $order['product_id']]);
            } catch (Exception $e) {
                $product = null;
            }
        ?>
            <div class="order-card">
                <div class="row">
                    <div class="col-md-8">
                        <h4><?= htmlspecialchars($product['product_name'] ?? 'Product not available') ?></h4>
                        <div class="row mt-3">
                            <div class="col-6 col-md-3">
                                <p class="mb-1 text-muted">Quantity</p>
                                <p><?= $order['quantity'] ?></p>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="mb-1 text-muted">Unit Price</p>
                                <p>₹<?= number_format($order['total_price'] / $order['quantity'], 2) ?></p>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="mb-1 text-muted">Total</p>
                                <p class="fw-bold">₹<?= number_format($order['total_price'], 2) ?></p>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="mb-1 text-muted">Status</p>
                                <p class="status-<?= htmlspecialchars($order['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <p class="text-muted mb-1">Order Date</p>
                        <p><?= $order['order_date']->toDateTime()->format('d M Y, h:i A') ?></p>
                        
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
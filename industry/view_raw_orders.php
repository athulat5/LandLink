<?php
require '../db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = new MongoDB\BSON\ObjectId($_SESSION['user_id']);

// Fetch orders for this user
$orders = $database->raworders->find([
    'user_id' => $userId
], [
    'sort' => ['order_date' => -1] // Newest first
]);

// Count orders for display
$orderCount = $database->raworders->countDocuments(['user_id' => $userId]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --border-color: #dee2e6;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --radius-md: 8px;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background-color: #1a252f;
            color: white;
            transform: translateX(-3px);
        }

        .order-card {
            border-left: 4px solid var(--accent-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.875rem;
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

        @media (max-width: 768px) {
            .order-details {
                margin-top: 1rem;
            }
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
            <h2><i class="bi bi-list-check"></i> My Orders</h2>
            <span class="badge bg-primary"><?= $orderCount ?> orders</span>
        </div>

        <?php if ($orderCount === 0): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> You haven't placed any orders yet.
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card order-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-3">Order #<?= $order['_id'] ?></h5>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                                <p class="text-muted mb-3">
                                    <i class="bi bi-calendar"></i> 
                                    <?= $order['order_date']->toDateTime()->format('d M Y, h:i A') ?>
                                </p>
                                
                                <h6><?= htmlspecialchars($order['material_name']) ?></h6>
                                <p>
                                    <span class="fw-bold"><?= $order['quantity'] ?> <?= $order['unit'] ?></span> 
                                    at ₹<?= number_format($order['unit_price'], 2) ?> per <?= $order['unit'] ?>
                                </p>
                            </div>
                            <div class="col-md-4 order-details">
                                <div class="border-start ps-3 h-100">
                                    <h5 class="text-success">₹<?= number_format($order['total_amount'], 2) ?></h5>
                                    <p class="mb-1"><strong>Payment:</strong> <?= ucfirst($order['payment_method']) ?></p>
                                    
                                    <div class="mt-3">
                                        <h6>Delivery Info:</h6>
                                        <p class="mb-1"><?= htmlspecialchars($order['customer_name']) ?></p>
                                        <p class="mb-1"><?= nl2br(htmlspecialchars($order['customer_address'])) ?></p>
                                        <p class="mb-0"><?= htmlspecialchars($order['customer_mobile']) ?></p>
                                    </div>
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
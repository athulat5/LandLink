<?php
require '../check_role.php';
require '../db.php'; // MongoDB connection
checkRole(["staff"]);

// Fetch all raw material orders sorted by date (newest first)
$rawOrders = $database->raworders->find([], [
    'sort' => ['order_date' => -1]
]);

// Count all raw material orders
$orderCount = $database->raworders->countDocuments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raw Material Orders - Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --text-muted: #6c757d;
            --radius-md: 10px;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 8px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--light-bg);
            color: #212529;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .order-card {
            background: var(--card-bg);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--accent-color);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .order-header {
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-body {
            padding: 1.5rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.8rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .badge-completed {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }

        .badge-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }

        .badge-processing {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .detail-row {
            display: flex;
            margin-bottom: 0.8rem;
        }

        .detail-label {
            font-weight: 600;
            min-width: 150px;
            color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--card-bg);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
        }

        .search-container {
            margin-bottom: 2rem;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                margin-bottom: 0.3rem;
                min-width: 100px;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Back Button -->
        <a href="staffdashboard.php" class="back-btn">
            <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
        </a>

        <div class="page-header">
            <h2 class="mb-0">
                <i class="bi bi-box-seam me-2"></i>Raw Material Orders
            </h2>
            <div class="d-flex align-items-center">
                <span class="badge bg-primary rounded-pill">
                    <?= $orderCount ?> orders
                </span>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-container card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="searchInput" class="form-label">Search Orders</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by customer name, material...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="all" selected>All Statuses</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="paymentFilter" class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentFilter">
                            <option value="all" selected>All Methods</option>
                            <option value="dummy">Dummy</option>
                            <option value="credit">Credit Card</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($orderCount === 0): ?>
            <div class="empty-state">
                <i class="bi bi-cart-x" style="font-size: 3rem; color: var(--text-muted);"></i>
                <h3 class="mt-3">No Orders Found</h3>
                <p class="text-muted">There are currently no raw material orders in the system.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rawOrders as $order): 
                $statusClass = 'badge-' . $order['status'];
                $orderDate = $order['order_date']->toDateTime();
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <strong class="me-2">Order #<?= $order['_id'] ?></strong>
                            <span class="status-badge <?= $statusClass ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        <div class="text-muted">
                            <i class="bi bi-calendar me-1"></i>
                            <?= $orderDate->format('M j, Y \a\t g:i A') ?>
                        </div>
                    </div>
                    <div class="order-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">
                                    <i class="bi bi-person me-2"></i>Customer Information
                                </h5>
                                <div class="detail-row">
                                    <span class="detail-label">Name:</span>
                                    <span><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Mobile:</span>
                                    <span><?= htmlspecialchars($order['customer_mobile'] ?? 'N/A') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Address:</span>
                                    <span><?= nl2br(htmlspecialchars($order['customer_address'] ?? 'N/A')) ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">
                                    <i class="bi bi-box-seam me-2"></i>Order Details
                                </h5>
                                <div class="detail-row">
                                    <span class="detail-label">Material:</span>
                                    <span><?= htmlspecialchars($order['material_name'] ?? 'N/A') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Material ID:</span>
                                    <span><?= $order['material_id'] ?? 'N/A' ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Quantity:</span>
                                    <span><?= $order['quantity'] ?? 0 ?> <?= $order['unit'] ?? '' ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Unit Price:</span>
                                    <span>₹<?= number_format($order['unit_price'] ?? 0, 2) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Total Amount:</span>
                                    <span class="fw-bold">₹<?= number_format($order['total_amount'] ?? 0, 2) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Payment Method:</span>
                                    <span><?= ucfirst($order['payment_method'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Basic search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const orderCards = document.querySelectorAll('.order-card');
            
            orderCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Status filter functionality
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value;
            const orderCards = document.querySelectorAll('.order-card');
            
            orderCards.forEach(card => {
                if (status === 'all') {
                    card.style.display = 'block';
                } else {
                    const cardStatus = card.querySelector('.status-badge').textContent.toLowerCase();
                    card.style.display = cardStatus.includes(status) ? 'block' : 'none';
                }
            });
        });

        // Payment method filter functionality
        document.getElementById('paymentFilter').addEventListener('change', function() {
            const method = this.value;
            const orderCards = document.querySelectorAll('.order-card');
            
            orderCards.forEach(card => {
                if (method === 'all') {
                    card.style.display = 'block';
                } else {
                    const paymentText = card.textContent.toLowerCase();
                    card.style.display = paymentText.includes(method) ? 'block' : 'none';
                }
            });
        });
    </script>
</body>
</html>
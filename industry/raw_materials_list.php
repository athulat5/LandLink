<?php
require '../db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$rawMaterials = $database->raw_material->find(['status' => 'active']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Raw Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .material-card {
            transition: transform 0.3s;
        }
        .material-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .material-img {
            height: 200px;
            object-fit: cover;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            margin-bottom: 1.5rem;
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
        .buy-btn {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <!-- Back Button -->
    <a href="industrydashboard.php" class="back-btn">
        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
    </a>

    <h2 class="mb-4">Available Raw Materials</h2>
    <div class="row">
        <?php foreach ($rawMaterials as $material): ?>
        <div class="col-md-4 mb-4">
            <div class="card material-card h-100">
                <img src="/parksystem/staff/<?= htmlspecialchars($material['photo']) ?>" class="card-img-top material-img">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($material['name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($material['description']) ?></p>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-success fw-bold">₹<?= number_format($material['price'], 2) ?></span>
                        <span class="badge bg-primary"><?= $material['stock_quantity'] ?> <?= $material['stock_unit'] ?> available</span>
                    </div>
                    <a href="order_form.php?id=<?= $material['_id'] ?>" class="btn btn-primary w-100 buy-btn">
                        <i class="bi bi-cart-plus me-2"></i> Buy Now
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
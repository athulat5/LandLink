<?php
require '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$materialId = $_GET['id'] ?? '';
$material = $database->raw_material->findOne(['_id' => new MongoDB\BSON\ObjectId($materialId)]);

if (!$material) {
    header("Location: raw_materials_list.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['order_details'] = [
        'material_id' => $materialId,
        'quantity' => $_POST['quantity'],
        'name' => $_POST['name'],
        'address' => $_POST['address'],
        'mobile' => $_POST['mobile']
    ];
    header("Location: payment.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Place Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Order <?= htmlspecialchars($material['name']) ?></h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <img src="/parksystem/staff/<?= htmlspecialchars($material['photo']) ?>" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($material['name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($material['description']) ?></p>
                    <p class="text-success fw-bold">₹<?= number_format($material['price'], 2) ?> per <?= $material['stock_unit'] ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Enter Your Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Quantity (<?= $material['stock_unit'] ?>)</label>
                            <input type="number" name="quantity" class="form-control" 
                                   min="0.1" step="0.1" max="<?= $material['stock_quantity'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Delivery Address</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
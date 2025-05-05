<?php
require '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_details'])) {
    header("Location: raw_materials_list.php");
    exit();
}

$userId = $_SESSION['user_id'];
$orderDetails = $_SESSION['order_details'];
$material = $database->raw_material->findOne(['_id' => new MongoDB\BSON\ObjectId($orderDetails['material_id'])]);

if (!$material) {
    header("Location: raw_materials_list.php");
    exit();
}

$totalAmount = $material['price'] * $orderDetails['quantity'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create order document
    $orderData = [
        'user_id' => new MongoDB\BSON\ObjectId($userId),
        'material_id' => new MongoDB\BSON\ObjectId($orderDetails['material_id']),
        'material_name' => $material['name'],
        'quantity' => (float)$orderDetails['quantity'],
        'unit' => $material['stock_unit'],
        'unit_price' => $material['price'],
        'total_amount' => $totalAmount,
        'customer_name' => $orderDetails['name'],
        'customer_address' => $orderDetails['address'],
        'customer_mobile' => $orderDetails['mobile'],
        'order_date' => new MongoDB\BSON\UTCDateTime(),
        'status' => 'completed',
        'payment_method' => 'dummy'
    ];
    
    // Insert order
    $result = $database->raworders->insertOne($orderData);
    
    if ($result->getInsertedCount() > 0) {
        // Update stock
        $database->raw_material->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($orderDetails['material_id'])],
            ['$inc' => ['stock_quantity' => -$orderDetails['quantity']]]
        );
        
        unset($_SESSION['order_details']);
        header("Location: order_success.php?id=" . $result->getInsertedId());
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Order Summary</h6>
                        <p><?= htmlspecialchars($material['name']) ?> - <?= $orderDetails['quantity'] ?> <?= $material['stock_unit'] ?></p>
                        <p>Unit Price: ₹<?= number_format($material['price'], 2) ?></p>
                        <hr>
                        <h5>Total Amount: ₹<?= number_format($totalAmount, 2) ?></h5>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title">Dummy Payment</h6>
                            <p class="card-text">This is a simulated payment process for demonstration purposes.</p>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">I agree to the terms and conditions</label>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <button type="submit" class="btn btn-success w-100 py-2">
                            <i class="bi bi-credit-card"></i> Confirm Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
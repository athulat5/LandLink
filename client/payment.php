<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: buyproduct.php');
    exit;
}

// Save data in session for later use in placeorder.php
$_SESSION['order'] = $_POST;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Payment</h3>
    <form action="placeorder.php" method="POST">
        <div class="mb-3">
            <label>Card Number</label>
            <input type="text" name="card" class="form-control" placeholder="XXXX-XXXX-XXXX-XXXX" required>
        </div>
        <div class="mb-3">
            <label>Expiry</label>
            <input type="text" name="expiry" class="form-control" placeholder="MM/YY" required>
        </div>
        <div class="mb-3">
            <label>CVV</label>
            <input type="password" name="cvv" class="form-control" placeholder="123" required>
        </div>
        <button type="submit" class="btn btn-success">Pay & Place Order</button>
    </form>
</div>
</body>
</html>

<?php
session_start();
require '../db.php'; // MongoDB connection

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

// Redirect if not logged in or not an industry user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'industry') {
    echo '<script>alert("Access Denied!"); window.location.href="/parksystem/login.html";</script>';
    exit();
}

$industryId = new ObjectId($_SESSION['user_id']);
$error = "";
$success = "";

// Create upload folder if not exists
$uploadDir = "uploads/product_images/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productName = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $category = trim($_POST['category']);
    $industryName = trim($_POST['ind_name']);

    // Validations
    if (empty($productName) || empty($description) || empty($price) || empty($quantity) || empty($category) || empty($industryName)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a positive number.";
    } elseif (!is_numeric($quantity) || $quantity < 0) {
        $error = "Quantity must be a non-negative number.";
    } elseif (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== 0) {
        $error = "Product image is required.";
    } else {
        // Validate file type and size
        $fileTmp = $_FILES['product_image']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $targetPath = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $fileSize = $_FILES['product_image']['size'];

        $allowedTypes = ['jpg', 'jpeg', 'png'];
        $allowedMime = ['image/jpeg', 'image/png'];

        $mimeType = mime_content_type($fileTmp);

        if (!in_array($fileType, $allowedTypes) || !in_array($mimeType, $allowedMime)) {
            $error = "Only JPG, JPEG, and PNG image files are allowed.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $error = "Image size should not exceed 2MB.";
        } else {
            // Check if product already exists
            $existing = $productsCollection->findOne([
                'product_name' => $productName,
                'industry_id' => $industryId
            ]);

            if ($existing) {
                $error = "Product already exists.";
            } else {
                // Save image
                if (move_uploaded_file($fileTmp, $targetPath)) {
                    // Insert into MongoDB
                    try {
                        $productsCollection->insertOne([
                            'product_name' => htmlspecialchars($productName),
                            'description' => htmlspecialchars($description),
                            'price' => (float)$price,
                            'quantity' => (int)$quantity,
                            'category' => htmlspecialchars($category),
                            'product_image' => $targetPath,
                            'industry_id' => $industryId,
                            'industry_name' => htmlspecialchars($industryName),
                            'status' => 'active',
                            'created_at' => new UTCDateTime()
                        ]);
                        $success = "Product added successfully.";
                    } catch (Exception $e) {
                        $error = "Database error: " . $e->getMessage();
                    }
                } else {
                    $error = "Failed to upload product image.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f9f9f9; }
        .form-box { background: #fff; padding: 20px; border-radius: 8px; width: 400px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, textarea, select { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; }
        .btn { background: #28a745; color: #fff; border: none; padding: 10px; cursor: pointer; width: 100%; border-radius: 4px; }
        .btn:hover { background: #218838; }
        .message { margin-top: 10px; padding: 10px; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        a.back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Add Product</h2>

    <?php if (!empty($error)): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Name</label>
        <input type="text" name="product_name" required>

        <label>Description</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Price (in ₹)</label>
        <input type="number" name="price" step="0.01" required>

        <label>Quantity</label>
        <input type="number" name="quantity" required>

        <label>Category</label>
        <input type="text" name="category" required>

        <label>Industry Name</label>
        <input type="text" name="ind_name" required>

        <label>Product Image</label>
        <input type="file" name="product_image" accept="image/*" required>

        <button type="submit" class="btn">Add Product</button>
    </form>
    <a href="industrydashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>

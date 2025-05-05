<?php
session_start();
require '../db.php'; // MongoDB connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'industry') {
    echo '<script>alert("Access Denied!"); window.location.href="/parksystem/login.html";</script>';
    exit();
}

$productCollection = $database->products;
$products = iterator_to_array($productCollection->find([
    "industry_id" => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
]));

// Handle Edit Product
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['edit_product'])) {
    // Debug - Check POST data
    // echo "<pre>"; print_r($_POST); echo "</pre>";
    
    try {
        $id = new MongoDB\BSON\ObjectId($_POST['product_id']);
        
        $updateResult = $productCollection->updateOne(
            ["_id" => $id],
            ['$set' => [
                "product_name" => trim($_POST['product_name']),
                "description" => trim($_POST['description']),
                "price" => (float) $_POST['price'],
                "quantity" => (int) $_POST['quantity']
            ]]
        );

        if ($updateResult->getModifiedCount() > 0) {
            echo "<script>alert('Product Updated Successfully!'); window.location.href='manageproduct.php';</script>";
        } else {
            // No changes were made or document wasn't found
            echo "<script>alert('No changes made or product not found!');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Error updating product: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Handle Delete Product
if (isset($_GET['delete_id'])) {
    try {
        $id = new MongoDB\BSON\ObjectId($_GET['delete_id']);
        $deleteResult = $productCollection->deleteOne(["_id" => $id]);

        if ($deleteResult->getDeletedCount() > 0) {
            echo "<script>alert('Product Deleted!'); window.location.href='manageproduct.php';</script>";
        } else {
            echo "<script>alert('Error deleting product!');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Error deleting product: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <a href="industrydashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>
    <h2 class="mb-4">Manage Products</h2>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock Quantity</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 1;
        foreach ($products as $product) { ?>
            <tr>
                <td><?= $count++; ?></td>
                <td><?= htmlspecialchars($product['product_name']); ?></td>
                <td><?= htmlspecialchars($product['description']); ?></td>
                <td>$<?= number_format($product['price'], 2); ?></td>
                <td><?= htmlspecialchars($product['quantity']); ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                            data-bs-target="#editProductModal<?= $product['_id']; ?>">✏ Edit
                    </button>
                    <a href="?delete_id=<?= $product['_id']; ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Are you sure you want to delete this product?')">🗑 Delete</a>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editProductModal<?= $product['_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $product['_id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="manageproduct.php">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel<?= $product['_id']; ?>">Edit Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="product_id" value="<?= $product['_id']; ?>">

                                <div class="mb-3">
                                    <label for="product_name<?= $product['_id']; ?>" class="form-label">Product Name:</label>
                                    <input type="text" id="product_name<?= $product['_id']; ?>" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description<?= $product['_id']; ?>" class="form-label">Description:</label>
                                    <textarea id="description<?= $product['_id']; ?>" name="description" class="form-control" rows="3" required><?= htmlspecialchars($product['description']); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="price<?= $product['_id']; ?>" class="form-label">Price:</label>
                                    <input type="number" id="price<?= $product['_id']; ?>" name="price" class="form-control" value="<?= htmlspecialchars($product['price']); ?>" step="0.01" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label for="quantity<?= $product['_id']; ?>" class="form-label">Quantity:</label>
                                    <input type="number" id="quantity<?= $product['_id']; ?>" name="quantity" class="form-control" value="<?= htmlspecialchars($product['quantity']); ?>" min="0" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="edit_product" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fix for modals automatically closing when clicking inside
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to all modal dialog elements to prevent event propagation
    const modalDialogs = document.querySelectorAll('.modal-dialog');
    modalDialogs.forEach(function(dialog) {
        dialog.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
});
</script>
</body>
</html>
<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["staff"]);

// Fetch all raw materials with creator information
$rawMaterials = $database->raw_material->aggregate([
    [
        '$lookup' => [
            'from' => 'users',
            'localField' => 'created_by',
            'foreignField' => '_id',
            'as' => 'creator'
        ]
    ],
    ['$unwind' => '$creator'],
    ['$sort' => ['created_at' => -1]] // Newest first
]);

// Handle Edit Raw Material
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['edit_material'])) {
    $id = new MongoDB\BSON\ObjectId($_POST['material_id']);
    
    $updateData = [
        "name" => $_POST['name'],
        "description" => $_POST['description'],
        "price" => (float)$_POST['price'],
        "stock_quantity" => (float)$_POST['stock_quantity'],
        "stock_unit" => $_POST['stock_unit'],
        "status" => $_POST['status'],
        "updated_at" => new MongoDB\BSON\UTCDateTime()
    ];

    // Handle photo update if new file uploaded
    if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "raw_materials" . DIRECTORY_SEPARATOR;
        $photoName = time() . "_" . basename($_FILES["photo"]["name"]);
        $photoPath = $photoDir . $photoName;
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath)) {
            $updateData['photo'] = "uploads/raw_materials/" . $photoName;
        }
    }

    $updateResult = $database->raw_material->updateOne(
        ["_id" => $id],
        ['$set' => $updateData]
    );

    if ($updateResult->getModifiedCount() > 0) {
        echo "<script>alert('Raw Material Updated Successfully!'); window.location.href='manage_raw_materials.php';</script>";
    } else {
        echo "<script>alert('No changes made or error updating!');</script>";
    }
}

// Handle Delete Raw Material
if (isset($_GET['delete_id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['delete_id']);
    $deleteResult = $database->raw_material->deleteOne(["_id" => $id]);

    if ($deleteResult->getDeletedCount() > 0) {
        echo "<script>alert('Raw Material Deleted!'); window.location.href='manage_raw_materials.php';</script>";
    } else {
        echo "<script>alert('Error deleting raw material!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Raw Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #dee2e6;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
        }

        .card {
            border-radius: 10px;
            box-shadow: var(--shadow);
            border: none;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--light-color);
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .status-active {
            color: var(--success-color);
            font-weight: 500;
        }

        .status-inactive {
            color: var(--danger-color);
            font-weight: 500;
        }

        .material-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid var(--border-color);
        }

        .action-btn {
            padding: 5px 10px;
            font-size: 14px;
            transition: var(--transition);
        }

        .edit-btn:hover {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .delete-btn:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .modal-content {
            border-radius: 10px;
            border: none;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-body img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .creator-info {
            font-size: 14px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <a href="staffdashboard.php" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Manage Raw Materials</h4>
                <a href="add_raw.php" class="btn btn-success btn-sm">
                    <i class="bi bi-plus"></i> Add New
                </a>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 1; foreach ($rawMaterials as $material): ?>
                            <tr>
                                <td><?= $count++; ?></td>
                                <td>
                                    <img src="/parksystem/staff/<?= htmlspecialchars($material['photo'] ?? 'images/default-material.jpg') ?>" 
                                         alt="<?= htmlspecialchars($material['name']) ?>" 
                                         class="material-img">
                                </td>
                                <td><?= htmlspecialchars($material['name']) ?></td>
                                <td><?= htmlspecialchars(substr($material['description'], 0, 50)) . (strlen($material['description']) > 50 ? '...' : '') ?></td>
                                <td>₹<?= number_format($material['price'], 2) ?></td>
                                <td><?= htmlspecialchars($material['stock_quantity']) ?> <?= htmlspecialchars($material['stock_unit']) ?></td>
                                <td class="status-<?= htmlspecialchars($material['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($material['status'])) ?>
                                </td>
                                <td>
                                    <?= date('d M Y', $material['created_at']->toDateTime()->getTimestamp()) ?>
                                    <div class="creator-info">
                                        by <?= htmlspecialchars($material['creator']['email']) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning action-btn edit-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal<?= $material['_id'] ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <a href="?delete_id=<?= $material['_id'] ?>" 
                                       class="btn btn-sm btn-danger action-btn delete-btn"
                                       onclick="return confirm('Are you sure you want to delete this material?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $material['_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Raw Material</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="material_id" value="<?= $material['_id'] ?>">
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <img src="/parksystem/staff/<?= htmlspecialchars($material['photo']) ?>" 
                                                             alt="Current Photo" 
                                                             class="img-fluid rounded">
                                                        <label class="mt-2">Change Photo:</label>
                                                        <input type="file" name="photo" class="form-control" accept="image/*">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label>Name</label>
                                                            <input type="text" name="name" class="form-control" 
                                                                   value="<?= htmlspecialchars($material['name']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Description</label>
                                                            <textarea name="description" class="form-control" rows="3" required><?= 
                                                                htmlspecialchars($material['description']) ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label>Price (₹)</label>
                                                        <input type="number" name="price" class="form-control" 
                                                               value="<?= htmlspecialchars($material['price']) ?>" step="0.01" min="0.01" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label>Stock Quantity</label>
                                                        <input type="number" name="stock_quantity" class="form-control" 
                                                               value="<?= htmlspecialchars($material['stock_quantity']) ?>" step="0.1" min="0" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label>Unit</label>
                                                        <select name="stock_unit" class="form-control" required>
                                                            <option value="kg" <?= $material['stock_unit'] == 'kg' ? 'selected' : '' ?>>kg</option>
                                                            <option value="g" <?= $material['stock_unit'] == 'g' ? 'selected' : '' ?>>g</option>
                                                            <option value="l" <?= $material['stock_unit'] == 'l' ? 'selected' : '' ?>>l</option>
                                                            <option value="ml" <?= $material['stock_unit'] == 'ml' ? 'selected' : '' ?>>ml</option>
                                                            <option value="m" <?= $material['stock_unit'] == 'm' ? 'selected' : '' ?>>m</option>
                                                            <option value="cm" <?= $material['stock_unit'] == 'cm' ? 'selected' : '' ?>>cm</option>
                                                            <option value="unit" <?= $material['stock_unit'] == 'unit' ? 'selected' : '' ?>>unit</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="active" <?= $material['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= $material['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="edit_material" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fix for modals automatically closing when clicking inside
        document.addEventListener('DOMContentLoaded', function() {
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
<?php
require '../db.php'; // MongoDB connection
require '../check_role.php';
checkRole(["staff"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = (float)$_POST["price"];
    $stock_quantity = (float)$_POST["stock_quantity"];
    $stock_unit = $_POST["stock_unit"];
    $status = $_POST["status"]; 
    $created_by = $_SESSION['user_id']; // Get staff ID from session
    $created_at = new MongoDB\BSON\UTCDateTime(); // Current timestamp

    // Validate required fields
    if (empty($name) || empty($description) || $price <= 0 || $stock_quantity <= 0 || empty($stock_unit)) {
        echo '<script>alert("Please fill in all fields correctly.");</script>';
        exit;
    }

    // Handle file upload
    if (isset($_FILES["photo"]) && ($_FILES["photo"]["error"]) === UPLOAD_ERR_OK) {
        $photoDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "raw_materials" . DIRECTORY_SEPARATOR;

        // Create directory if it doesn't exist
        if (!is_dir($photoDir)) {
            mkdir($photoDir, 0777, true);
        }

        $photoName = time() . "_" . basename($_FILES["photo"]["name"]);
        $photoPath = $photoDir . $photoName;
        $allowedExtensions = ["jpg", "jpeg", "png"];
        $fileExtension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo '<script>alert("Invalid file type. Only JPG, JPEG and PNG files are allowed.");</script>';
            exit;
        }

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath)) {
            $photoPath = "uploads/raw_materials/" . $photoName;
        } else {
            echo '<script>alert("Failed to upload image!");</script>';
            exit;
        }
    } else {
        echo '<script>alert("Please upload a valid image file!");</script>';
        exit;
    }

    // Insert into raw-materials collection
    $rawMaterialInsertResult = $rawMaterialCollection->insertOne([
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "stock_quantity" => $stock_quantity,
        "stock_unit" => $stock_unit,
        "photo" => $photoPath,
        "status" => $status,
        "created_by" => new MongoDB\BSON\ObjectId($created_by),
        "created_at" => $created_at,
        "updated_at" => $created_at
    ]);

    if ($rawMaterialInsertResult->getInsertedCount() > 0) {
        echo '<script>alert("Raw Material Added Successfully!"); window.location.href="staffdashboard.php";</script>';
    } else {
        echo '<script>alert("Error adding raw material!");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Raw Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --light-color: #ffffff;
            --dark-color: #1a1a1a;
            --border-color: #e0e0e0;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #000000, #444444);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        .container {
            background: var(--light-color);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 500px;
            animation: fadeIn 0.8s ease-in-out;
            border-top: 4px solid var(--accent-color);
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 10px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-color);
        }

        input, textarea, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: var(--transition);
        }

        input:focus, textarea:focus, select:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="file"] {
            padding: 10px;
            background: #f8f9fa;
            border: 1px dashed var(--border-color);
        }

        button {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            margin-top: 10px;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            background: var(--primary-color);
            color: var(--light-color);
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: var(--shadow);
            border: 2px solid var(--primary-color);
        }

        .back-btn:hover {
            background: var(--light-color);
            color: var(--primary-color);
            transform: translateX(-5px);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px 20px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .back-btn {
                top: 15px;
                left: 15px;
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
    <script>
        function validateForm() {
            const price = parseFloat(document.forms["staffForm"]["price"].value);
            const stockQuantity = parseFloat(document.forms["staffForm"]["stock_quantity"].value);
            
            if (price <= 0 || isNaN(price)) {
                alert("Price must be greater than zero.");
                return false;
            }

            if (stockQuantity <= 0 || isNaN(stockQuantity)) {
                alert("Stock quantity must be greater than zero.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <a href="staffdashboard.php" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div class="container">
        <h2>Add Raw Materials</h2>
        <form name="staffForm" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="name">Material Name</label>
                <input type="text" id="name" name="name" placeholder="Enter material name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter description" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price (per unit)</label>
                <input type="number" id="price" name="price" placeholder="0.00" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity</label>
                <input type="number" id="stock_quantity" name="stock_quantity" placeholder="0" step="0.1" min="0.1" required>
            </div>
            
            <div class="form-group">
                <label for="stock_unit">Unit of Measurement</label>
                <select id="stock_unit" name="stock_unit" required>
                    <option value="kg">Kilograms (kg)</option>
                    <option value="g">Grams (g)</option>
                    <option value="l">Liters (l)</option>
                    <option value="ml">Milliliters (ml)</option>
                    <option value="m">Meters (m)</option>
                    <option value="cm">Centimeters (cm)</option>
                    <option value="unit">Units</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="photo">Material Photo</label>
                <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png" required>
                <small>Only JPG, JPEG, PNG files allowed</small>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <button type="submit">Add Raw Material</button>
        </form>
    </div>
</body>
</html>
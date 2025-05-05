<?php
require 'db.php';


$products = $productsCollection->find([]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2c3e50;
        --primary-dark: #1a252f;
        --accent-color: #3498db;
        --light-color: #f8f9fa;
        --border-color: #dee2e6;
        --text-muted: #6c757d;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.25s ease;
        --radius-md: 8px;
    }

    body {
        background-color: #f8fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #333;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        margin: 1.5rem;
        padding: 0.5rem 1rem;
        background-color: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: var(--radius-md);
        transition: var(--transition);
    }

    .back-btn:hover {
        background-color: var(--primary-dark);
        transform: translateX(-3px);
    }

    .page-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .page-header h1 {
        font-weight: 600;
        color: var(--primary-color);
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        padding: 0 1.5rem;
    }

    .product-card {
        background: white;
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-bottom: 1px solid var(--border-color);
    }

    .product-body {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .product-category {
        display: inline-block;
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .product-description {
        color: var(--text-muted);
        margin-bottom: 1rem;
        flex-grow: 1;
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 1.5rem 1.5rem;
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .product-stock {
        font-size: 0.875rem;
        color: #388e3c;
        font-weight: 500;
    }

    .view-btn {
        background-color: var(--accent-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
    }

    .view-btn:hover {
        background-color: #1e88e5;
        color: white;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        
        .product-image {
            height: 180px;
        }
    }

    @media (max-width: 576px) {
        .product-grid {
            grid-template-columns: 1fr;
            padding: 0 1rem;
        }
        
        .page-header h1 {
            font-size: 1.75rem;
        }
    }
    </style>
</head>
<body>

<a href="home.php" class="back-btn">
    <i class="bi bi-arrow-left"></i> Back to Dashboard
</a>

<div class="container">
    <div class="page-header">
        <h1>Our Product Catalog</h1>
        <p class="text-muted">Browse our premium selection of products</p>
    </div>
    
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="/parksystem/industry/<?php echo htmlspecialchars($product['product_image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                     class="product-image">
                
                <div class="product-body">
                    <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="product-description">
                        <?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>
                        <?php if(strlen($product['description']) > 100): ?>...<?php endif; ?>
                    </p>
                </div>
                
                <div class="product-footer">
                    <div>
                        <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                        <div class="product-stock"><?php echo htmlspecialchars($product['quantity']); ?> in stock</div>
                    </div>
                    
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
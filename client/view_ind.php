<?php
require '../db.php';

// Fetch all industries
$industries = $industriesCollection->find();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Industries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .industry-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .industry-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .industry-logo {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            object-fit: contain;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Back button added here -->
        <a href="clientdashboard.php" class="btn btn-outline-secondary back-btn">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        
        <h1 class="mb-4">All Industries</h1>
        
        <div class="row">
            <?php foreach ($industries as $industry): ?>
                <div class="col-md-6">
                    <div class="industry-card">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <?php if (!empty($industry['logo'])): ?>
                                    <img src="/parksystem/industry/<?= htmlspecialchars($industry['logo']) ?>" 
                                         alt="<?= htmlspecialchars($industry['industry_name']) ?> Logo" 
                                         class="industry-logo img-fluid mb-3">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <h3><?= htmlspecialchars($industry['industry_name']) ?></h3>
                                <p><strong>Description:</strong> <?= htmlspecialchars($industry['description']) ?></p>
                                <p><strong>Land Area:</strong> <?= htmlspecialchars($industry['land_area']) ?> sq.ft</p>
                                <p><strong>Contact:</strong> <?= htmlspecialchars($industry['contact']) ?></p>
                                <p><strong>Address:</strong> <?= htmlspecialchars($industry['address']) ?></p>
                                <?php if (!empty($industry['certificate'])): ?>
                                    <a href="/parksystem/industry/<?= htmlspecialchars($industry['certificate']) ?>" 
                                       class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                        View Certificate
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
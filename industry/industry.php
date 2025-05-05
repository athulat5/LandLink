<?php
require '../db.php';


// Fetch only approved industries
$industries = $industriesCollection->find(['approval_status' => 'approved']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Industries</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .industry-card {
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            width: 300px;
            margin: 15px;
            padding: 15px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }
        .industry-card:hover {
            transform: scale(1.05);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
        }
        .industry-logo {
            width: 100%;
            height: 180px;
            object-fit: contain;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .industry-info {
            padding: 10px;
        }
        .industry-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: black;
        }
        .industry-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .industry-contact {
            font-size: 14px;
            color: #444;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            background: black;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background: #333;
        }

    </style>
</head>
<body>
 
<a href="javascript:history.back()" class="back-btn">← Back</a>
    <h1>Approved Industries</h1>
    
    <div class="container">
        <?php
        foreach ($industries as $industry) {
            echo '<div class="industry-card">';
            echo '<img src="' . htmlspecialchars($industry['logo']) . '" alt="Industry Logo" class="industry-logo">';
            echo '<div class="industry-info">';
            echo '<p class="industry-name">' . htmlspecialchars($industry['industry_name']) . '</p>';
            echo '<p class="industry-description">' . htmlspecialchars($industry['description']) . '</p>';
            echo '<p class="industry-contact"><strong>Contact:</strong> ' . htmlspecialchars($industry['contact']) . '</p>';
            echo '<p class="industry-contact"><strong>Address:</strong> ' . htmlspecialchars($industry['address']) . '</p>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>

</body>
</html>

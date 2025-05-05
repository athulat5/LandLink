<?php
require 'db.php';

$filesCollection = $database->selectCollection("fs.files");
$images = $filesCollection->find([]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Land Photos</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --light-color: #ffffff;
            --dark-color: #1a1a1a;
            --border-color: #e0e0e0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            background-color: var(--light-color);
            padding: 40px 20px;
            margin: 0;
            line-height: 1.6;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 15px;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .image-container {
            display: inline-block;
            border: 1px solid var(--border-color);
            margin: 20px;
            padding: 0;
            background: var(--light-color);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            vertical-align: top;
            width: 320px;
        }

        .image-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-color: var(--accent-color);
        }

        img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-bottom: 1px solid var(--border-color);
        }

        .info {
            padding: 15px;
            text-align: left;
        }

        .info p {
            margin: 8px 0;
            color: var(--dark-color);
        }

        .info p strong {
            color: var(--primary-color);
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            text-decoration: none;
            background: var(--primary-color);
            color: var(--light-color);
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
            z-index: 100;
            border: 2px solid var(--primary-color);
        }

        .back-btn:hover {
            background: var(--light-color);
            color: var(--primary-color);
            transform: translateX(-5px);
        }

        @media (max-width: 768px) {
            .image-container {
                width: 100%;
                max-width: 350px;
                margin: 15px 0;
            }
            
            h1 {
                font-size: 1.8rem;
                margin-top: 40px;
            }
        }
    </style>
</head>
<body>
<a href="javascript:history.back()" class="back-btn">← Back</a>
    <h1>Uploaded Land Photos</h1>
    <?php
    foreach ($images as $image) {
        echo '<div class="image-container">';
        echo '<img src="fetch_image.php?id=' . $image['_id'] . '" alt="Uploaded Image">';
        echo '<div class="info">';
        echo '<p><strong>Title:</strong> ' . htmlspecialchars($image->metadata['title']) . '</p>';
        echo '<p><strong>Description:</strong> ' . htmlspecialchars($image->metadata['description']) . '</p>';
        echo '</div>';
        echo '</div>';
    }
    ?>
</body>
</html>
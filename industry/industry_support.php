<?php
session_start();
require '../db.php'; // MongoDB connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'industry') {
    echo '<script>alert("Access Denied!"); window.location.href="/parksystem/login.html";</script>';
    exit();
}

$user_id = new MongoDB\BSON\ObjectId($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageContent = trim($_POST['message']);

    if (!empty($messageContent)) {
        $messageDocument = [
            'sender_id' => $user_id,
            'receiver_id' => null, // null indicates message to admin
            'message' => $messageContent,
            'timestamp' => new MongoDB\BSON\UTCDateTime(),
            'is_admin' => false
        ];

        $messagesCollection->insertOne($messageDocument);
        echo '<script>alert("Message sent successfully!"); window.location.href="industry_support.php";</script>';
        exit();
    } else {
        $error = "Message cannot be empty.";
    }
}

// Fetch messages between industry and admin
$messages = $messagesCollection->find([
    '$or' => [
        ['sender_id' => $user_id],
        ['receiver_id' => $user_id]
    ]
])->toArray();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support - Industry</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
            font-size: 16px;
        }
        form button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007BFF;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .message-list {
            list-style-type: none;
            padding: 0;
        }
        .message-list li {
            background-color: #f1f1f1;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .message-list li strong {
            display: block;
            margin-bottom: 5px;
        }
        .message-list li em {
            display: block;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .back-button {
    display: inline-block;
    margin-bottom: 15px;
    text-decoration: none;
    color: #007BFF;
    font-weight: bold;
    font-size: 20px;
    background: none;
    border: none;
    cursor: pointer;
}
.back-button:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
<a href="industrydashboard.php" class="back-button">← Back</a>
<div class="container">


    <h2>Support</h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <textarea name="message" rows="5" placeholder="Type your message here..." required></textarea><br>
        <button type="submit">Send Message</button>
    </form>

    <h3>Message History</h3>
    <ul class="message-list">
        <?php foreach ($messages as $msg): ?>
            <li>
                <strong><?php echo $msg['is_admin'] ? 'Admin' : 'You'; ?>:</strong>
                <?php echo htmlspecialchars($msg['message']); ?>
                <em><?php echo $msg['timestamp']->toDateTime()->format('Y-m-d H:i'); ?></em>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>

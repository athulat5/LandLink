<?php
session_start();
require '../db.php'; // MongoDB connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo '<script>alert("Access Denied!"); window.location.href="/parksystem/login.html";</script>';
    exit();
}

$admin_id = new MongoDB\BSON\ObjectId($_SESSION['user_id']);

// Fetch distinct industry IDs that have sent messages
$industryIds = $messagesCollection->distinct('sender_id', ['is_admin' => false]);

// Fetch industry details
$industries = $industriesCollection->find(['user_id' => ['$in' => $industryIds]])->toArray();

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = new MongoDB\BSON\ObjectId($_POST['receiver_id']);
    $messageContent = trim($_POST['message']);

    if (!empty($messageContent)) {
        $messageDocument = [
            'sender_id' => $admin_id,
            'receiver_id' => $receiver_id,
            'message' => $messageContent,
            'timestamp' => new MongoDB\BSON\UTCDateTime(),
            'is_admin' => true
        ];

        $messagesCollection->insertOne($messageDocument);
        echo '<script>alert("Reply sent successfully!"); window.location.href="admin_support.php";</script>';
        exit();
    } else {
        $error = "Message cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f3;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .industry-section {
            background: #fff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .industry-section h3 {
            margin-top: 0;
            color: #007BFF;
        }
        .message-list {
            list-style-type: none;
            padding: 0;
        }
        .message-list li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border-left: 4px solid #007BFF;
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
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        form button:hover {
            background-color: #218838;
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
<a href="admin_dashboard.php" class="back-button">← Back</a>
<div class="container">
    <h2>Support Messages</h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <?php foreach ($industries as $industry): ?>
        <div class="industry-section">
            <h3><?php echo htmlspecialchars($industry['industry_name']); ?></h3>

            <?php
            $industry_id = $industry['user_id'];
            $conversation = $messagesCollection->find([
                '$or' => [
                    ['sender_id' => $industry_id, 'receiver_id' => $admin_id],
                    ['sender_id' => $admin_id, 'receiver_id' => $industry_id],
                    ['sender_id' => $industry_id, 'receiver_id' => null]
                ]
            ])->toArray();
            ?>

            <ul class="message-list">
                <?php foreach ($conversation as $msg): ?>
                    <li>
                        <strong><?php echo $msg['is_admin'] ? 'Admin' : $industry['industry_name']; ?>:</strong>
                        <?php echo htmlspecialchars($msg['message']); ?>
                        <em><?php echo $msg['timestamp']->toDateTime()->format('Y-m-d H:i'); ?></em>
                    </li>
                <?php endforeach; ?>
            </ul>

            <form method="POST">
                <input type="hidden" name="receiver_id" value="<?php echo $industry_id; ?>">
                <textarea name="message" rows="3" placeholder="Type your reply here..." required></textarea><br>
                <button type="submit">Send Reply</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>

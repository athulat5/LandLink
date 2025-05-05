<?php
require '../db.php';

use MongoDB\BSON\ObjectId;

$industryData = null;
$error = "";
$approval_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $user = $usersCollection->findOne(['email' => $email]);

    if (!$user) {
        $error = "No user found for this email.";
    } else {
        $industry = $industriesCollection->findOne(['user_id' => new ObjectId($user['_id'])]);

        if ($industry) {
            $industryData = [
                "industry_name" => $industry['industry_name'] ?? 'N/A',
                "contact" => $industry['contact'] ?? 'N/A',
                "address" => $industry['address'] ?? 'N/A',
                "description" => $industry['description'] ?? 'N/A',
                "logo" => $industry['logo'] ?? '',
            ];
            $approval_status = $industry['approval_status'] ?? 'Pending';
        } else {
            $error = "Industry details not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Industry Approval Check</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: white;
            color: black;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid black;
        }

        h2, h3 {
            text-align: center;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="email"] {
            padding: 8px;
            width: 60%;
            border: 1px solid black;
            margin-right: 10px;
        }

        button {
            padding: 8px 15px;
            border: 1px solid black;
            background: white;
            cursor: pointer;
        }

        .error {
            color: black;
            font-weight: bold;
            text-align: center;
        }

        .approved, .pending, .rejected {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            border: 1px solid black;
            text-align: left;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        .back-btn {
            display: inline-block;
            margin: 10px;
            text-decoration: none;
            color: black;
            border: 1px solid black;
            padding: 6px 12px;
        }

        .back-btn:hover {
            background: #eee;
        }
    </style>
</head>
<body>

<a href="../home.php" class="back-btn">← Back</a>

<div class="container">
    <h2>Industry Approval Check</h2>

    <form method="POST">
        <input type="email" name="email" required placeholder="Enter Industry Email">
        <button type="submit">Check Approval</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($industryData): ?>
        <h3>Approval Status:
            <span class="<?php echo strtolower($approval_status); ?>">
                <?php echo ucfirst($approval_status); ?>
            </span>
        </h3>

        <table>
            <tr>
                <th>Industry Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Description</th>
                <th>Logo</th>
            </tr>
            <tr>
                <td><?php echo $industryData['industry_name']; ?></td>
                <td><?php echo $industryData['contact']; ?></td>
                <td><?php echo $industryData['address']; ?></td>
                <td><?php echo $industryData['description']; ?></td>
                <td>
                    <?php echo $industryData['logo'] ? "<img src='{$industryData['logo']}' alt='Logo'>" : 'N/A'; ?>
                </td>
            </tr>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

<?php
require '../db.php'; // Ensure db.php is included correctly

use MongoDB\BSON\ObjectId;

$clientData = null;
$error = "";
$approval_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Check if user exists
    $user = $usersCollection->findOne(['email' => $email]);

    if (!$user) {
        $error = "No user found for this email.";
    } else {
        // Fetch industry details using the user's ObjectId
        $client = $clientsCollection->findOne(['user_id' => new ObjectId($user['_id'])]);

        if ($client) {
            $clientData = [
                "name" => $client['name'] ?? 'N/A',
                "phone" => $client['phone'] ?? 'N/A',
                "address" => $client['address'] ?? 'N/A',
                
            ];
            $approval_status = $client['approval_status'] ?? 'Pending'; 
        } else {
            $error = "Client not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industry Approval Check</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f4f4f4; }
        .container { margin: 50px auto; width: 50%; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .error { color: red; }
        .approved { color: green; font-weight: bold; }
        .pending { color: orange; font-weight: bold; }
        .rejected { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #f2f2f2; }
        img { max-width: 100px; }
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
        }
        .back-btn:hover {
            background: #333;
        }


    </style>
</head>
<body>


<a href="../home.php" class="back-btn">← Back</a>
<div class="container">
    <h2>Client Approval Check</h2>
    
    <form method="POST">
        <input type="email" name="email" required placeholder="Enter Email">
        <button type="submit">Check Approval</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($clientData): ?>
        <h3>Approval Status: 
            <span class="<?php echo strtolower($approval_status); ?>">
                <?php echo ucfirst($approval_status); ?>
            </span>
        </h3>
        
        <table>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Address</th>
            
                
            </tr>
            <tr>
                <td><?php echo $clientData['name']; ?></td>
                <td><?php echo $clientData['phone']; ?></td>
                <td><?php echo $clientData['address']; ?></td>
                
            </tr>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

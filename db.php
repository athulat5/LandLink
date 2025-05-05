<?php
require 'vendor/autoload.php'; 

$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->industrial_park;
$usersCollection = $database->users;
$staffCollection = $database->staff;
$industriesCollection = $database->industries;
$landCollection = $database->land;
$clientsCollection = $database->client;
// $landCollection = $database->land;
$rawMaterialCollection = $database->raw_material;
$gridFSBucket = $database->selectGridFSBucket();
$productsCollection = $database->products;
$messagesCollection = $database->messages;
$ordersCollection = $database->orders;
?>

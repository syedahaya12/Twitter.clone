<?php
// db.php - Database connection file

$host = 'localhost'; // Assuming localhost, change if needed
$dbname = 'dbrgf6pwzh5jsq';
$user = 'uannmukxu07nw';
$pass = 'nhh1divf0d2c';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<?php
// edit_profile.php - Action file

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$bio = $_POST['bio'];

$stmt = $pdo->prepare("UPDATE users SET bio = :bio WHERE id = :id");
$stmt->execute(['bio' => $bio, 'id' => $user_id]);

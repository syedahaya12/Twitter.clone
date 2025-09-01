<?php
// post_tweet.php - Action file

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$content = $_POST['content'];

$stmt = $pdo->prepare("INSERT INTO tweets (user_id, content) VALUES (:user_id, :content)");
$stmt->execute(['user_id' => $user_id, 'content' => $content]);

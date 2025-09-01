<?php
// like_tweet.php - Action file

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$tweet_id = $_POST['tweet_id'];

$stmt = $pdo->prepare("INSERT IGNORE INTO likes (user_id, tweet_id) VALUES (:user_id, :tweet_id)");
$stmt->execute(['user_id' => $user_id, 'tweet_id' => $tweet_id]);

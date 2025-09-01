<?php
// comment_tweet.php - Action file

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$tweet_id = $_POST['tweet_id'];
$content = $_POST['content'];

$stmt = $pdo->prepare("INSERT INTO comments (user_id, tweet_id, content) VALUES (:user_id, :tweet_id, :content)");
$stmt->execute(['user_id' => $user_id, 'tweet_id' => $tweet_id, 'content' => $content]);

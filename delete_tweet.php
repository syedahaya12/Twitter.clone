<?php
// delete_tweet.php - Action file

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$tweet_id = $_POST['tweet_id'];

$stmt = $pdo->prepare("DELETE FROM tweets WHERE id = :id AND user_id = :user_id");
$stmt->execute(['id' => $tweet_id, 'user_id' => $user_id]);

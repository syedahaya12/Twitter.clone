<?php
// follow_user.php - Action file

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) exit;

$follower_id = $_SESSION['user_id'];
$followed_id = $_POST['user_id'];
$action = $_POST['action'];

if ($action == 'follow') {
    $stmt = $pdo->prepare("INSERT IGNORE INTO follows (follower_id, followed_id) VALUES (:follower, :followed)");
    $stmt->execute(['follower' => $follower_id, 'followed' => $followed_id]);
} else {
    $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = :follower AND followed_id = :followed");
    $stmt->execute(['follower' => $follower_id, 'followed' => $followed_id]);
}

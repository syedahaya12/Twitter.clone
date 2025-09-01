<?php
// profile.php - User profile page

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

$user_id = $_SESSION['user_id'];
$profile_user_id = isset($_GET['id']) ? $_GET['id'] : $user_id;

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $profile_user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch tweets
$stmt = $pdo->prepare("SELECT * FROM tweets WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $profile_user_id]);
$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Followers count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM follows WHERE followed_id = :id");
$stmt->execute(['id' => $profile_user_id]);
$followers = $stmt->fetch()['count'];

// Following count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM follows WHERE follower_id = :id");
$stmt->execute(['id' => $profile_user_id]);
$following = $stmt->fetch()['count'];

// Check if following
$is_following = false;
if ($profile_user_id != $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM follows WHERE follower_id = :follower AND followed_id = :followed");
    $stmt->execute(['follower' => $user_id, 'followed' => $profile_user_id]);
    $is_following = $stmt->rowCount() > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - @<?php echo htmlspecialchars($user['username']); ?></title>
    <style>
        /* Internal CSS - Amazing, real-looking, responsive */
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .profile-header { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; text-align: center; }
        .profile-header img { width: 100px; height: 100px; border-radius: 50%; }
        .profile-header h1 { margin: 10px 0; }
        .profile-header p { color: #657786; }
        .stats { display: flex; justify-content: space-around; margin: 20px 0; }
        .stats div { text-align: center; }
        .stats strong { display: block; font-size: 18px; }
        .follow-btn { background: #1da1f2; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; }
        .follow-btn:hover { background: #0c85d0; }
        .tweets { }
        .tweet { background: white; padding: 15px; border-bottom: 1px solid #ddd; }
        @media (max-width: 600px) { .container { padding: 10px; } .stats { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile">
            <h1>@<?php echo htmlspecialchars($user['username']); ?></h1>
            <p><?php echo htmlspecialchars($user['bio']); ?></p>
            <div class="stats">
                <div><strong><?php echo $following; ?></strong> Following</div>
                <div><strong><?php echo $followers; ?></strong> Followers</div>
            </div>
            <?php if ($profile_user_id == $user_id): ?>
                <button class="follow-btn" onclick="editProfile()">Edit Profile</button>
            <?php else: ?>
                <button class="follow-btn" onclick="followUser(<?php echo $profile_user_id; ?>, <?php echo $is_following ? 'true' : 'false'; ?>)">
                    <?php echo $is_following ? 'Unfollow' : 'Follow'; ?>
                </button>
            <?php endif; ?>
        </div>
        <div class="tweets">
            <h2>Tweets</h2>
            <?php foreach ($tweets as $tweet): ?>
                <div class="tweet">
                    <p><?php echo htmlspecialchars($tweet['content']); ?></p>
                    <div class="tweet-time"><?php echo $tweet['created_at']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        // Inline JS
        function followUser(userId, isFollowing) {
            fetch('follow_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `user_id=${userId}&action=${isFollowing ? 'unfollow' : 'follow'}`
            }).then(() => window.location.reload());
        }

        function editProfile() {
            const bio = prompt('Update bio:');
            if (bio === null) return;
            fetch('edit_profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `bio=${encodeURIComponent(bio)}`
            }).then(() => window.location.reload());
        }
    </script>
</body>
</html>

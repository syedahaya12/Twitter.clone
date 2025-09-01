<?php
// index.php - Homepage with feed and tweet box

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in, but using JS as per instruction
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch feed: tweets from followed users and own
$stmt = $pdo->prepare("
    SELECT t.*, u.username, u.profile_pic 
    FROM tweets t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.user_id = :user_id OR t.user_id IN (SELECT followed_id FROM follows WHERE follower_id = :user_id)
    ORDER BY t.created_at DESC LIMIT 50
");
$stmt->execute(['user_id' => $user_id]);
$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitter Clone - Home</title>
    <style>
        /* Internal CSS - Amazing, real-looking, responsive */
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .tweet-box { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .tweet-box textarea { width: 100%; height: 80px; border: 1px solid #ddd; border-radius: 4px; padding: 10px; font-size: 16px; }
        .tweet-box button { background: #1da1f2; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; }
        .tweet-box button:hover { background: #0c85d0; }
        .feed { }
        .tweet { background: white; padding: 15px; border-bottom: 1px solid #ddd; display: flex; }
        .tweet img { width: 50px; height: 50px; border-radius: 50%; margin-right: 10px; }
        .tweet-content { flex: 1; }
        .tweet-header { font-weight: bold; }
        .tweet-time { color: #657786; font-size: 14px; }
        .tweet-actions { margin-top: 10px; }
        .tweet-actions button { background: none; border: none; color: #657786; cursor: pointer; margin-right: 20px; }
        .tweet-actions button:hover { color: #1da1f2; }
        @media (max-width: 600px) { .container { padding: 10px; } .tweet { flex-direction: column; align-items: center; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Twitter Clone</h1>
        <div class="tweet-box">
            <form id="tweetForm">
                <textarea id="tweetContent" placeholder="What's happening?"></textarea>
                <button type="button" onclick="postTweet()">Tweet</button>
            </form>
        </div>
        <div class="feed">
            <?php foreach ($tweets as $tweet): ?>
                <div class="tweet">
                    <img src="<?php echo htmlspecialchars($tweet['profile_pic']); ?>" alt="Profile">
                    <div class="tweet-content">
                        <div class="tweet-header">@<?php echo htmlspecialchars($tweet['username']); ?></div>
                        <div class="tweet-time"><?php echo $tweet['created_at']; ?></div>
                        <p><?php echo htmlspecialchars($tweet['content']); ?></p>
                        <div class="tweet-actions">
                            <button onclick="likeTweet(<?php echo $tweet['id']; ?>)">Like</button>
                            <button onclick="commentTweet(<?php echo $tweet['id']; ?>)">Comment</button>
                            <?php if ($tweet['user_id'] == $user_id): ?>
                                <button onclick="editTweet(<?php echo $tweet['id']; ?>)">Edit</button>
                                <button onclick="deleteTweet(<?php echo $tweet['id']; ?>)">Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        // Inline JS for actions and redirections
        function postTweet() {
            const content = document.getElementById('tweetContent').value;
            if (!content) return;
            fetch('post_tweet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `content=${encodeURIComponent(content)}`
            }).then(response => response.text()).then(() => {
                window.location.reload(); // JS redirect/refresh
            });
        }

        function likeTweet(tweetId) {
            fetch('like_tweet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `tweet_id=${tweetId}`
            }).then(() => alert('Liked!'));
        }

        function commentTweet(tweetId) {
            const comment = prompt('Enter comment:');
            if (!comment) return;
            fetch('comment_tweet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `tweet_id=${tweetId}&content=${encodeURIComponent(comment)}`
            }).then(() => alert('Commented!'));
        }

        function editTweet(tweetId) {
            const newContent = prompt('Edit tweet:');
            if (!newContent) return;
            fetch('edit_tweet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `tweet_id=${tweetId}&content=${encodeURIComponent(newContent)}`
            }).then(() => window.location.reload());
        }

        function deleteTweet(tweetId) {
            if (confirm('Delete tweet?')) {
                fetch('delete_tweet.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `tweet_id=${tweetId}`
                }).then(() => window.location.reload());
            }
        }
    </script>
</body>
</html>

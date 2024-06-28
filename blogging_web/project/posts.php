<?php

global $conn;
include 'components/connect.php'; // Assuming this file contains mysqli connection code

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'components/like_post.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>

    <!-- Font awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="posts-container">

    <h1 class="heading">Latest Posts</h1>

    <div class="box-container">

        <?php
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ?");
        $status = 'active'; // Assuming 'active' is the status for active posts
        $select_posts->bind_param("s", $status);
        $select_posts->execute();
        $result = $select_posts->get_result();

        if ($result->num_rows > 0) {
            while ($fetch_posts = $result->fetch_assoc()) {

                $post_id = $fetch_posts['id'];

                // Count comments for the post
                $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
                $count_post_comments->bind_param("i", $post_id);
                $count_post_comments->execute();
                $count_comments_result = $count_post_comments->get_result();
                $total_post_comments = $count_comments_result->num_rows;

                // Count likes for the post
                $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
                $count_post_likes->bind_param("i", $post_id);
                $count_post_likes->execute();
                $count_likes_result = $count_post_likes->get_result();
                $total_post_likes = $count_likes_result->num_rows;

                // Check if user has liked the post
                $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
                $confirm_likes->bind_param("ii", $user_id, $post_id);
                $confirm_likes->execute();
                $confirm_likes_result = $confirm_likes->get_result();
                ?>
                <form class="box" method="post">
                    <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                    <input type="hidden" name="admin_id" value="<?= $fetch_posts['admin_id']; ?>">
                    <div class="post-admin">
                        <i class="fas fa-user"></i>
                        <div>
                            <a href="author_posts.php?author=<?= $fetch_posts['name']; ?>"><?= $fetch_posts['name']; ?></a>
                            <div><?= $fetch_posts['date']; ?></div>
                        </div>
                    </div>

                    <?php
                    if (!empty($fetch_posts['image'])) {
                        ?>
                        <img src="uploaded_img/<?= $fetch_posts['image']; ?>" class="post-image" alt="">
                        <?php
                    }
                    ?>
                    <div class="post-title"><?= $fetch_posts['title']; ?></div>
                    <div class="post-content content-150"><?= $fetch_posts['content']; ?></div>
                    <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">Read More</a>
                    <a href="category.php?category=<?= $fetch_posts['category']; ?>" class="post-cat">
                        <i class="fas fa-tag"></i> <span><?= $fetch_posts['category']; ?></span>
                    </a>
                    <div class="icons">
                        <a href="view_post.php?post_id=<?= $post_id; ?>">
                            <i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span>
                        </a>
                        <button type="submit" name="like_post">
                            <i class="fas fa-heart" style="<?php if ($confirm_likes_result->num_rows > 0) { echo 'color: var(--red);'; } ?>"></i>
                            <span>(<?= $total_post_likes; ?>)</span>
                        </button>
                    </div>
                </form>
                <?php
            }
        } else {
            echo '<p class="empty">No posts added yet!</p>';
        }
        ?>
    </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- Custom JS file link -->
<script src="js/script.js"></script>

</body>
</html>

<?php

global $conn;
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_POST['delete'])) {

    $p_id = $_POST['post_id'];
    $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);

    // Delete image from the server
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $delete_image->bind_param("i", $p_id);
    $delete_image->execute();
    $result_delete_image = $delete_image->get_result();
    $fetch_delete_image = $result_delete_image->fetch_assoc();

    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }

    // Delete post
    $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
    $delete_post->bind_param("i", $p_id);
    $delete_post->execute();

    // Delete associated comments
    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
    $delete_comments->bind_param("i", $p_id);
    $delete_comments->execute();

    $message[] = 'Post deleted successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="show-posts">
    <h1 class="heading">Your Posts</h1>

    <div class="box-container">

        <?php
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
        $select_posts->bind_param("i", $admin_id);
        $select_posts->execute();
        $result_posts = $select_posts->get_result();

        if ($result_posts->num_rows > 0) {
            while ($fetch_posts = $result_posts->fetch_assoc()) {
                $post_id = $fetch_posts['id'];

                // Count comments for the post
                $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
                $count_post_comments->bind_param("i", $post_id);
                $count_post_comments->execute();
                $result_comments = $count_post_comments->get_result();
                $total_post_comments = $result_comments->num_rows;

                // Count likes for the post
                $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
                $count_post_likes->bind_param("i", $post_id);
                $count_post_likes->execute();
                $result_likes = $count_post_likes->get_result();
                $total_post_likes = $result_likes->num_rows;
                ?>
                <form method="post" class="box">
                    <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id); ?>">
                    <?php if ($fetch_posts['image'] != '') { ?>
                        <img src="../uploaded_img/<?= htmlspecialchars($fetch_posts['image']); ?>" class="image" alt="">
                    <?php } ?>
                    <div class="status" style="background-color:<?php if ($fetch_posts['status'] == 'active') { echo 'limegreen'; } else { echo 'coral'; } ?>;">
                        <?= htmlspecialchars($fetch_posts['status']); ?>
                    </div>
                    <div class="title"><?= htmlspecialchars($fetch_posts['title']); ?></div>
                    <div class="posts-content"><?= htmlspecialchars($fetch_posts['content']); ?></div>
                    <div class="icons">
                        <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
                        <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
                    </div>
                    <div class="flex-btn">
                        <a href="edit_post.php?id=<?= htmlspecialchars($post_id); ?>" class="option-btn">Edit</a>
                        <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Delete this post?');">Delete</button>
                    </div>
                    <a href="read_post.php?post_id=<?= htmlspecialchars($post_id); ?>" class="btn">View Post</a>
                </form>
                <?php
            }
        } else {
            echo '<p class="empty">No posts added yet! <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">Add Post</a></p>';
        }
        ?>

    </div>
</section>

<!-- Custom JS file link -->
<script src="../js/admin_script.js"></script>

</body>
</html>

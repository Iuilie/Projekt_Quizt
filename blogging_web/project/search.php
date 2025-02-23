<?php

global $conn;
include 'components/connect.php';

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
    <title>Search Page</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
if (isset($_POST['search_box']) || isset($_POST['search_btn'])) {
    ?>
    <section class="posts-container">

        <div class="box-container">

            <?php
            $search_box = htmlspecialchars($_POST['search_box'], ENT_QUOTES, 'UTF-8');
            $status = 'active';
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE (title LIKE ? OR category LIKE ?) AND status = ?");
            $search_param = "%{$search_box}%";
            $select_posts->bind_param("sss", $search_param, $search_param, $status);
            $select_posts->execute();
            $result_posts = $select_posts->get_result();

            if ($result_posts->num_rows > 0) {
                while ($fetch_posts = $result_posts->fetch_assoc()) {

                    $post_id = $fetch_posts['id'];

                    $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
                    $count_post_comments->bind_param("i", $post_id);
                    $count_post_comments->execute();
                    $result_post_comments = $count_post_comments->get_result();
                    $total_post_comments = $result_post_comments->num_rows;

                    $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
                    $count_post_likes->bind_param("i", $post_id);
                    $count_post_likes->execute();
                    $result_post_likes = $count_post_likes->get_result();
                    $total_post_likes = $result_post_likes->num_rows;

                    $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
                    $confirm_likes->bind_param("ii", $user_id, $post_id);
                    $confirm_likes->execute();
                    $result_confirm_likes = $confirm_likes->get_result();
                    ?>
                    <form class="box" method="post">
                        <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                        <input type="hidden" name="admin_id" value="<?= $fetch_posts['admin_id']; ?>">
                        <div class="post-admin">
                            <i class="fas fa-user"></i>
                            <div>
                                <a href="author_posts.php?author=<?= htmlspecialchars($fetch_posts['name'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($fetch_posts['name'], ENT_QUOTES, 'UTF-8'); ?></a>
                                <div><?= htmlspecialchars($fetch_posts['date'], ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>

                        <?php
                        if ($fetch_posts['image'] != '') {
                            ?>
                            <img src="uploaded_img/<?= htmlspecialchars($fetch_posts['image'], ENT_QUOTES, 'UTF-8'); ?>" class="post-image" alt="">
                            <?php
                        }
                        ?>
                        <div class="post-title"><?= htmlspecialchars($fetch_posts['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="post-content content-150"><?= htmlspecialchars($fetch_posts['content'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">Read More</a>
                        <a href="category.php?category=<?= htmlspecialchars($fetch_posts['category'], ENT_QUOTES, 'UTF-8'); ?>" class="post-cat"> <i class="fas fa-tag"></i> <span><?= htmlspecialchars($fetch_posts['category'], ENT_QUOTES, 'UTF-8'); ?></span></a>
                        <div class="icons">
                            <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
                            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($result_confirm_likes->num_rows > 0) { echo 'color:var(--red);'; } ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
                        </div>

                    </form>
                    <?php
                }
            } else {
                echo '<p class="empty">No results found!</p>';
            }
            ?>
        </div>

    </section>

    <?php
} else {
    echo '<section><p class="empty">Search something!</p></section>';
}
?>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

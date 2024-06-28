<?php
global $conn, $fetch_profile;
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'components/like_post.php';

$get_id = $_GET['post_id'];
$get_id = filter_var($get_id, FILTER_SANITIZE_STRING);

if (isset($_POST['add_comment'])) {
    $admin_id = filter_var($_POST['admin_id'], FILTER_SANITIZE_STRING);
    $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
    $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);

    $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ? AND admin_id = ? AND user_id = ? AND user_name = ? AND comment = ?");
    $verify_comment->bind_param("iiiss", $get_id, $admin_id, $user_id, $user_name, $comment);
    $verify_comment->execute();
    $verify_comment->store_result();

    if ($verify_comment->num_rows > 0) {
        $message[] = 'comment already added!';
    } else {
        $insert_comment = $conn->prepare("INSERT INTO `comments` (post_id, admin_id, user_id, user_name, comment) VALUES (?, ?, ?, ?, ?)");
        $insert_comment->bind_param("iiiss", $get_id, $admin_id, $user_id, $user_name, $comment);
        $insert_comment->execute();
        $message[] = 'new comment added!';
        $insert_comment->close();
    }

    $verify_comment->close();
}

if (isset($_POST['edit_comment'])) {
    $edit_comment_id = filter_var($_POST['edit_comment_id'], FILTER_SANITIZE_STRING);
    $comment_edit_box = filter_var($_POST['comment_edit_box'], FILTER_SANITIZE_STRING);

    $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE comment = ? AND id = ?");
    $verify_comment->bind_param("si", $comment_edit_box, $edit_comment_id);
    $verify_comment->execute();
    $verify_comment->store_result();

    if ($verify_comment->num_rows > 0) {
        $message[] = 'comment already added!';
    } else {
        $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
        $update_comment->bind_param("si", $comment_edit_box, $edit_comment_id);
        $update_comment->execute();
        $message[] = 'your comment edited successfully!';
        $update_comment->close();
    }

    $verify_comment->close();
}

if (isset($_POST['delete_comment'])) {
    $delete_comment_id = filter_var($_POST['comment_id'], FILTER_SANITIZE_STRING);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
    $delete_comment->bind_param("i", $delete_comment_id);
    $delete_comment->execute();
    $message[] = 'comment deleted successfully!';
    $delete_comment->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view post</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<?php
if (isset($_POST['open_edit_box'])) {
    $comment_id = filter_var($_POST['comment_id'], FILTER_SANITIZE_STRING);
    ?>
    <section class="comment-edit-form">
        <p>edit your comment</p>
        <?php
        $select_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
        $select_edit_comment->bind_param("i", $comment_id);
        $select_edit_comment->execute();
        $result_edit_comment = $select_edit_comment->get_result();
        $fetch_edit_comment = $result_edit_comment->fetch_assoc();
        ?>
        <form action="" method="POST">
            <input type="hidden" name="edit_comment_id" value="<?= htmlspecialchars($comment_id); ?>">
            <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="please enter your comment"><?= htmlspecialchars($fetch_edit_comment['comment']); ?></textarea>
            <button type="submit" class="inline-btn" name="edit_comment">edit comment</button>
            <div class="inline-option-btn" onclick="window.location.href = 'view_post.php?post_id=<?= htmlspecialchars($get_id); ?>';">cancel edit</div>
        </form>
    </section>
    <?php
    $select_edit_comment->close();
}
?>

<section class="posts-container" style="padding-bottom: 0;">
    <div class="box-container">
        <?php
        $status = 'active';
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? AND id = ?");
        $select_posts->bind_param("si", $status, $get_id);
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
                    <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id); ?>">
                    <input type="hidden" name="admin_id" value="<?= htmlspecialchars($fetch_posts['admin_id']); ?>">
                    <div class="post-admin">
                        <i class="fas fa-user"></i>
                        <div>
                            <a href="author_posts.php?author=<?= htmlspecialchars($fetch_posts['name']); ?>"><?= htmlspecialchars($fetch_posts['name']); ?></a>
                            <div><?= htmlspecialchars($fetch_posts['date']); ?></div>
                        </div>
                    </div>

                    <?php if ($fetch_posts['image'] != '') { ?>
                        <img src="uploaded_img/<?= htmlspecialchars($fetch_posts['image']); ?>" class="post-image" alt="">
                    <?php } ?>
                    <div class="post-title"><?= htmlspecialchars($fetch_posts['title']); ?></div>
                    <div class="post-content"><?= htmlspecialchars($fetch_posts['content']); ?></div>
                    <div class="icons">
                        <div><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></div>
                        <button type="submit" name="like_post"><i class="fas fa-heart" style="<?= ($result_confirm_likes->num_rows > 0) ? 'color:var(--red);' : ''; ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
                    </div>
                </form>
                <?php
                $confirm_likes->close();
                $count_post_likes->close();
                $count_post_comments->close();
            }
        } else {
            echo '<p class="empty">no posts found!</p>';
        }
        $select_posts->close();
        ?>
    </div>
</section>

<section class="comments-container">
    <p class="comment-title">add comment</p>
    <?php
    if ($user_id != '') {
        $select_admin_id = $conn->prepare("SELECT admin_id FROM `posts` WHERE id = ?");
        $select_admin_id->bind_param("i", $get_id);
        $select_admin_id->execute();
        $result_admin_id = $select_admin_id->get_result();
        $fetch_admin_id = $result_admin_id->fetch_assoc();
        ?>
        <form action="" method="post" class="add-comment">
            <input type="hidden" name="admin_id" value="<?= htmlspecialchars($fetch_admin_id['admin_id']); ?>">
            <input type="hidden" name="user_name" value="<?= htmlspecialchars($fetch_profile['name']); ?>">
            <p class="user"><i class="fas fa-user"></i><a href="update.php"><?= htmlspecialchars($fetch_profile['name']); ?></a></p>
            <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="write your comment" required></textarea>
            <input type="submit" value="add comment" class="inline-btn" name="add_comment">
        </form>
        <?php
        $select_admin_id->close();
    } else {
        ?>
        <div class="add-comment">
            <p>please login to add or edit your comment</p>
            <a href="login.php" class="inline-btn">login now</a>
        </div>
    <?php } ?>

    <p class="comment-title">post comments</p>
    <div class="user-comments-container">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
        $select_comments->bind_param("i", $get_id);
        $select_comments->execute();
        $result_comments = $select_comments->get_result();
        if ($result_comments->num_rows > 0) {
            while ($fetch_comments = $result_comments->fetch_assoc()) {
                ?>
                <div class="show-comments" style="<?= ($fetch_comments['user_id'] == $user_id) ? 'order:-1;' : ''; ?>">
                    <div class="comment-user">
                        <i class="fas fa-user"></i>
                        <div>
                            <span><?= htmlspecialchars($fetch_comments['user_name']); ?></span>
                            <div><?= htmlspecialchars($fetch_comments['date']); ?></div>
                        </div>
                    </div>
                    <div class="comment-box" style="<?= ($fetch_comments['user_id'] == $user_id) ? 'color:var(--white); background:var(--black);' : ''; ?>"><?= htmlspecialchars($fetch_comments['comment']); ?></div>
                    <?php if ($fetch_comments['user_id'] == $user_id) { ?>
                        <form action="" method="POST">
                            <input type="hidden" name="comment_id" value="<?= htmlspecialchars($fetch_comments['id']); ?>">
                            <button type="submit" class="inline-option-btn" name="open_edit_box">edit comment</button>
                            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">delete comment</button>
                        </form>
                    <?php } ?>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">no comments added yet!</p>';
        }
        $select_comments->close();
        ?>
    </div>
</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>

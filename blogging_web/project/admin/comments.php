<?php
global $conn;
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    $comment_id = filter_var($comment_id, FILTER_SANITIZE_STRING);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
    $delete_comment->bind_param('i', $comment_id);
    if ($delete_comment->execute()) {
        $message[] = 'Comment deleted!';
    } else {
        $message[] = 'Failed to delete comment!';
    }
    $delete_comment->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Accounts</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="comments">

    <h1 class="heading">Posts Comments</h1>

    <p class="comment-title">Post Comments</p>
    <div class="box-container">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
        $select_comments->bind_param('i', $admin_id);
        $select_comments->execute();
        $result_comments = $select_comments->get_result();

        if ($result_comments->num_rows > 0) {
            while ($fetch_comments = $result_comments->fetch_assoc()) {
                $post_id = $fetch_comments['post_id'];
                ?>
                <?php
                $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
                $select_posts->bind_param('i', $post_id);
                $select_posts->execute();
                $result_posts = $select_posts->get_result();

                while ($fetch_posts = $result_posts->fetch_assoc()) {
                    ?>
                    <div class="post-title">from : <span><?= htmlspecialchars($fetch_posts['title']); ?></span> <a href="read_post.php?post_id=<?= htmlspecialchars($fetch_posts['id']); ?>" >view post</a></div>
                    <?php
                }
                $select_posts->close();
                ?>
                <div class="box">
                    <div class="user">
                        <i class="fas fa-user"></i>
                        <div class="user-info">
                            <span><?= htmlspecialchars($fetch_comments['user_name']); ?></span>
                            <div><?= htmlspecialchars($fetch_comments['date']); ?></div>
                        </div>
                    </div>
                    <div class="text"><?= htmlspecialchars($fetch_comments['comment']); ?></div>
                    <form action="" method="POST">
                        <input type="hidden" name="comment_id" value="<?= htmlspecialchars($fetch_comments['id']); ?>">
                        <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('Delete this comment?');">Delete Comment</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">No comments added yet!</p>';
        }
        $select_comments->close();
        ?>
    </div>

</section>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>

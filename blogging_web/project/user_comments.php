<?php

global $conn;
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else{
    $user_id = '';
    header('location:/project/home.php');
    exit();
}

if(isset($_POST['edit_comment'])){

    $edit_comment_id = $_POST['edit_comment_id'];
    $edit_comment_id = filter_var($edit_comment_id, FILTER_SANITIZE_STRING);
    $comment_edit_box = $_POST['comment_edit_box'];
    $comment_edit_box = filter_var($comment_edit_box, FILTER_SANITIZE_STRING);

    $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE comment = ? AND id = ?");
    $verify_comment->bind_param("si", $comment_edit_box, $edit_comment_id);
    $verify_comment->execute();
    $verify_comment->store_result();

    if($verify_comment->num_rows > 0){
        $message[] = 'comment already added!';
    }else{
        $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
        $update_comment->bind_param("si", $comment_edit_box, $edit_comment_id);
        $update_comment->execute();
        $message[] = 'your comment edited successfully!';
    }

}

if(isset($_POST['delete_comment'])){
    $delete_comment_id = $_POST['comment_id'];
    $delete_comment_id = filter_var($delete_comment_id, FILTER_SANITIZE_STRING);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
    $delete_comment->bind_param("i", $delete_comment_id);
    $delete_comment->execute();
    $message[] = 'comment deleted successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update profile</title>

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
if(isset($_POST['open_edit_box'])){
    $comment_id = $_POST['comment_id'];
    $comment_id = filter_var($comment_id, FILTER_SANITIZE_STRING);
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
            <div class="inline-option-btn" onclick="window.location.href = 'user_comments.php';">cancel edit</div>
        </form>
    </section>
    <?php
}
?>

<section class="comments-container">

    <h1 class="heading">your comments</h1>

    <p class="comment-title">your comments on the posts</p>
    <div class="user-comments-container">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
        $select_comments->bind_param("i", $user_id);
        $select_comments->execute();
        $result_comments = $select_comments->get_result();
        if($result_comments->num_rows > 0){
            while($fetch_comments = $result_comments->fetch_assoc()){
                ?>
                <div class="show-comments">
                    <?php
                    $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
                    $select_posts->bind_param("i", $fetch_comments['post_id']);
                    $select_posts->execute();
                    $result_posts = $select_posts->get_result();
                    while($fetch_posts = $result_posts->fetch_assoc()){
                        ?>
                        <div class="post-title"> from : <span><?= htmlspecialchars($fetch_posts['title']); ?></span> <a href="view_post.php?post_id=<?= htmlspecialchars($fetch_posts['id']); ?>" >view post</a></div>
                        <?php
                    }
                    ?>
                    <div class="comment-box"><?= htmlspecialchars($fetch_comments['comment']); ?></div>
                    <form action="" method="POST">
                        <input type="hidden" name="comment_id" value="<?= htmlspecialchars($fetch_comments['id']); ?>">
                        <button type="submit" class="inline-option-btn" name="open_edit_box">edit comment</button>
                        <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">delete comment</button>
                    </form>
                </div>
                <?php
            }
        }else{
            echo '<p class="empty">no comments added yet!</p>';
        }
        ?>
    </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>

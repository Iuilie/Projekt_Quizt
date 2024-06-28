<?php

global $conn;
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:admin_login.php');
}

$get_id = $_GET['post_id'];

if(isset($_POST['delete'])){

    $p_id = $_POST['post_id'];
    $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $delete_image->bind_param("i", $p_id);
    $delete_image->execute();
    $result_delete_image = $delete_image->get_result();
    $fetch_delete_image = $result_delete_image->fetch_assoc();
    if($fetch_delete_image['image'] != ''){
        unlink('../uploaded_img/'.$fetch_delete_image['image']);
    }
    $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
    $delete_post->bind_param("i", $p_id);
    $delete_post->execute();
    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
    $delete_comments->bind_param("i", $p_id);
    $delete_comments->execute();
    header('location:view_posts.php');
}

if(isset($_POST['delete_comment'])){

    $comment_id = $_POST['comment_id'];
    $comment_id = filter_var($comment_id, FILTER_SANITIZE_STRING);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
    $delete_comment->bind_param("i", $comment_id);
    $delete_comment->execute();
    $message[] = 'comment deleted!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>posts</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="read-post">

    <?php
    $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND id = ?");
    $select_posts->bind_param("ii", $admin_id, $get_id);
    $select_posts->execute();
    $result_posts = $select_posts->get_result();
    if($result_posts->num_rows > 0){
        while($fetch_posts = $result_posts->fetch_assoc()){
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

            ?>
            <form method="post">
                <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                <div class="status" style="background-color:<?php if($fetch_posts['status'] == 'active'){echo 'limegreen'; }else{echo 'coral';} ?>;"><?= htmlspecialchars($fetch_posts['status']); ?></div>
                <?php if($fetch_posts['image'] != ''){ ?>
                    <img src="../uploaded_img/<?= htmlspecialchars($fetch_posts['image']); ?>" class="image" alt="">
                <?php } ?>
                <div class="title"><?= htmlspecialchars($fetch_posts['title']); ?></div>
                <div class="content"><?= htmlspecialchars($fetch_posts['content']); ?></div>
                <div class="icons">
                    <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
                    <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
                </div>
                <div class="flex-btn">
                    <a href="edit_post.php?id=<?= $post_id; ?>" class="inline-option-btn">edit</a>
                    <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('delete this post?');">delete</button>
                    <a href="view_posts.php" class="inline-option-btn">go back</a>
                </div>
            </form>
            <?php
        }
    }else{
        echo '<p class="empty">no posts added yet! <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">add post</a></p>';
    }
    ?>

</section>

<section class="comments" style="padding-top: 0;">

    <p class="comment-title">post comments</p>
    <div class="box-container">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
        $select_comments->bind_param("i", $get_id);
        $select_comments->execute();
        $result_comments = $select_comments->get_result();
        if($result_comments->num_rows > 0){
            while($fetch_comments = $result_comments->fetch_assoc()){
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
                        <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
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

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>

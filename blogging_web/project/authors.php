<?php

global $conn;
include 'components/connect.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

include 'components/like_post.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>author</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->


<section class="authors">

    <h1 class="heading">authors</h1>

    <div class="box-container">

        <?php
        $select_author = $conn->prepare("SELECT * FROM `admin`");
        $select_author->execute();
        $result_authors = $select_author->get_result();

        if($result_authors->num_rows > 0){
            while($fetch_authors = $result_authors->fetch_assoc()) {

                $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
                $count_admin_posts->bind_param("is", $admin_id, $status);
                $admin_id = $fetch_authors['id'];
                $status = 'active';
                $count_admin_posts->execute();
                $result_admin_posts = $count_admin_posts->get_result();
                $total_admin_posts = $result_admin_posts->num_rows;

                $count_admin_likes = $conn->prepare("SELECT * FROM `likes` WHERE admin_id = ?");
                $count_admin_likes->bind_param("i", $admin_id);
                $count_admin_likes->execute();
                $result_admin_likes = $count_admin_likes->get_result();
                $total_admin_likes = $result_admin_likes->num_rows;

                $count_admin_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
                $count_admin_comments->bind_param("i", $admin_id);
                $count_admin_comments->execute();
                $result_admin_comments = $count_admin_comments->get_result();
                $total_admin_comments = $result_admin_comments->num_rows;

                ?>
                <div class="box">
                    <p>author : <span><?= htmlspecialchars($fetch_authors['name']); ?></span></p>
                    <p>total posts : <span><?= $total_admin_posts; ?></span></p>
                    <p>posts likes : <span><?= $total_admin_likes; ?></span></p>
                    <p>posts comments : <span><?= $total_admin_comments; ?></span></p>
                    <a href="author_posts.php?author=<?= htmlspecialchars($fetch_authors['name']); ?>" class="btn">view posts</a>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">no authors found</p>';
        }
        ?>

    </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>

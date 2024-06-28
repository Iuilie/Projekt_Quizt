<?php

global $fetch_profile, $conn;
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- admin dashboard section starts  -->

<section class="dashboard">

    <h1 class="heading">Dashboard</h1>

    <div class="box-container">

        <div class="box">
            <h3>Welcome!</h3>
            <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
            <a href="update_profile.php" class="btn">Update Profile</a>
        </div>

        <div class="box">
            <?php
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
            $select_posts->bind_param("i", $admin_id);
            $select_posts->execute();
            $result_posts = $select_posts->get_result();
            $numbers_of_posts = $result_posts->num_rows;
            ?>
            <h3><?= $numbers_of_posts; ?></h3>
            <p>Posts Added</p>
            <a href="add_posts.php" class="btn">Add New Post</a>
        </div>

        <div class="box">
            <?php
            $select_active_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
            $status_active = 'active';
            $select_active_posts->bind_param("is", $admin_id, $status_active);
            $select_active_posts->execute();
            $result_active_posts = $select_active_posts->get_result();
            $numbers_of_active_posts = $result_active_posts->num_rows;
            ?>
            <h3><?= $numbers_of_active_posts; ?></h3>
            <p>Active Posts</p>
            <a href="view_posts.php" class="btn">See Posts</a>
        </div>

        <div class="box">
            <?php
            $select_deactive_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
            $status_deactive = 'deactive';
            $select_deactive_posts->bind_param("is", $admin_id, $status_deactive);
            $select_deactive_posts->execute();
            $result_deactive_posts = $select_deactive_posts->get_result();
            $numbers_of_deactive_posts = $result_deactive_posts->num_rows;
            ?>
            <h3><?= $numbers_of_deactive_posts; ?></h3>
            <p>Deactive Posts</p>
            <a href="view_posts.php" class="btn">See Posts</a>
        </div>

        <div class="box">
            <?php
            $select_users = $conn->prepare("SELECT * FROM `users`");
            $select_users->execute();
            $result_users = $select_users->get_result();
            $numbers_of_users = $result_users->num_rows;
            ?>
            <h3><?= $numbers_of_users; ?></h3>
            <p>Users Account</p>
            <a href="users_accounts.php" class="btn">See Users</a>
        </div>

        <div class="box">
            <?php
            $select_admins = $conn->prepare("SELECT * FROM `admin`");
            $select_admins->execute();
            $result_admins = $select_admins->get_result();
            $numbers_of_admins = $result_admins->num_rows;
            ?>
            <h3><?= $numbers_of_admins; ?></h3>
            <p>Admins Account</p>
            <a href="admin_accounts.php" class="btn">See Admins</a>
        </div>

        <div class="box">
            <?php
            $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
            $select_comments->bind_param("i", $admin_id);
            $select_comments->execute();
            $result_comments = $select_comments->get_result();
            $numbers_of_comments = $result_comments->num_rows;
            ?>
            <h3><?= $numbers_of_comments; ?></h3>
            <p>Comments Added</p>
            <a href="comments.php" class="btn">See Comments</a>
        </div>

        <div class="box">
            <?php
            $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE admin_id = ?");
            $select_likes->bind_param("i", $admin_id);
            $select_likes->execute();
            $result_likes = $select_likes->get_result();
            $numbers_of_likes = $result_likes->num_rows;
            ?>
            <h3><?= $numbers_of_likes; ?></h3>
            <p>Total Likes</p>
            <a href="view_posts.php" class="btn">See Posts</a>
        </div>

    </div>

</section>

<!-- admin dashboard section ends -->

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>

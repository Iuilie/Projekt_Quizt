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
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
    $delete_image->bind_param("i", $admin_id);
    $delete_image->execute();
    $result_delete_image = $delete_image->get_result();
    while ($fetch_delete_image = $result_delete_image->fetch_assoc()) {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }

    $delete_posts = $conn->prepare("DELETE FROM `posts` WHERE admin_id = ?");
    $delete_posts->bind_param("i", $admin_id);
    $delete_posts->execute();

    $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE admin_id = ?");
    $delete_likes->bind_param("i", $admin_id);
    $delete_likes->execute();

    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE admin_id = ?");
    $delete_comments->bind_param("i", $admin_id);
    $delete_comments->execute();

    $delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
    $delete_admin->bind_param("i", $admin_id);
    $delete_admin->execute();

    header('location:../components/admin_logout.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Accounts</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- admins accounts section starts  -->

<section class="accounts">

    <h1 class="heading">Admins Account</h1>

    <div class="box-container">

        <div class="box" style="order: -2;">
            <p>Register New Admin</p>
            <a href="register_admin.php" class="option-btn" style="margin-bottom: .5rem;">Register</a>
        </div>

        <?php
        $select_account = $conn->prepare("SELECT * FROM `admin`");
        $select_account->execute();
        $result_account = $select_account->get_result();
        if ($result_account->num_rows > 0) {
            while ($fetch_accounts = $result_account->fetch_assoc()) {

                $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
                $count_admin_posts->bind_param("i", $fetch_accounts['id']);
                $count_admin_posts->execute();
                $result_admin_posts = $count_admin_posts->get_result();
                $total_admin_posts = $result_admin_posts->num_rows;

                ?>
                <div class="box" style="order: <?php if ($fetch_accounts['id'] == $admin_id) { echo '-1'; } ?>;">
                    <p> Admin ID : <span><?= htmlspecialchars($fetch_accounts['id']); ?></span> </p>
                    <p> Username : <span><?= htmlspecialchars($fetch_accounts['name']); ?></span> </p>
                    <p> Total Posts : <span><?= $total_admin_posts; ?></span> </p>
                    <div class="flex-btn">
                        <?php
                        if ($fetch_accounts['id'] == $admin_id) {
                            ?>
                            <a href="update_profile.php" class="option-btn" style="margin-bottom: .5rem;">Update</a>
                            <form action="" method="POST">
                                <input type="hidden" name="post_id" value="<?= htmlspecialchars($fetch_accounts['id']); ?>">
                                <button type="submit" name="delete" onclick="return confirm('Delete the account?');" class="delete-btn" style="margin-bottom: .5rem;">Delete</button>
                            </form>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">No accounts available</p>';
        }
        ?>

    </div>

</section>

<!-- admins accounts section ends -->

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>

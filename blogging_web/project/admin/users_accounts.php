<?php

global $conn;
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>users accounts</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- users accounts section starts -->

<section class="accounts">

    <h1 class="heading">users account</h1>

    <div class="box-container">

        <?php
        $select_account = $conn->prepare("SELECT * FROM `users`");
        $select_account->execute();
        $result = $select_account->get_result();
        if ($result->num_rows > 0) {
            while ($fetch_accounts = $result->fetch_assoc()) {
                $user_id = $fetch_accounts['id'];
                $count_user_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
                $count_user_comments->bind_param("i", $user_id);
                $count_user_comments->execute();
                $count_user_comments->store_result();
                $total_user_comments = $count_user_comments->num_rows;
                $count_user_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
                $count_user_likes->bind_param("i", $user_id);
                $count_user_likes->execute();
                $count_user_likes->store_result();
                $total_user_likes = $count_user_likes->num_rows;
                ?>
                <div class="box">
                    <p> users id : <span><?= $user_id; ?></span> </p>
                    <p> username : <span><?= $fetch_accounts['name']; ?></span> </p>
                    <p> total comments : <span><?= $total_user_comments; ?></span> </p>
                    <p> total likes : <span><?= $total_user_likes; ?></span> </p>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">no accounts available</p>';
        }
        ?>

    </div>

</section>

<!-- users accounts section ends -->

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>

</body>
</html>

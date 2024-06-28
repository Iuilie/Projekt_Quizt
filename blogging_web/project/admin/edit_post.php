<?php
global $conn;
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_POST['save'])) {
    $post_id = $_GET['id'];
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');

    $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ?");
    $update_post->bind_param("ssssi", $title, $content, $category, $status, $post_id);
    $update_post->execute();

    $message[] = 'Post updated!';

    $old_image = $_POST['old_image'];
    $image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES, 'UTF-8');
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
    $select_image->bind_param("si", $image, $admin_id);
    $select_image->execute();
    $select_image_result = $select_image->get_result();

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = 'Image size is too large!';
        } elseif ($select_image_result->num_rows > 0 && $image != '') {
            $message[] = 'Please rename your image!';
        } else {
            $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
            $update_image->bind_param("si", $image, $post_id);
            move_uploaded_file($image_tmp_name, $image_folder);
            $update_image->execute();
            if ($old_image != $image && $old_image != '') {
                unlink('../uploaded_img/' . $old_image);
            }
            $message[] = 'Image updated!';
        }
    }
}

if (isset($_POST['delete_post'])) {
    $post_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES, 'UTF-8');
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $delete_image->bind_param("i", $post_id);
    $delete_image->execute();
    $fetch_delete_image = $delete_image->get_result()->fetch_assoc();
    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
    $delete_post->bind_param("i", $post_id);
    $delete_post->execute();
    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
    $delete_comments->bind_param("i", $post_id);
    $delete_comments->execute();
    $message[] = 'Post deleted successfully!';
}

if (isset($_POST['delete_image'])) {
    $empty_image = '';
    $post_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES, 'UTF-8');
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $delete_image->bind_param("i", $post_id);
    $delete_image->execute();
    $fetch_delete_image = $delete_image->get_result()->fetch_assoc();
    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $unset_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
    $unset_image->bind_param("si", $empty_image, $post_id);
    $unset_image->execute();
    $message[] = 'Image deleted successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>
    <!-- Font awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<?php include '../components/admin_header.php' ?>
<section class="post-editor">
    <h1 class="heading">Edit Post</h1>
    <?php
    $post_id = $_GET['id'];
    $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $select_posts->bind_param("i", $post_id);
    $select_posts->execute();
    $result = $select_posts->get_result();
    if ($result->num_rows > 0) {
        while ($fetch_posts = $result->fetch_assoc()) {
            ?>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="old_image" value="<?= $fetch_posts['image']; ?>">
                <input type="hidden" name="post_id" value="<?= $fetch_posts['id']; ?>">
                <p>Post status <span>*</span></p>
                <select name="status" class="box" required>
                    <option value="<?= $fetch_posts['status']; ?>" selected><?= $fetch_posts['status']; ?></option>
                    <option value="active">active</option>
                    <option value="deactive">deactive</option>
                </select>
                <p>Post title <span>*</span></p>
                <input type="text" name="title" maxlength="100" required placeholder="Add post title" class="box" value="<?= $fetch_posts['title']; ?>">
                <p>Post content <span>*</span></p>
                <textarea name="content" class="box" required maxlength="10000" placeholder="Write your content..." cols="30" rows="10"><?= $fetch_posts['content']; ?></textarea>
                <p>Post category <span>*</span></p>
                <select name="category" class="box" required>
                    <option value="<?= $fetch_posts['category']; ?>" selected><?= $fetch_posts['category']; ?></option>
                    <option value="nature">nature</option>
                    <option value="education">education</option>
                    <option value="pets and animals">pets and animals</option>
                    <option value="technology">technology</option>
                    <option value="fashion">fashion</option>
                    <option value="entertainment">entertainment</option>
                    <option value="movies and animations">movies</option>
                    <option value="gaming">gaming</option>
                    <option value="music">music</option>
                    <option value="sports">sports</option>
                    <option value="news">news</option>
                    <option value="travel">travel</option>
                    <option value="comedy">comedy</option>
                    <option value="design and development">design and development</option>
                    <option value="food and drinks">food and drinks</option>
                    <option value="lifestyle">lifestyle</option>
                    <option value="personal">personal</option>
                    <option value="health and fitness">health and fitness</option>
                    <option value="business">business</option>
                    <option value="shopping">shopping</option>
                    <option value="animations">animations</option>
                </select>
                <p>Post image</p>
                <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
                <?php if ($fetch_posts['image'] != '') { ?>
                    <img src="../uploaded_img/<?= $fetch_posts['image']; ?>" class="image" alt="">
                    <input type="submit" value="delete image" class="inline-delete-btn" name="delete_image">
                <?php } ?>
                <div class="flex-btn">
                    <input type="submit" value="save post" name="save" class="btn">
                    <a href="view_posts.php" class="option-btn">Go back</a>
                    <input type="submit" value="delete post" class="delete-btn" name="delete_post">
                </div>
            </form>
            <?php
        }
    } else {
        echo '<p class="empty">No posts found!</p>';
        ?>
        <div class="flex-btn">
            <a href="view_posts.php" class="option-btn">View posts</a>
            <a href="add_posts.php" class="option-btn">Add posts</a>
        </div>
        <?php
    }
    ?>
</section>
<!-- Custom JS file link -->
<script src="../js/admin_script.js"></script>
</body>
</html>

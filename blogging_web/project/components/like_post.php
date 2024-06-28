<?php

global $conn, $user_id;

if (isset($_POST['like_post'])) {

    if ($user_id != '') {

        $post_id = filter_var($_POST['post_id'], FILTER_SANITIZE_SPECIAL_CHARS);
        $admin_id = filter_var($_POST['admin_id'], FILTER_SANITIZE_SPECIAL_CHARS);

        $select_post_like = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ? AND user_id = ?");
        $select_post_like->bind_param("ii", $post_id, $user_id);
        $select_post_like->execute();
        $result_post_like = $select_post_like->get_result();

        if ($result_post_like->num_rows > 0) {
            $remove_like = $conn->prepare("DELETE FROM `likes` WHERE post_id = ? AND user_id = ?");
            $remove_like->bind_param("ii", $post_id, $user_id);
            $remove_like->execute();
            $message[] = 'Removed from likes';
        } else {
            $add_like = $conn->prepare("INSERT INTO `likes` (user_id, post_id, admin_id) VALUES (?, ?, ?)");
            $add_like->bind_param("iii", $user_id, $post_id, $admin_id);
            $add_like->execute();
            $message[] = 'Added to likes';
        }

    } else {
        $message[] = 'Please login first!';
    }
}
?>

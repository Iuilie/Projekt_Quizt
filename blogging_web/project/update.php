<?php

global $conn, $fetch_profile;
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:/project/home.php');
    exit;
}

if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

    if (!empty($name)) {
        $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
        $update_name->bind_param("si", $name, $user_id);
        $update_name->execute();
    }

    if (!empty($email)) {
        $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_email->bind_param("s", $email);
        $select_email->execute();
        $result_email = $select_email->get_result();
        if ($result_email->num_rows > 0) {
            $message[] = 'Email already taken!';
        } else {
            $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
            $update_email->bind_param("si", $email, $user_id);
            $update_email->execute();
        }
    }

    $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
    $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
    $select_prev_pass->bind_param("i", $user_id);
    $select_prev_pass->execute();
    $result_prev_pass = $select_prev_pass->get_result();
    $fetch_prev_pass = $result_prev_pass->fetch_assoc();
    $prev_pass = $fetch_prev_pass['password'];
    $old_pass = sha1($_POST['old_pass']);
    $new_pass = sha1($_POST['new_pass']);
    $confirm_pass = sha1($_POST['confirm_pass']);

    if ($old_pass != $empty_pass) {
        if ($old_pass != $prev_pass) {
            $message[] = 'Old password not matched!';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = 'Confirm password not matched!';
        } else {
            if ($new_pass != $empty_pass) {
                $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                $update_pass->bind_param("si", $confirm_pass, $user_id);
                $update_pass->execute();
                $message[] = 'Password updated successfully!';
            } else {
                $message[] = 'Please enter a new password!';
            }
        }
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

    <form action="" method="post">
        <h3>Update Profile</h3>
        <input type="text" name="name" placeholder="<?= htmlspecialchars($fetch_profile['name'], ENT_QUOTES, 'UTF-8'); ?>" class="box" maxlength="50">
        <input type="email" name="email" placeholder="<?= htmlspecialchars($fetch_profile['email'], ENT_QUOTES, 'UTF-8'); ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="old_pass" placeholder="Enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="new_pass" placeholder="Enter your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="confirm_pass" placeholder="Confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" value="Update Now" name="submit" class="btn">
    </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>

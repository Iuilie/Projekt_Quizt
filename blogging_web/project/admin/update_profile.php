<?php

global $conn, $fetch_profile;
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

    if (!empty($name)) {
        $select_name = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
        $select_name->bind_param("s", $name);
        $select_name->execute();
        $select_name->store_result();

        if ($select_name->num_rows > 0) {
            $message[] = 'Username already taken!';
        } else {
            $update_name = $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
            $update_name->bind_param("si", $name, $admin_id);
            $update_name->execute();
            $message[] = 'Username updated successfully!';
        }
    }

    $empty_pass = sha1('');
    $select_old_pass = $conn->prepare("SELECT password FROM `admin` WHERE id = ?");
    $select_old_pass->bind_param("i", $admin_id);
    $select_old_pass->execute();
    $select_old_pass->bind_result($prev_pass);
    $select_old_pass->fetch();
    $select_old_pass->close();

    $old_pass = sha1($_POST['old_pass']);
    $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
    $new_pass = sha1($_POST['new_pass']);
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
    $confirm_pass = sha1($_POST['confirm_pass']);
    $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

    if ($old_pass != $empty_pass) {
        if ($old_pass != $prev_pass) {
            $message[] = 'Old password not matched!';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = 'Confirm password not matched!';
        } else {
            if ($new_pass != $empty_pass) {
                $update_pass = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
                $update_pass->bind_param("si", $confirm_pass, $admin_id);
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
    <title>Profile Update</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- Admin profile update section starts -->

<section class="form-container">

    <form action="" method="POST">
        <h3>Update Profile</h3>
        <input type="text" name="name" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?= $fetch_profile['name']; ?>">
        <input type="password" name="old_pass" maxlength="20" placeholder="Enter your old password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="new_pass" maxlength="20" placeholder="Enter your new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="confirm_pass" maxlength="20" placeholder="Confirm your new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" value="Update Now" name="submit" class="btn">
    </form>

</section>

<!-- Admin profile update section ends -->

<!-- Custom JS file link -->
<script src="../js/admin_script.js"></script>

</body>
</html>

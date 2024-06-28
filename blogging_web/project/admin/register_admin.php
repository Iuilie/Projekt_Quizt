<?php

global $conn;
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
    $select_admin->bind_param("s", $name);
    $select_admin->execute();
    $select_admin->store_result();

    if ($select_admin->num_rows > 0) {
        $message[] = 'Username already exists!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'Confirm password not matched!';
        } else {
            $insert_admin = $conn->prepare("INSERT INTO `admin` (name, password) VALUES (?, ?)");
            $insert_admin->bind_param("ss", $name, $pass);
            $insert_admin->execute();
            $message[] = 'New admin registered!';
            $insert_admin->close();
        }
    }
    $select_admin->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- Register admin section starts -->
<section class="form-container">

    <form action="" method="POST">
        <h3>Register New</h3>
        <input type="text" name="name" maxlength="20" required placeholder="Enter your username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="pass" maxlength="20" required placeholder="Enter your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="cpass" maxlength="20" required placeholder="Confirm your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" value="Register Now" name="submit" class="btn">
    </form>

</section>
<!-- Register admin section ends -->

<!-- Custom JS file link -->
<script src="../js/admin_script.js"></script>

</body>
</html>

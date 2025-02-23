<?php

global $conn;
include '../components/connect.php';

session_start();

if(isset($_POST['submit'])){

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_SPECIAL_CHARS);

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
    $stmt->bind_param('ss', $name, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $fetch_admin_id = $result->fetch_assoc();
        $_SESSION['admin_id'] = $fetch_admin_id['id'];
        header('location:dashboard.php');
    } else {
        $message[] = 'incorrect username or password!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0 !important;">

<?php
if(isset($message)){
    foreach($message as $msg){
        echo '
      <div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}
?>

<!-- admin login form section starts  -->

<section class="form-container">

    <form action="" method="POST">
        <h3>login now</h3>
        <p>default username = <span>admin</span> & password = <span>111</span></p>
        <input type="text" name="name" maxlength="20" required placeholder="enter your username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="pass" maxlength="20" required placeholder="enter your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" value="login now" name="submit" class="btn">
    </form>

</section>

<!-- admin login form section ends -->











</body>
</html>

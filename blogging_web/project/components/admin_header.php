<?php
global $conn, $admin_id;

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

<header class="header">

    <a href="../admin/dashboard.php" class="logo">Admin<span>Panel</span></a>

    <div class="profile">
        <?php
        // Prepare and execute the SQL statement to fetch the admin profile using mysqli
        $stmt = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch_profile = $result->fetch_assoc();

        // Check if a profile was found and display the name
        if($fetch_profile) {
            echo '<p>' . htmlspecialchars($fetch_profile['name']) . '</p>';
        }
        $stmt->close();
        ?>
        <a href="../admin/update_profile.php" class="btn">update profile</a>
    </div>

    <nav class="navbar">
        <a href="../admin/dashboard.php"><i class="fas fa-home"></i> <span>home</span></a>
        <a href="../admin/add_posts.php"><i class="fas fa-pen"></i> <span>add posts</span></a>
        <a href="../admin/view_posts.php"><i class="fas fa-eye"></i> <span>view posts</span></a>
        <a href="../admin/admin_accounts.php"><i class="fas fa-user"></i> <span>accounts</span></a>
        <a href="../components/admin_logout.php" style="color:var(--red);" onclick="return confirm('logout from the website?');"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
    </nav>

    <div class="flex-btn">
        <a href="../admin/admin_login.php" class="option-btn">login</a>
        <a href="../admin/register_admin.php" class="option-btn">register</a>
    </div>

</header>

<div id="menu-btn" class="fas fa-bars"></div>

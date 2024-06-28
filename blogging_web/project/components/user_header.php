<?php
global $conn, $user_id;
$message = [];

if (!empty($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<header class="header">
    <section class="flex">
        <a href="/project/home.php" class="logo">Blogo.</a>

        <form action="/project/search.php" method="POST" class="search-form">
            <label>
                <input type="text" name="search_box" class="box" maxlength="100" placeholder="search for blogs" required>
            </label>
            <button type="submit" class="fas fa-search" name="search_btn"></button>
        </form>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>

        <nav class="navbar">
            <a href="/project/home.php"><i class="fas fa-angle-right"></i> home</a>
            <a href="/project/posts.php"><i class="fas fa-angle-right"></i> posts</a>
            <a href="/project/all_category.php"><i class="fas fa-angle-right"></i> category</a>
            <a href="/project/authors.php"><i class="fas fa-angle-right"></i> authors</a>
            <a href="/project/login.php"><i class="fas fa-angle-right"></i> login</a>
            <a href="/project/register.php"><i class="fas fa-angle-right"></i> register</a>
        </nav>

        <div class="profile">
            <?php
            // Prepare and execute the SQL statement to fetch the user profile using mysqli
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->bind_param("i", $user_id);
            $select_profile->execute();
            $result_profile = $select_profile->get_result();

            // Check if a profile was found and display the name
            if ($result_profile->num_rows > 0) {
                $fetch_profile = $result_profile->fetch_assoc();
                ?>
                <p class="name"><?= htmlspecialchars($fetch_profile['name']); ?></p>
                <a href="/project/update.php" class="btn">update profile</a>
                <div class="flex-btn">
                    <a href="/project/login.php" class="option-btn">login</a>
                    <a href="/project/register.php" class="option-btn">register</a>
                </div>
                <a href="/project/components/user_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
                <?php
            } else {
                ?>
                <p class="name">please login first!</p>
                <a href="/project/login.php" class="option-btn">login</a>
                <?php
            }

            $select_profile->close();
            ?>
        </div>
    </section>
</header>

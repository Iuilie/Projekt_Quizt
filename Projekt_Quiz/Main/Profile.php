<?php

global $conn;
session_start();


include_once "db.php";


if (isset($_SESSION['id_users'])) {
    $id_users = $_SESSION['id_users'];

    // Prepare the query to get user details
    $query = $conn->prepare("SELECT * FROM users WHERE id_users = ?");
    $query->bind_param("i", $id_users);
    $query->execute();
    $result = $query->get_result();

    // Check if result is greater than 0
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $first_name = $row['first_name'] ?? '';
            $last_name = $row['last_name'] ?? '';
            $user_name = $row['user_name'] ?? '';
            $email = $row['email'] ?? '';
            $password = $row['password'] ?? '';
            $avatar = $row['avatar'] ?? '';
            $created_at = $row['created_at'] ?? '';
        }
    }
    $query->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profil</title>
    <link rel="stylesheet" href="../Css/Profile.css">
    <link rel="stylesheet" href="../Css/Index.css">
    <link rel="stylesheet" href="../Css/Main.css">
</head>
<body>
<header>
    <h1><a href="Index.php">Crazyquizy.pl</a></h1>
</header>
<main>
    <h2>Profil</h2>
    <?php if (isset($id_users)): ?>
        <form method="post" action="Profile.php" enctype="multipart/form-data">
            <section>
                <h3>Imię</h3>
                <label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>">
                </label>
            </section>
            <section>
                <h3>Nazwisko</h3>
                <label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>">
                </label>
            </section>
            <section>
                <h3>Nazwa użytkownika</h3>
                <label>
                    <input type="text" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>">
                </label>
            </section>
            <section>
                <h3>Email</h3>
                <label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </label>
            </section>
            <section>
                <h3>Hasło</h3>
                <label>
                    <input type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
                </label>
            </section>
            <section>
                <h3>Avatar</h3>
                <input type="file" name="avatar">
                <img src="../Images/User_images/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar">
            </section>
            <section>
                <input type="submit" value="Zaktualizuj profil">
            </section>
        </form>
    <?php else: ?>
        <p>Nie jesteś zalogowany.</p>
    <?php endif; ?>
</main>
<footer>
    <?php
     include 'Footer.php';
    ?>
</footer>
</body>
</html>

<?php
// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($id_users)) {
    $first_name = $_POST['first_name'];
    $last_name= $_POST['last_name'];
    $user_name= $_POST['user_name'];
    $email= $_POST['email'];
    $password= $_POST['password'];
    $avatar = $_FILES['avatar']['name'];

    $target_dir = "../Images/User_images/";
    $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
    move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file);


    // Prepare the query to update user details
    $query1 = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, user_name = ?, email = ?, password = ?, avatar = ? WHERE id_users = ?");
    $query1->bind_param("ssssssi", $first_name, $last_name, $user_name, $email, $password, $avatar, $id_users);
    $query1->execute();
    $query1->close();
}
?>

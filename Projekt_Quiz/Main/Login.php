<?php
global $conn;
session_start();
include_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["user_name"]) && isset($_POST["password"])) {
        $user_name_given = $_POST["user_name"];
        $password_given = $_POST["password"];

        $query = "SELECT * FROM users WHERE user_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $user_name_given);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_users = $row['id_users'];
            $password_hash = $row['password'];
            $role = $row['role'];

            if (password_verify($password_given, $password_hash)) {
                $_SESSION["id_users"] = $id_users;
                $_SESSION["role"] = $role;
                header("Location: Index.php");
                exit;
            } else {
                echo "<script>alert('Błędne hasło!');</script>";
            }
        } else {
            echo "<script>alert('Nie znaleziono użytkownika!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zaloguj się</title>
    <link rel="stylesheet" href="../Css/Login.css">
</head>
<body>
<header>
    <h1><a href="Index.php">Crazyquizy.pl</a></h1>
    <link rel="stylesheet" href="../Css/Index.css">
</header>
<main>
    <form action="Login.php" method="POST">
        <div>
            <label for="user_name">Nazwa użytkownika:</label>
            <input type="text" name="user_name" id="user_name" required>
        </div>
        <div>
            <label for="password">Hasło:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <input type="submit" value="Zaloguj się">
    </form>
    <span>
        <h3>Nie masz konta?</h3>
        <p><a href="Register.php">Zarejestruj się</a></p>
    </span>
</main>
<footer>
    <?php include 'Footer.php'; ?>
</footer>
</body>
</html>

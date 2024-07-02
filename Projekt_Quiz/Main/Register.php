<?php
global $conn;
session_start();
include_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password1 = $_POST['password1'];

    if (empty($first_name) || empty($last_name) || empty($user_name) || empty($email) || empty($password) || empty($password1)) {
        $errors[] = 'Wszystkie pola są wymagane.';
    }

    if ($password !== $password1) {
        $errors[] = 'Hasła nie są takie same!';
    }

    if (empty($errors)) {
        // Use placeholders in the SQL statement
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE user_name = ? OR email = ?");
        $stmt->bind_param("ss", $user_name, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Nazwa użytkownika lub email są już wykorzystane.';
        } else {
            // Use placeholders in the SQL statement
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, user_name, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $role = 1; // Assuming default role is 1
            $stmt->bind_param("sssssi", $first_name, $last_name, $user_name, $email, $password_hash, $role);

            if ($stmt->execute()) {
                header("Location: Login.php");
                exit;
            } else {
                $errors[] = 'Błąd podczas rejestracji. Spróbuj ponownie.';
            }
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
    <title>Zarejestruj się</title>
    <link rel="stylesheet" href="../Css/Register.css">
</head>
<body>
<header>
    <h1><a href="Index.php">Crazyquizy.pl</a></h1>
    <link rel="stylesheet" href="../Css/Index.css">
</header>
<main>
    <form action="Register.php" method="POST">
        <div>
            <label for="first_name">Imię:</label>
            <input type="text" name="first_name" id="first_name" required>
        </div>
        <div>
            <label for="last_name">Nazwisko:</label>
            <input type="text" name="last_name" id="last_name" required>
        </div>
        <div>
            <label for="user_name">Nazwa użytkownika:</label>
            <input type="text" name="user_name" id="user_name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="password">Hasło:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <label for="password1">Potwiedź hasło:</label>
            <input type="password" name="password1" id="password1" required>
        </div>
        <input type="submit" value="Zarejestruj się">
    </form>
    <span>
        <h3>Masz już konto?</h3>
        <p><a href="Login.php">Zaloguj się</a></p>
    </span>

    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
    ?>
</main>
<footer>
    <?php include 'Footer.php'; ?>
</footer>
</body>
</html>

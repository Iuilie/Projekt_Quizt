<?php
// Start global connection and session
global $conn;
session_start();

// Check if user role is set and greater than 0
if (!(isset($_SESSION["role"]) && $_SESSION["role"] > 0)) {
    header("Location: Login.php");
    exit;
}

// Get user id from session
$id_users = $_SESSION["id_users"];

// Include database connection
include_once "db.php";

// Initialize general points to 0
$generalPoints= 0;

// Query to get sum of scores
$query = "SELECT SUM(score) FROM user_quiz_attempts WHERE user_id = $id_users";
$result = $conn->query($query);

// Check if result is greater than 0
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $generalPoints = $row['SUM(score)'];
    }
}

// Initialize general games to 0
$generalGames = 0;

// Query to get count of user quiz attempts
$query = "SELECT COUNT(*) FROM user_quiz_attempts WHERE user_id = $id_users";
$result = $conn->query($query);

// Check if result is greater than 0
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $generalGames = $row['COUNT(*)'];
    }

    // Initialize max points to 0
    $maxPoints = 0;

    // Query to get count of user quiz attempts
    $query = "SELECT COUNT(*) FROM user_quiz_attempts WHERE user_id =$id_users";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $maxPoints = $row['COUNT(*)'];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Statystyki</title>
    <link rel="stylesheet" href="../Css/Stats.css">
    <link rel="stylesheet" href="../Css/Main.css">
</head>
<body>
<header>
    <h1><a href="Index.php">Crazyquizy.pl</a></h1>
</header>
<main>
    <article>
        <h2>Ogólnie</h2>
        <section>
            <h3>Ilość rozgrywek</h3>
            <p><?php echo $generalGames?></p>
        </section>
        <section>
            <h3>Łączna ilość punktów</h3>
            <p><?php echo $generalPoints?></p>
        </section>
    </article>
</main>
<footer>
    <?php
    include 'Footer.php'
    ?>
</footer>
</body>
</html>

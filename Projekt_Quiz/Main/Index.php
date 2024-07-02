<?php
global $conn;
require_once "db.php";
session_start();

// Redirect if user role is not set or less than 1
if (!isset($_SESSION["role"]) || $_SESSION["role"] <= 0) {
    header("Location: Login.php");
    die();
}

function generatePseudoRandomNumberByDate($date) {
    list($year, $month, $day) = explode('-', date('Y-m-d', strtotime($date)));
    srand((int)($year . $month . $day));
    $randomNumber = rand();
    srand();
    return $randomNumber;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = "SELECT COUNT(*) FROM quizzes";
    $result = $conn->query($query);
    $max = $result->fetch_assoc()['COUNT(*)'];

    if (isset($_POST['quizList'])) {
        header("Location: Quizlist.php");
    } elseif (isset($_POST['randomQuiz'])) {
        $quiz_id = rand(1, $max);
        setcookie('selected_quiz', $quiz_id, time() + 360, '/');
        header("Location: Quizform.php");
    } elseif (isset($_POST['dayQuiz'])) {
        $today = date('Y-m-d');
        $pseudoRandomNumber = generatePseudoRandomNumberByDate($today);
        if ($max > 0) {
            $quiz_id = rand(1, $max);
            setcookie('selected_quiz', $pseudoRandomNumber % $max + 1, time() + 360, '/');
        } else {
            echo "<p>Brak quizów</p>";
        }
    } elseif (isset($_POST['Arcade'])) {
        $quizzes = range(0, $max - 1);
        shuffle($quizzes);
        $_SESSION['Arcade'] = $quizzes;
        $_SESSION['Arcade_progress'] = 0;
        header("Location: Quizform.php");
        exit();
    }
}

if (isset($_POST['logOut'])) {
    session_unset();
    session_destroy();
    header("Location: Index.php");
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Strona Główna</title>
    <link rel="stylesheet" href="../Css/Index.css">
    <link rel="stylesheet" href="../Css/Main.css">
</head>
<body>
<header>
    <h1><a href="Index.php">Crazyquizy.pl</a></h1>
    <nav>
        <ul>
            <li><a href="Quizlist.php">Quizy</a></li>
            <li><a href="Profile.php">Profil</a></li>
            <li><a href="Stats.php">Statystyki</a></li>
            <?php
            if (isset($_SESSION["role"]) && $_SESSION["role"] > 1) {
                echo "<li><a href=\"admin.php\">Panel Admina</a></li>";
            }
            ?>
            <li>
                <form action="Index.php" method="POST">
                    <button type="submit" id="logOutButton" name="logOut">Wyloguj</button>
                </form>
            </li>
        </ul>
    </nav>
</header>
<main>
    <section>
        <h2>Quizy z listy</h2>
        <form method="POST">
            <input type="submit" name="quizList" value="Kliknij aby przejść">
        </form>
    </section>
    <section>
        <h2>Quiz dnia</h2>
        <form method="POST">
            <input type="submit" name="dayQuiz" value="Kliknij aby przejść">
        </form>
    </section>
    <section>
        <h2>Losowy quiz</h2>
        <form method="POST">
            <input type="submit" name="randomQuiz" value="Kliknij aby wylosować">
        </form>
    </section>
    <section>
        <h2>Tryb arcde</h2>
        <form method="POST">
            <input type="submit" name="Arcade" value="Kliknij aby rozpocząć">
        </form>
    </section>
</main>
<footer>
    <?php include 'Footer.php'; ?>
</footer>
</body>
</html>

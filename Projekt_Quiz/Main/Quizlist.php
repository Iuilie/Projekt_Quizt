<?php
global $conn;
session_start();
if(!(isset($_SESSION["role"]) && $_SESSION["role"] > 0)){
    header("Location: Login.php");
}
include_once "db.php";

$query = "SELECT * FROM categories ";

$result = $conn->query($query);
$categories = [];
$i = 0;
while ($row = $result->fetch_assoc()) {
    $categories[$i] = $row['category_name'];
    $i++;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lista quizów</title>
    <link rel="stylesheet" href="../Css/Quizlist.css">
    <link rel="stylesheet" href="../Css/Index.css">
    <link rel="stylesheet" href="../Css/Main.css">
</head>
<body>
<header>
    <h1><a href="Index.php">Crazyquizy.pl</a></h1>
    <nav>
        <ul>
            <li><a href="Create_quiz.php">Stwórz Quiz</a></li> <!-- Dodany przycisk -->
            <li><a href="Profile.php">Profil</a></li>
            <li><a href="Stats.php">Statystyki</a></li>
            <?php
            // Check if user role is set and greater than 1
            if (isset($_SESSION["role"]) && ($_SESSION["role"] > 1)) {
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
    <?php
    for ($j = 0; $j < count($categories); $j++) {
        echo "<article>";
        echo "<h2> $categories[$j] </h2>";
        $query1 = "SELECT * FROM quizzes WHERE category_id = (" . ($j + 1) . ") ";
        $result1 = $conn->query($query1);
        echo "<section>";
        if ($result1->num_rows > 0) {
            while ($row = $result1->fetch_assoc()) {
                $quiz_id = $row['quiz_id'];
                $quiz_name = $row['quiz_name'];
                $quiz_type = $row['quiz_type'];
                $description = $row['description'];

                echo "<div class='one'>";
                echo "<h3> $quiz_name</h3>";
                echo "<div class='two'>";
                switch ($quiz_type) {
                    case 1:
                        echo "<p>Test jednokrotnego wyboru</p>";
                        break;
                    case 2:
                        echo "<p>Test typu: odgadnij coś z obrazka</p>";
                        break;
                }
                echo "<p>$description</p>";

                echo "<form action='#' method='post'>";
                echo "<input type='hidden' name='quiz_id' value='$quiz_id'>";
                echo "<button type='submit' name='selectQuiz'>Wybierz ten quiz</button>";
                echo "</form>";

                echo "</div>";
                echo "</div>";
            }
        }
        echo "</section>";
        echo "</article>";
    }
    ?>

</main>
<footer>
    <?php
    include 'Footer.php'
    ?>
</footer>
</body>
</html>

<?php

if (isset($_POST['quiz_id']) && is_numeric($_POST['quiz_id'])) {
    $quiz_id = $_POST['quiz_id'];

    setcookie('selected_quiz', $quiz_id, time() + 3600, '/'); // 1 hour cookie

    header('Location: Quizform.php');
    exit;
}
?>

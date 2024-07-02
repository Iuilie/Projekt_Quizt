<?php
include_once "db.php";
global $conn;
session_start();

if (!(isset($_SESSION["role"]) && $_SESSION["role"] > 0)) {
    header("Location: Login.php");
    exit();
}

$id_users = $_SESSION['id_users'];
$quiz_id = isset($_COOKIE['selected_quiz']) ? $_COOKIE['selected_quiz'] : null;

if ($quiz_id === null) {
    die("Quiz ID not set. Please select a quiz.");
}

// Debugging: Check if the user exists in the users table
$user_check_query = $conn->prepare("SELECT * FROM users WHERE id_users = ?");
$user_check_query->bind_param('i', $id_users);
$user_check_query->execute();
$user_check_result = $user_check_query->get_result();

if ($user_check_result->num_rows === 0) {
    die("User not found. Please log in again.");
}

// Debugging: Print user ID
echo "Debug: User ID is " . $id_users . "<br>";

$points = 0;

// Initialize variables
$quiz_name = '';
$quiz_type = 0;

// Fetch quiz details
$query = $conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
$query->bind_param('i', $quiz_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $quiz_type = $row['quiz_type'];
        $quiz_name = $row['quiz_name'];
    }
} else {
    die("Quiz not found.");
}

$questions = [];
$questions_text = [];
$questions_images = [];
$query1 = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$query1->bind_param('i', $quiz_id);
$query1->execute();
$result1 = $query1->get_result();

$i = 0;
if ($result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        $questions[$i] = $row['question_id'];
        $questions_text[$i] = $row['question_text'];
        $questions_images[$i] = $row['question_image'];
        $i++;
    }
} else {
    die("No questions found for this quiz.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz</title>
    <link rel="stylesheet" href="../Css/Form.css">
</head>
<body>
<header>
    <h1><a href="Index.php">Crazyquizy.pl</a></h1>
    <link rel="stylesheet" href="../Css/Index.css">
    <link rel="stylesheet" href="../Css/Main.css">
</header>
<main>
    <?php
    echo "<h2>QUIZ: $quiz_name</h2>";

    switch ($quiz_type) {
        case 1:
            echo "<form method='POST' action='Quizform.php'>";
            $correct_answers = [];

            echo "<div class='one'>";
            for ($k = 0; $k < count($questions); $k++) {
                $query3 = $conn->prepare("SELECT * FROM quiz_answers WHERE question_id = ? AND is_correct = 1");
                $query3->bind_param('i', $questions[$k]);
                $query3->execute();
                $result3 = $query3->get_result();
                while ($row = $result3->fetch_assoc()) {
                    $correct_answers[$k + 1] = $row['answer_id'];
                }
            }

            for ($j = 0; $j < count($questions); $j++) {
                echo "<div class='two'>";
                if ($questions_images[$j]) {
                    echo "<img src='".$questions_images[$j]."' alt='Question Image'>";
                }
                echo "<h3>$questions_text[$j]</h3>";
                $query2 = $conn->prepare("SELECT * FROM quiz_answers WHERE question_id = ?");
                $query2->bind_param('i', $questions[$j]);
                $query2->execute();
                $result2 = $query2->get_result();
                if ($result2->num_rows > 0) {
                    while ($row = $result2->fetch_assoc()) {
                        $answer_id = $row['answer_id'];
                        $answer_text = $row['answer_text'];
                        echo "<label for='answer_$answer_id'>$answer_text</label>";
                        echo "<input type='radio' id='answer_$answer_id' name='question_$questions[$j]' value='$answer_id'> <br>";
                    }
                }
                echo "</div>";
            }
            echo "</div>";
            echo "<input type='submit' value='Submit'>";
            echo "</form>";

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user_answers = [];
                $a = 1;
                foreach ($questions as $question_id) {
                    if (isset($_POST["question_$question_id"])) {
                        $user_answers[$a] = $_POST["question_$question_id"];
                    } else {
                        $user_answers[$question_id] = null;
                    }
                    $a++;
                }

                for ($l = 1; $l < count($questions) + 1; $l++) {
                    $a = $l - 1;
                    $query4 = $conn->prepare("INSERT INTO user_quiz_answers (user_id, question_id, selected_answer_id) VALUES (?, ?, ?)");
                    $query4->bind_param('iii', $id_users, $questions[$a], $user_answers[$l]);
                    if (!$query4->execute()) {
                        // Debugging: Print error if query fails
                        echo "Error inserting answer: " . $query4->error . "<br>";
                    }

                    if ($user_answers[$l] == $correct_answers[$l]) {
                        $points++;
                    }
                }

                $query4 = $conn->prepare("INSERT INTO user_quiz_attempts (user_id, quiz_id, score) VALUES (?, ?, ?)");
                $query4->bind_param('iii', $id_users, $quiz_id, $points);
                if (!$query4->execute()) {
                    echo "Error inserting attempt: " . $query4->error . "<br>";
                }

                echo "<script>
                alert('Odpowiedziałeś poprawnie $points razy');
                window.location.href = 'Quizlist.php';
                </script>";
                exit();
            }
            break;
    }
    ?>
</main>
<footer>
    <?php include 'Footer.php'; ?>
</footer>
</body>
</html>

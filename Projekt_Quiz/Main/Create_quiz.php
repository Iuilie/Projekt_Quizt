<?php
global $conn;
include_once "db.php";
session_start();

if (!(isset($_SESSION["role"]) && $_SESSION["role"] > 0)) {
    header("Location: Login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_type = $_POST['quiz_type'];
    $quiz_name = $_POST['quiz_name'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];

    $query = "INSERT INTO quizzes (quiz_type, quiz_name, category_id, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isis', $quiz_type, $quiz_name, $category_id, $description);
    $stmt->execute();
    $quiz_id = $stmt->insert_id;

    $questions = $_POST['questions'];
    foreach ($questions as $index => $question) {
        $question_text = $question['text'];
        $question_image = null;

        if (isset($_FILES['questions']['name'][$index]['image']) && $_FILES['questions']['name'][$index]['image'] !== '') {
            $target_dir = "../Images/Quizimages/";
            $target_file = $target_dir . basename($_FILES['questions']['name'][$index]['image']);
            if (move_uploaded_file($_FILES['questions']['tmp_name'][$index]['image'], $target_file)) {
                $question_image = $target_file;
            }
        }

        $query = "INSERT INTO quiz_questions (quiz_id, question_text, question_image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iss', $quiz_id, $question_text, $question_image);
        $stmt->execute();
        $question_id = $stmt->insert_id;

        foreach ($question['answers'] as $answer) {
            $answer_text = $answer['text'];
            $is_correct = isset($answer['correct']) ? 1 : 0;
            $query = "INSERT INTO quiz_answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)";
            $stmt->prepare($query);
            $stmt->bind_param('isi', $question_id, $answer_text, $is_correct);
            $stmt->execute();
        }
    }

    echo "<script>
        alert('Quiz został pomyślnie utworzony.');
        window.location.href = 'Quizlist.php';
    </script>";
    exit();
}

$query = "SELECT * FROM categories";
$result = $conn->query($query);
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Quiz</title>
    <link rel="stylesheet" href="../Css/Form.css">
    <link rel="stylesheet" href="../Css/Main.css">
</head>
<body>
<header>
    <h1><a href="Index.php">Crazyquizy.pl</a></h1>
    <nav>
        <ul>
            <li><a href="Create_quiz.php">Stwórz Quiz</a></li>
            <li><a href="Quizlist.php">Quizy</a></li>
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
    <h2>Create a New Quiz</h2>
    <form method="POST" action="Create_quiz.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="quiz_name">Nazwa Quizu:</label>
            <input type="text" id="quiz_name" name="quiz_name" required>
        </div>
        <div class="form-group">
            <label for="category_id">Kategoria:</label>
            <select id="category_id" name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quiz_type">Typ Quizu:</label>
            <select id="quiz_type" name="quiz_type" required>
                <option value="1">Test jednokrotnego wyboru</option>
                <option value="2">Odgadnij z obrazka</option>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Opis Quizu:</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <div id="questions_container" class="form-group">
            <h3>Questions:</h3>
            <button type="button" class="btn-add" onclick="addQuestion()">Add Question</button>
        </div>
        <input type="submit" value="Create Quiz" class="btn-submit">
    </form>
</main>
<footer>
    <?php include 'Footer.php'; ?>
</footer>
<script>
    function addQuestion() {
        const container = document.getElementById('questions_container');
        const questionIndex = document.querySelectorAll('.question').length;

        const questionDiv = document.createElement('div');
        questionDiv.className = 'question';

        questionDiv.innerHTML = `
        <div class="form-group">
            <label for="question_text_${questionIndex}">Question Text:</label>
            <input type="text" id="question_text_${questionIndex}" name="questions[${questionIndex}][text]" required>
        </div>
        <div class="form-group">
            <label for="question_image_${questionIndex}">Image (optional):</label>
            <input type="file" id="question_image_${questionIndex}" name="questions[${questionIndex}][image]">
        </div>
        <div class="answers form-group">
            <h4>Answers:</h4>
            <button type="button" class="btn-add" onclick="addAnswer(this)">Add Answer</button>
        </div>
    `;
        container.appendChild(questionDiv);
    }

    function addAnswer(button) {
        const answersDiv = button.parentElement;
        const questionIndex = answersDiv.parentElement.querySelector('input[type="text"]').id.split('_')[2];
        const answerIndex = answersDiv.querySelectorAll('.answer').length;

        const answerDiv = document.createElement('div');
        answerDiv.className = 'answer';

        answerDiv.innerHTML = `
        <div class="form-group">
            <label for="answer_text_${questionIndex}_${answerIndex}">Odpowiedź:</label>
            <input type="text" id="answer_text_${questionIndex}_${answerIndex}" name="questions[${questionIndex}][answers][${answerIndex}][text]" required>
            <label for="answer_correct_${questionIndex}_${answerIndex}">Poprawna:</label>
            <input type="checkbox" id="answer_correct_${questionIndex}_${answerIndex}" name="questions[${questionIndex}][answers][${answerIndex}][correct]">
        </div>
    `;
        answersDiv.appendChild(answerDiv);
    }
</script>
</body>
</html>

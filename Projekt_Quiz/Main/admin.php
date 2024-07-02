<?php
global $conn;
require_once "db.php";
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] <= 0) {
    header("Location: Login.php");
    die();
}

$action_one = $_POST["action_one"] ?? null;
$action_two = $_POST["action_two"] ?? null;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona Główna</title>
    <link rel="stylesheet" href="../Css/Admin.css">
    <link rel="stylesheet" href="../Css/Main.css">
</head>
<body>
<header>
    <h1><a href="Index.php">quizy.pl</a></h1>
</header>
<main>
    <section>
        <h2>Co chcesz zrobić?</h2>
        <form action="admin.php" method="post">
            <div>
                <label for="View"> Przeglądać</label>
                <input type="radio" id="View" value="View" name="action_one" <?= $action_one == "View" ? "checked" : "" ?>>
            </div>
            <div>
                <label for="Add"> Dodać</label>
                <input type="radio" id="Add" value="Add" name="action_one" <?= $action_one == "Add" ? "checked" : "" ?>>
            </div>
            <div>
                <label for="Modify"> Zmodyfikować</label>
                <input type="radio" id="Modify" value="Modify" name="action_one" <?= $action_one == "Modify" ? "checked" : "" ?>>
            </div>
            <div>
                <label for="Delete"> Usunąć</label>
                <input type="radio" id="Delete" value="Delete" name="action_one" <?= $action_one == "Delete" ? "checked" : "" ?>>
            </div>
            <div>
                <label for="Export"> Eksportować</label>
                <input type="radio" id="Export" value="Export" name="action_one" <?= $action_one == "Export" ? "checked" : "" ?>>
            </div>
            <input type="submit">
        </form>
    </section>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["action_one"])) {
        echo "<section>";
        echo "<h2>Jaką tabelę?</h2>";
        echo "<form action=\"admin.php\" method=\"post\">";
        $tables = ["users", "quizzes", "categories", "quiz_answers", "quiz_questions", "user_quiz_answers", "user_quiz_attempts"];
        foreach ($tables as $table) {
            echo "<div>";
            echo "<label>$table</label>";
            echo "<input type=\"radio\" id=\"$table\" value=\"$table\" name=\"action_two\" " . ($action_two == $table ? "checked" : "") . ">";
            echo "</div>";
        }
        echo "<input type=\"hidden\" name=\"action_one\" value=\"$action_one\">";
        echo "<input type=\"submit\">";
        echo "</form>";
        echo "</section>";
        if (isset($action_two)) {
echo "<table>";

                $query = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '' AND TABLE_NAME = '$action_two'";

                $result = $conn->query($query);
                $columns = [];
                while ($row = $result->fetch_assoc()) {
                    $columns[] = $row['COLUMN_NAME'];
                }
                if ($result->num_rows > 0) {
                    switch ($action_one) {
                        case "View":
                            echo "<tr>";
                            foreach ($columns as $column) {
                                echo "<th>$column</th>";
                            }
                            echo "</tr>";

                            $query = "SELECT * FROM $action_two";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                foreach ($columns as $column) {
                                    echo "<td>$row[$column]</td>";
                                }
                                echo "</tr>";
                            }
                            break;
                        case "Add":
                            echo "<section class='last'>";
                            echo "<h2>Dodaj nowy rekord</h2>";
                            echo "<form action='admin.php' method='post'>";
                            for ($i = 1; $i < count($columns); $i++) {
                                echo "<label>$columns[$i]:</label>";
                                echo "<input type='text' name='$columns[$i]'><br>";
                            }
                            echo "<input type='hidden' name='action_one' value='Add'>";
                            echo "<input type='hidden' name='action_two' value='$action_two'>";
                            echo "<input type='submit'>";
                            echo "</form>";
                            echo "</section>";

                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[$columns[1]])) {
                                $values = [];
                                for ($i = 1; $i < count($columns); $i++) {
                                    $values[] = "'".$conn->real_escape_string($_POST[$columns[$i]])."'";
                                }
                                $values_str = implode(", ", $values);
                                $query = "INSERT INTO $action_two (" . implode(", ", array_slice($columns, 1)) . ") VALUES ($values_str)";
                                if ($conn->query($query) === TRUE) {
                                    echo "Record added successfully";
                                } else {
                                    echo "Error adding record: " . $conn->error;
                                }
                            }
                            break;
                        case "Modify":
                            echo "<section class='last'>";
                            echo "<h2>Zmodyfikuj rekord</h2>";
                            echo "<form action='admin.php' method='post'>";
                            echo "<label>ID rekordu do zmodyfikowania:</label>";
                            echo "<input type='number' name='id'><br>";
                            for ($i = 1; $i < count($columns); $i++) {
                                echo "<label>$columns[$i]:</label>";
                                echo "<input type='text' name='$columns[$i]'><br>";
                            }
                            echo "<input type='hidden' name='action_one' value='Modify'>";
                            echo "<input type='hidden' name='action_two' value='$action_two'>";
                            echo "<input type='submit'>";
                            echo "</form>";
                            echo "</section>";
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                                $id = $_POST['id'];
                                $updates = [];
                                for ($i = 1; $i < count($columns); $i++) {
                                    if (isset($_POST[$columns[$i]])) {
                                        $updates[] = "$columns[$i] = '".$conn->real_escape_string($_POST[$columns[$i]])."'";
                                    }
                                }
                                $updates_str = implode(", ", $updates);
                                $query = "UPDATE $action_two SET $updates_str WHERE $columns[0] = $id";
                                if ($conn->query($query) === TRUE) {
                                    echo "Record updated successfully";
                                } else {
                                    echo "Error updating record: " . $conn->error;
                                }
                            }
                            break;
                        case "Delete":
                            echo "<section class='last'>";
                            echo "<h2>Numer id do usunięcia</h2>";
                            echo "<form action='admin.php' method='post'>";
                            echo "<input type='hidden' name='action_one' value='Delete'>";
                            echo "<input type='hidden' name='action_two' value='$action_two'>";
                            echo "<input type='number' name='number'>";
                            echo "<input type='submit'>";
                            echo "</form>";
                            echo "</section>";

                            if (isset($_POST["number"])) {
                                $number = $_POST["number"];
                                $query = "DELETE FROM $action_two WHERE $columns[0] = $number";
                                if ($conn->query($query) === TRUE) {
                                    echo "Rekord został pomyślnie usunięty";
                                } else {
                                    echo "Błąd usuwania rekordu: " . $conn->error;
                                }
                            }
                            break;
                        case "Export":
                            $query = "SELECT * FROM $action_two";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                $filename = $action_two . "_data_" . date('Ymd') . ".csv";
                                header('Content-Type: text/csv');
                                header('Content-Disposition: attachment;filename=' . $filename);

                                $fp = fopen('php://output', 'w');

                                fputcsv($fp, $columns);

                                while ($row = $result->fetch_assoc()) {
                                    fputcsv($fp, $row);
                                }

                                fclose($fp);
                                exit;
                            } else {
                                echo "Brak danych do eksportowania.";
                            }
                            break;
                    }
                }
                echo "</table>";
            }
    }
    $conn->close();

    ?>
</main>
<footer>
    <?php
    include 'Footer.php';
    ?>
</footer>
</body>
</html>


<?php
$servername = "localhost";
$username = "Julia";
$password = "827613";
$dbname = "Quizz";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>





<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$usernameToDelete = $_GET['username'];
$sql_delete = "DELETE FROM users WHERE username=?";
$stmt = $conn->prepare($sql_delete);
$stmt->bind_param("s", $usernameToDelete);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit();
} else {
    echo "<p>Error: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>

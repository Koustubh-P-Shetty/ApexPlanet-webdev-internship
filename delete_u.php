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

if (isset($_GET['username'])) {
    $usernameToDelete = $_GET['username'];

    // Check if the user exists
    $sql_check = "SELECT username FROM users WHERE username=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $usernameToDelete);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Prepare for deletion
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm'])) {
            $sql_delete = "DELETE FROM users WHERE username=?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("s", $usernameToDelete);

            if ($stmt_delete->execute()) {
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<p>Error: " . $stmt_delete->error . "</p>";
            }

            $stmt_delete->close();
        }
    } else {
        echo "<p>User not found.</p>";
    }

    $stmt_check->close();
} else {
    echo "<p>No user specified for deletion.</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        p {
            margin-bottom: 20px;
            color: #555;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        button, a {
            display: inline-block;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px 0;
            text-decoration: none;
            color: white;
            text-align: center;
        }
        .confirm-btn {
            background-color: #dc3545;
        }
        .confirm-btn:hover {
            background-color: #c82333;
        }
        .cancel-btn {
            background-color: #007bff;
        }
        .cancel-btn:hover {
            background-color: #0056b3;
        }
        .cancel-btn-container {
            display: flex;
            justify-content: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete the user "<?php echo htmlspecialchars($usernameToDelete); ?>"?</p>
        <form action="delete_u.php?username=<?php echo urlencode($usernameToDelete); ?>" method="post">
            <button type="submit" name="confirm" class="confirm-btn">Confirm</button>
            <div class="cancel-btn-container">
                <a href="dashboard.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

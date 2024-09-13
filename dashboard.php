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

$loggedInUsername = $_SESSION['username'];

// Validate user information
$sql_user = "SELECT username, email, date_of_birth, gender FROM users WHERE username=?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $loggedInUsername);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
    echo "<p style='color: red; text-align: center;'>Error fetching user information. Please try again.</p>";
    exit();
}

$stmt_user->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
            background-color: #eef2f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
            margin-top: 50px;
            transition: box-shadow 0.3s ease;
        }
        .container:hover {
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }
        header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #007bff;
        }
        h1 {
            margin: 0;
            font-size: 28px;
            color: #333;
        }
        .profile-info {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .profile-info h2 {
            margin: 0;
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }
        .profile-info p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard</h1>
        </header>
        <div class="profile-info">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Date of Birth: <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
            <p>Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
        </div>
        <a href="user_manager.php" class="btn">Manage Users</a> <!-- Button to user management page -->
        <a href="logout.php" class="btn">Logout</a> <!-- Logout button -->
    </div>
</body>
</html>

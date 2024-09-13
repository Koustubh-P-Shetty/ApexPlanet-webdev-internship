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
$sql_user = "SELECT username, email, date_of_birth, gender, profile_picture FROM users WHERE username=?";
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

// Set the profile picture path
$profilePicture = htmlspecialchars($user['profile_picture']);
$profilePicturePath = $profilePicture ? $profilePicture : 'dup.png';

// Fetch a random thought
$thoughtApiUrl = 'https://api.forismatic.com/api/1.0/?method=getQuote&format=json&lang=en';
$thoughtResponse = file_get_contents($thoughtApiUrl);

if ($thoughtResponse === FALSE) {
    $thought = "Unable to fetch thought at this time.";
    $author = "";
} else {
    $thoughtData = json_decode($thoughtResponse, true);
    $thought = htmlspecialchars($thoughtData['quoteText'] ?? "No thought available.");
    $author = htmlspecialchars($thoughtData['quoteAuthor'] ?? "Unknown");
}
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
            /* Add padding to create space for buttons */
            padding-bottom: 100px; /* Adjust this value as needed */
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
            text-align: center;
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
        .profile-info img {
            width: 150px; /* Adjust size as needed */
            height: 150px; /* Adjust size as needed */
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
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
        .thought {
            margin-top: 30px;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 8px;
            text-align: center;
        }
        .thought p {
            font-size: 18px;
            color: #555;
            margin: 0;
        }
        .thought .author {
            font-size: 16px;
            color: #007bff;
            margin-top: 10px;
        }
        .btn-container {
            text-align: center;
            margin-top: 20px; /* Adjust this value as needed */
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard</h1>
        </header>
        <div class="profile-info">
            <img src="<?php echo $profilePicturePath; ?>" alt="Profile Picture">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Date of Birth: <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
            <p>Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
        </div>
        <div class="thought">
            <p>"<?php echo $thought; ?>"</p>
            <p class="author">— <?php echo $author; ?></p>
        </div>
        <!-- Button container -->
        <div class="btn-container">
            <a href="user_manager.php" class="btn">Manage Users</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>
</body>
</html>

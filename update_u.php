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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldUsername = $_POST['old_username'];
    $email = $_POST['email'];
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    
    // Handling file upload
    $profilePicture = $_FILES['profile_picture'];
    $profilePicturePath = '';

    if ($profilePicture['error'] == UPLOAD_ERR_OK) {
        // Define the directory where files will be uploaded
        $uploadDir = '';  // No subdirectory
        $profilePicturePath = basename($profilePicture['name']);
        
        if (move_uploaded_file($profilePicture['tmp_name'], $uploadDir . $profilePicturePath)) {
            // File uploaded successfully
        } else {
            echo "<p>Error uploading file.</p>";
            exit();
        }
    } else {
        // If no new file is uploaded, keep the old file
        $profilePicturePath = $_POST['existing_profile_picture'];
    }

    $sql_update = "UPDATE users SET email=?, date_of_birth=?, gender=?, profile_picture=? WHERE username=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sssss", $email, $dateOfBirth, $gender, $profilePicturePath, $oldUsername);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$username = $_GET['username'];
$sql_user = "SELECT username, email, date_of_birth, gender, profile_picture FROM users WHERE username=?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
    echo "Error fetching user information.";
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
    <title>Edit User</title>
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
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 90%;
            width: 400px;
            text-align: center;
            transition: box-shadow 0.3s ease;
        }
        .container:hover {
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
        }
        h2 {
            margin-bottom: 15px;
            color: #333;
            font-size: 24px;
        }
        form {
            text-align: left;
        }
        label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"], input[type="email"], input[type="date"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 5px 0;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                width: 90%;
            }
            h2 {
                font-size: 20px;
            }
            input[type="submit"] {
                font-size: 12px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit User</h2>
        <form action="update_u.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="old_username" value="<?php echo htmlspecialchars($user['username']); ?>">
            <input type="hidden" name="existing_profile_picture" value="<?php echo htmlspecialchars($user['profile_picture']); ?>">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male" <?php echo $user['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo $user['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                <option value="other" <?php echo $user['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture">
            <input type="submit" value="Update">
        </form>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>

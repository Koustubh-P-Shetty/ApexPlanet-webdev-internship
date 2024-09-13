<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "user_management";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];

    if (empty($username) || empty($email) || empty($password) || empty($dateOfBirth) || empty($gender)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        $conn->close();
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        $conn->close();
        exit();
    }

    if (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long.'); window.history.back();</script>";
        $conn->close();
        exit();
    }

    $sql_check = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $email, $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>alert('This email or username is already registered. Please use another one.'); window.history.back();</script>";
        $stmt_check->close();
        $conn->close();
        exit();
    }

    $stmt_check->close();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password, date_of_birth, gender) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $email, $hashed_password, $dateOfBirth, $gender);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = 'login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            width: 100%;
            transition: box-shadow 0.3s ease;
        }
        .container:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        form {
            display: flex;
            flex-direction: column;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #444;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"], select {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="date"]:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #0056b3;
        }
        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            h2 {
                font-size: 1.5rem;
            }
            input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <form name="registerForm" action="register.php" method="post" onsubmit="return validateForm()">
            <h2>Create Account</h2>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <input type="submit" value="Register">
            <a href="login.php">Already have an account? Login here</a>
        </form>
    </div>
</body>
</html>

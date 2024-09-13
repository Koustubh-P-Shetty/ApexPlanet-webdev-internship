<?php
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "user_management";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password. Please try again.";
            }
        } else {
            $error = "No account found with this email. Please <a href='register.php'>register here</a>.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus, input[type="password"]:focus {
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
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
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
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <h2>Login</h2>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
            <a href="register.php">Don't have an account? Register here</a>
        </form>
    </div>
</body>
</html>

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

// Fetch current user's role
$current_user = $_SESSION['username'];
$sql_current_user = "SELECT role FROM users WHERE username=?";
$stmt_current_user = $conn->prepare($sql_current_user);
$stmt_current_user->bind_param("s", $current_user);
$stmt_current_user->execute();
$result_current_user = $stmt_current_user->get_result();
$current_user_role = 'user'; // Default to user
if ($result_current_user->num_rows > 0) {
    $row = $result_current_user->fetch_assoc();
    $current_user_role = $row['role'];
}
$stmt_current_user->close();

// Fetch users and admins
$sql_users = "SELECT username, email, date_of_birth, gender FROM users WHERE role='user'";
$sql_admins = "SELECT username, email, date_of_birth, gender FROM users WHERE role='admin'";

$result_users = $conn->query($sql_users);
$result_admins = $conn->query($sql_admins);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manager</title>
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
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn, .action-btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 16px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .action-btn {
            padding: 8px 12px;
            margin: 0 5px;
            font-size: 14px;
        }
        .update-btn {
            background-color: #28a745;
        }
        .update-btn:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Management</h1>

        <h2>Admins</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result_admins && $result_admins->num_rows > 0) {
                while ($row = $result_admins->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td>";
                    // Display actions for all users
                    echo "<a href='update_u.php?username=" . urlencode($row['username']) . "' class='action-btn update-btn'>Update</a>";
                    echo "<a href='delete_u.php?username=" . urlencode($row['username']) . "' class='action-btn delete-btn'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='error'>No admins found.</td></tr>";
            }
            ?>
        </table>

        <h2>Users</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result_users && $result_users->num_rows > 0) {
                while ($row = $result_users->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td>";
                    // Display actions for all users
                    echo "<a href='update_u.php?username=" . urlencode($row['username']) . "' class='action-btn update-btn'>Update</a>";
                    echo "<a href='delete_u.php?username=" . urlencode($row['username']) . "' class='action-btn delete-btn'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='error'>No users found.</td></tr>";
            }
            ?>
        </table>

        <a href="dashboard.php" class="btn" style="background-color: #007bff;">Back to Dashboard</a>
    </div>
</body>
</html>

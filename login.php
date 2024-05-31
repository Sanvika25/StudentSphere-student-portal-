<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
</head>
<body>
    <h1>Debugging Login Page</h1>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();

    if (isset($_SESSION['user_id'])) {
        header("Location: dashboard.php");
        exit();
    }

    if (isset($_POST['login'])) {
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];

        // Database connection
        $host = 'localhost:8889';
        $username = 'php';
        $password_db = '1234';
        $database = 'student_portal';

        $conn = mysqli_connect($host, $username, $password_db, $database);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Prevent SQL Injection
        $user_id = mysqli_real_escape_string($conn, $user_id);
        $password = mysqli_real_escape_string($conn, $password);

        $sql = "SELECT * FROM users WHERE user_id='$user_id' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $_SESSION['user_id'] = $user_id;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
    ?>
    <h2>End of Debugging</h2>
</body>
</html>

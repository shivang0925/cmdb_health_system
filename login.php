<?php
session_start();

include("includes/db_connect.php");

if (isset($_POST['username'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $result = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE username='$username'"
    );

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Username or Password";
        }
    } else {
        $error = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CMDB Health Monitoring System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 420px;
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .logo {
            width: 80px;
            height: 80px;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            margin: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
        }

        .title {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
            color: #0d6efd;
        }

        .subtitle {
            text-align: center;
            color: gray;
            margin-bottom: 25px;
        }

        .form-control {
            height: 50px;
            border-radius: 10px;
        }

        .btn-login {
            width: 100%;
            height: 50px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .footer-text {
            text-align: center;
            margin-top: 15px;
            color: gray;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <div class="logo">
            HS
        </div>

        <h3 class="title">CMDB Health Monitoring System</h3>
        <?php
        if (isset($error)) {
            ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php
        }
        ?>
        <form method="post">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Enter Username">
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter Password">
            </div>

            <button type="submit" class="btn btn-primary btn-login">
                Login
            </button>

        </form>

        <div class="footer-text">
            MCA Project 2026
        </div>

    </div>

</body>

</html>
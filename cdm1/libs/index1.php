<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($role == "student") {
        $sql = "SELECT * FROM students WHERE usn='$username' AND password='$password'";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $_SESSION['student'] = $username;
            header("Location: student_dashboard.php");
        } else {
            $error = "Invalid Student Login";
        }
    }

    if ($role == "driver") {
        $sql = "SELECT * FROM drivers WHERE username='$username' AND password='$password'";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $_SESSION['driver'] = $username;
            header("Location: driver_dashboard.php");
        } else {
            $error = "Invalid Driver Login";
        }
    }

    if ($role == "admin") {
        $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $_SESSION['admin'] = $username;
            header("Location: admin_dashboard.php");
        } else {
            $error = "Invalid Admin Login";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College Bus Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- External CSS -->
</head>
<body>
    <div class="login-box">
        <h2>🚍 College Bus Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Select Role</label>
                <select name="role" class="form-select" required>
                    <option value="student">Student</option>
                    <option value="driver">Driver</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username / USN" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>

__________________________________
<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($role == "student") {
        $sql = "SELECT * FROM students WHERE usn='$username' AND password='$password'";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $_SESSION['student'] = $username;
            header("Location: student_dashboard.php");
        } else {
            $error = "Invalid Student Login";
        }
    }

    if ($role == "driver") {
        $sql = "SELECT * FROM drivers WHERE username='$username' AND password='$password'";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $_SESSION['driver'] = $username;
            header("Location: driver_dashboard.php");
        } else {
            $error = "Invalid Driver Login";
        }
    }

    if ($role == "admin") {
        $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $_SESSION['admin'] = $username;
            header("Location: admin_dashboard.php");
        } else {
            $error = "Invalid Admin Login";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College Bus Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('bus-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(236, 235, 235, 0.6); /* soft overlay */
            z-index: 0;
        }

        .login-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            z-index: 1;
            position: relative;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: #010911ff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🚍 College Bus Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Select Role</label>
                <select name="role" class="form-select" required>
                    <option value="student">Student</option>
                    <option value="driver">Driver</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username / USN" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>












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


_________________________________________________________@@@
<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
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
            font-style: italic; /* make text italic */
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
            background: rgba(236, 235, 235, 0.6);
            z-index: 0;
        }

        .banner{
        background:linear-gradient(90deg,var(--lav),var(--pink),var(--mint));
        color:#111;font-weight:700;
        padding:10px 0;text-align:center;overflow:hidden;white-space:nowrap;
        position:fixed;top:0;left:0;right:0;z-index:1001;
        }
        .banner span{display:inline-block;animation:scrollText 15s linear infinite;}
        @keyframes scrollText{from{transform:translateX(100%);}to{transform:translateX(-100%);}}

        .login-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(5, 4, 4, 1);
            width: 100%;
            max-width: 400px;
            z-index: 1;
            position: relative;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #c47159ff;
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

        .track-left {
            position: absolute;
            /* moved 1cm down from previous position */
            top: calc(20px + 1cm);
            left: 20px;
            z-index: 2;
        }

        /* Make the Track button yellow */
        .track-left .btn-outline-dark {
            background-color: #ffd700;
            border-color: #cba407ff;
            color: #000;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        }
        .track-left .btn-outline-dark:hover {
            background-color: #e6c200;
            border-color: #d4ad00;
            color: #000;
            transform: translateY(-1px);
        }

        /* Brand color: make "GSSSIETW Bus Management" brown and italic */
        .login-box h6 { color: #333; margin: 0 0 12px 0; font-style: italic; }
        .login-box h6 .brand { color: #8B4513; font-weight: 700; font-style: italic; } /* brown + italic */
    </style>
</head>
<body>

    <!-- Track button + live summary (placed below Track) -->
    <div class="track-wrap" style="position:absolute; top:calc(20px + 3cm); left:20px; z-index:2000;">
        <a href="bus_tracking.php" class="btn btn-outline-dark" style="background:#ffd700;border-color:#e6b800;color:#000;display:inline-flex;align-items:center;padding:8px 12px;border-radius:6px;">
            <!-- inline SVG bus icon -->
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="margin-right:8px" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <rect x="3" y="7" width="18" height="10" rx="2" fill="#ffd700" stroke="rgba(0,0,0,0.08)"/>
                <circle cx="7.5" cy="18.2" r="1.1" fill="#333"/><circle cx="16.5" cy="18.2" r="1.1" fill="#333"/>
            </svg>
            <span style="font-weight:600">TracK</span>
        </a>
    </div>

    <!-- Banner -->
    <div class="banner">
    <span> Welcome to GSSSIETW Bus Management System  Developed by @PriyaSingh |Department of Computer Science in Arificial Intaligence and Mechine learning</span>
    </div>

    <!-- Track Button at Top-Left -->
    <form action="bus_tracking.php" method="GET" class="track-left">
        <button type="submit" class="btn btn-outline-dark"> TracKMyBus</button>
    </form>

    

    <div class="login-box">
        <h6> <span class="brand">GSSSIETW Bus Management</span> Login</h6>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="login" value="1">
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

    <script>
/* fetch counts from bus_tracking.php and update summary (runs on page load and periodically) */
async function loadLiveSummary(){
  try{
    const res = await fetch('bus_tracking.php', {cache:'no-store'});
    const text = await res.text();
    const doc = new DOMParser().parseFromString(text, 'text/html');
    const total = doc.getElementById('totalCount')?.textContent?.trim() ?? '0';
    const active = doc.getElementById('activeCount')?.textContent?.trim() ?? '0';
    const delayed = doc.getElementById('delayedCount')?.textContent?.trim() ?? '0';
    const inactive = doc.getElementById('inactiveCount')?.textContent?.trim() ?? '0';
    document.getElementById('idxTotal').textContent = total;
    document.getElementById('idxActive').textContent = active;
    document.getElementById('idxDelayed').textContent = delayed;
    document.getElementById('idxInactive').textContent = inactive;
  }catch(err){
    console.warn('Live summary load failed', err);
  }
}
loadLiveSummary();
setInterval(loadLiveSummary, 20000);
</script>
</body>
</html>


_________________________












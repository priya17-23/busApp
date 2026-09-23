

<?php
session_start();
include("config.php");

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// ---------- Student Approvals ----------
if (isset($_GET['approve'])) {
    $id = (int) $_GET['approve'];
    $conn->query("UPDATE students SET pass_generated=1 WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}
if (isset($_GET['reject'])) {
    $id = (int) $_GET['reject'];
    $conn->query("UPDATE students SET pass_generated=2 WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}
if (isset($_GET['reset'])) {
    $id = (int) $_GET['reset'];
    $conn->query("UPDATE students SET pass_generated=0 WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}

// ---------- Add Driver ----------
if (isset($_POST['add_driver'])) {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $bus_no = $_POST['bus_no'];
    $route = $_POST['route'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO drivers(username,name,phone,route,bus_no,password) 
                  VALUES('$username','$name','$phone','$route','$bus_no','$password')");
    header("Location: admin_dashboard.php#drivers");
    exit();
}

// ---------- Delete Driver ----------
if (isset($_GET['delete_driver'])) {
    $id = $_GET['delete_driver'];
    $conn->query("DELETE FROM drivers WHERE id=$id");
    header("Location: admin_dashboard.php#drivers");
    exit();
}

// ---------- Data ----------
$students = $conn->query("SELECT * FROM students");
$drivers = $conn->query("SELECT * FROM drivers");
$total = $conn->query("SELECT COUNT(*) c FROM students")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) c FROM students WHERE pass_generated=1")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) c FROM students WHERE pass_generated=0")->fetch_assoc()['c'];
$rejected = $conn->query("SELECT COUNT(*) c FROM students WHERE pass_generated=2")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - GSSSIETW Bus Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
:root {
  --purple: #fdfbffff;
  --blue: #82c4f8;
  --pink: #936c78ff;
  --dark: #3c2a4d;
  --light: #f9f9f9;
}

body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, var(--pink), var(--blue), var(--purple));
  background-attachment: fixed;
  color: var(--dark);
  overflow-x: hidden;
}

.header-bar {
  background: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(6px);
  text-align: center;
  padding: 10px;
  font-weight: 600;
  color: var(--dark);
  animation: moveText 15s linear infinite;
}
@keyframes moveText {
  0% {transform: translateX(100%);}
  100% {transform: translateX(-100%);}
}

/* Sidebar */
.sidebar {
  width: 250px;
  height: 100vh;
  background: rgba(255,255,255,0.25);
  backdrop-filter: blur(8px);
  position: fixed;
  left: 0;
  top: 0;
  padding-top: 80px;
  transition: all 0.4s ease;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}
.sidebar.closed {
  left: -250px;
}
.sidebar a {
  display: block;
  color: var(--dark);
  text-decoration: none;
  padding: 12px 20px;
  border-radius: 10px;
  margin: 6px 15px;
  font-weight: 500;
  transition: 0.3s;
}
.sidebar a.active, .sidebar a:hover {
  background: rgba(255,255,255,0.6);
}

/* Topbar */
.topbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 60px;
  background: rgba(255,255,255,0.3);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  z-index: 100;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.menu-btn {
  background: none;
  border: none;
  color: var(--dark);
  font-size: 1.6rem;
  cursor: pointer;
}

/* Content */
.content {
  margin-left: 260px;
  margin-top: 70px;
  transition: all 0.4s ease;
}
.sidebar.closed ~ .content {
  margin-left: 0;
}
.card {
  border-radius: 15px;
  background: rgba(255,255,255,0.9);
  border: none;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.table thead {
  background: var(--purple);
  color: white;
}
.table tbody tr:hover {
  background: rgba(255,255,255,0.5);
}
</style>
</head>

<body>
<div class="topbar">
  <button class="menu-btn"><i class="fas fa-bars"></i></button>
  <h5 class="m-0 fw-bold">Admin Dashboard</h5>
  <div><i class="fas fa-user-circle"></i> Admin</div>
</div>

<div class="header-bar">🌸 Welcome to GSSSIETW Bus Management System 🌸</div>

<div class="sidebar" id="sidebar">
  <a href="#" class="active" data-section="home"><i class="fas fa-home"></i> Dashboard</a>
  <a href="#" data-section="students"><i class="fas fa-users"></i> Students</a>
  <a href="#" data-section="drivers"><i class="fas fa-id-badge"></i> Drivers</a>
  <a href="#" data-section="reports"><i class="fas fa-chart-line"></i> Reports</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="content">
  <div id="home" class="section">
    <div class="card p-4 text-center">
      <h3>👋 Welcome Admin</h3>
      <p>Manage students, drivers, and approvals easily!</p>
    </div>
    <div class="row mt-4">
      <div class="col-md-3"><div class="p-3 bg-light border rounded text-center">👥 <b>Total:</b> <?= $total ?></div></div>
      <div class="col-md-3"><div class="p-3 bg-warning border rounded text-center">⏳ <b>Pending:</b> <?= $pending ?></div></div>
      <div class="col-md-3"><div class="p-3 bg-success text-white border rounded text-center">✅ <b>Approved:</b> <?= $approved ?></div></div>
      <div class="col-md-3"><div class="p-3 bg-danger text-white border rounded text-center">❌ <b>Rejected:</b> <?= $rejected ?></div></div>
    </div>
  </div>

  <div id="students" class="section mt-4" style="display:none;">
    <div class="card p-4">
      <h4>🎓 Students</h4>
      <table class="table table-bordered mt-3">
        <thead><tr><th>USN</th><th>Name</th><th>Photo</th><th>Receipt</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while($s=$students->fetch_assoc()){ ?>
          <tr>
            <td><?= $s['usn'] ?></td>
            <td><?= $s['name'] ?></td>
            <td><?php if($s['photo']) echo "<img src='{$s['photo']}' width='40'>"; ?></td>
            <td><?php if($s['fee_receipt']) echo "<a href='{$s['fee_receipt']}' target='_blank'>View</a>"; ?></td>
            <td><?= $s['pass_generated']==1?'✅ Approved':($s['pass_generated']==2?'❌ Rejected':'⏳ Pending') ?></td>
            <td>
              <a href="?approve=<?= $s['id'] ?>" class="btn btn-success btn-sm">✔</a>
              <a href="?reject=<?= $s['id'] ?>" class="btn btn-danger btn-sm">✖</a>
              <a href="?reset=<?= $s['id'] ?>" class="btn btn-warning btn-sm">↺</a>
            </td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <div id="drivers" class="section mt-4" style="display:none;">
    <div class="card p-4">
      <h4>🚌 Drivers</h4>
      <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addDriverModal">+ Add Driver</button>
      <table class="table table-bordered">
        <thead><tr><th>Username</th><th>Name</th><th>Phone</th><th>Route</th><th>Bus No</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while($d=$drivers->fetch_assoc()){ ?>
          <tr>
            <td><?= $d['username'] ?></td>
            <td><?= $d['name'] ?></td>
            <td><?= $d['phone'] ?></td>
            <td><?= $d['route'] ?></td>
            <td><?= $d['bus_no'] ?></td>
            <td><a href="?delete_driver=<?= $d['id'] ?>" class="btn btn-danger btn-sm">🗑</a></td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Driver Modal -->
<div class="modal fade" id="addDriverModal">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST">
      <div class="modal-body">
        <input type="hidden" name="add_driver" value="1">
        <input class="form-control mb-2" name="username" placeholder="Username" required>
        <input class="form-control mb-2" name="name" placeholder="Name" required>
        <input class="form-control mb-2" name="phone" placeholder="Phone" required>
        <input class="form-control mb-2" name="route" placeholder="Route" required>
        <input class="form-control mb-2" name="bus_no" placeholder="Bus No" required>
        <input class="form-control mb-2" name="password" placeholder="Password" required>
      </div>
      <div class="modal-footer"><button class="btn btn-success">Save</button></div>
    </form>
  </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById("sidebar");
document.querySelector(".menu-btn").addEventListener("click", () => {
  sidebar.classList.toggle("closed");
});

document.querySelectorAll('.sidebar a[data-section]').forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
    link.classList.add('active');
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById(link.dataset.section).style.display = 'block';
  });
});
</script>
</body>
</html>





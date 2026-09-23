

<?php
session_start();
include("config.php");

// Temporary demo login (replace with session when ready)
$driver_username = "driver1";
$result = $conn->query("SELECT * FROM drivers WHERE username='$driver_username'");
$driver = $result->fetch_assoc();

// Fetch assigned students
$students = $conn->query("SELECT * FROM students WHERE bus_no='{$driver['bus_no']}'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Driver Dashboard - GSSSIETW</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
:root {
  --primary: #e3f06dff;
  --secondary: #f1dd75ff;
  --accent: #c6e4c5ff;
  --dark: #302f2fff;
  --light: #f9f9f9;
}
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  background: linear-gradient(135deg, var(--secondary), var(--accent), var(--primary));
  min-height: 100vh;
  overflow-x: hidden;
}

/* Sidebar */
.sidebar {
  width: 250px;
  height: 100vh;
  background: rgba(48, 5, 5, 0.3);
  backdrop-filter: blur(10px);
  position: fixed;
  top: 0;
  left: 0;
  transition: all 0.3s ease;
  box-shadow: 3px 0 10px rgba(0,0,0,0.1);
  overflow: hidden;
  z-index: 1000;
}
.sidebar.closed {
  width: 70px;
}
.sidebar-header {
  text-align: center;
  padding: 20px;
  font-weight: 700;
  color: var(--dark);
}
.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.sidebar ul li a {
  display: flex;
  align-items: center;
  gap: 15px;
  color: var(--dark);
  padding: 12px 20px;
  text-decoration: none;
  border-radius: 8px;
  margin: 5px 10px;
  transition: 0.3s;
}
.sidebar ul li a:hover, .sidebar ul li a.active {
  background: rgba(2, 68, 37, 0.5);
  color: var(--primary);
}
.sidebar ul li a i {
  width: 20px;
  text-align: center;
}

/* Top Bar */
.topbar {
  position: fixed;
  left: 250px;
  right: 0;
  top: 0;
  height: 60px;
  background: rgba(255,255,255,0.4);
  backdrop-filter: blur(8px);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
  transition: 0.3s;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  z-index: 999;
}
.sidebar.closed + .topbar {
  left: 70px;
}
.menu-btn {
  font-size: 1.4rem;
  border: none;
  background: none;
  color: var(--dark);
}
.profile {
  position: relative;
}
.profile-btn {
  background: none;
  border: none;
  font-weight: 600;
  color: var(--dark);
}
.profile-dropdown {
  display: none;
  position: absolute;
  right: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  min-width: 220px;
  padding: 10px;
  z-index: 999;
}
.profile-dropdown.active {
  display: block;
}
.profile-dropdown p {
  margin: 5px 0;
  color: var(--dark);
  font-size: 0.9rem;
}

/* Moving Banner */
.header-bar {
  position: relative;
  overflow: hidden;
  width: 100%;
  height: 40px;
  background: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(8px);
  border-bottom: 2px solid rgba(0,0,0,0.1);
  margin-top: 60px;
}
.scroll-text {
  position: absolute;
  white-space: nowrap;
  animation: scrollText 20s linear infinite;
  font-weight: 600;
  color: var(--dark);
  padding: 8px 0;
  font-size: 1rem;
}
@keyframes scrollText {
  from { transform: translateX(100%); }
  to { transform: translateX(-100%); }
}

/* Main Content */
.main {
  margin-left: 250px;
  margin-top: 100px;
  padding: 20px;
  transition: all 0.3s ease;
}
.sidebar.closed ~ .main {
  margin-left: 70px;
}

/* Cards */
.card {
  border: none;
  border-radius: 16px;
  background: rgba(255,255,255,0.6);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  padding: 20px;
  margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    left: -250px;
  }
  .sidebar.open {
    left: 0;
  }
  .topbar {
    left: 0;
  }
  .main {
    margin-left: 0;
  }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <i class="fas fa-bus"></i> <span class="sidebar-title">Driver Panel</span>
  </div>
  <ul>
    <li><a href="#" class="nav-link active" data-section="dashboard"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
    <li><a href="#" class="nav-link" data-section="students"><i class="fas fa-users"></i><span>Assigned Students</span></a></li>
    <li><a href="#" class="nav-link" data-section="route"><i class="fas fa-map"></i><span>Route Info</span></a></li>
    <li><a href="#" class="nav-link" data-section="drivers"><i class="fas fa-id-card"></i><span>Add Drivers</span></a></li>
    <li><a href="#" class="nav-link" data-section="messages"><i class="fas fa-comment"></i><span>Messages</span></a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
  </ul>
</div>

<!-- Topbar -->
<div class="topbar">
  <button class="menu-btn" id="menuToggle"><i class="fas fa-bars"></i></button>
  <h4 class="m-0 fw-bold">Driver Dashboard</h4>
  <div class="profile">
    <button class="profile-btn" id="profileBtn"><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($driver['name']) ?></button>
    <div class="profile-dropdown" id="profileDropdown">
      <p><b>Name:</b> <?= htmlspecialchars($driver['name']) ?></p>
      <p><b>Bus No:</b> <?= htmlspecialchars($driver['bus_no']) ?></p>
      <p><b>Phone:</b> <?= htmlspecialchars($driver['phone']) ?></p>
      <p><b>Route:</b> <?= htmlspecialchars($driver['route']) ?></p>
    </div>
  </div>
</div>

<!-- Moving Banner -->
<div class="header-bar">
  <div class="scroll-text">
     Welcome to GSSSIETW Bus Management System - Hello <?= htmlspecialchars($driver['name']) ?>! 
  </div>
</div>

<!-- Main Content -->
<div class="main">
  <!-- Dashboard -->
  <div id="dashboard" class="section active">
    <div class="card">
      <h5><i class="fas fa-info-circle me-2"></i> Welcome <?= htmlspecialchars($driver['name']) ?></h5>
      <p>Bus No: <?= htmlspecialchars($driver['bus_no']) ?> | Route: <?= htmlspecialchars($driver['route']) ?></p>
      <p>You have <?= $students->num_rows ?> students assigned.</p>
    </div>
  </div>

  <!-- Assigned Students -->
  <div id="students" class="section" style="display:none;">
    <div class="card">
      <h5><i class="fas fa-users me-2"></i> Assigned Students</h5>
      <table class="table table-striped table-bordered mt-3">
        <thead class="table-light">
          <tr>
            <th>USN</th>
            <th>Name</th>
            <th>Branch</th>
            <th>Stop Name</th>
          </tr>
        </thead>
        <tbody>
        <?php while($s = $students->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($s['usn']) ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['branch']) ?></td>
            <td><?= htmlspecialchars($s['stop_name']) ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Route Info -->
  <div id="route" class="section" style="display:none;">
    <div class="card">
      <h5><i class="fas fa-map-marker-alt me-2"></i> Route Information</h5>
      <p>Bus Number: <strong><?= htmlspecialchars($driver['bus_no']) ?></strong></p>
      <p>Route: <strong><?= htmlspecialchars($driver['route']) ?></strong></p>
    </div>
  </div>

  <!-- Add Drivers -->
  <div id="drivers" class="section" style="display:none;">
    <div class="card">
      <h5><i class="fas fa-id-card me-2"></i> Add New Driver</h5>
      <form method="POST" action="add_driver.php" class="mt-3">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Driver Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Bus No</label>
            <input type="text" name="bus_no" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" required>
          </div>
          <div class="col-md-12">
            <label class="form-label">Route</label>
            <input type="text" name="route" class="form-control" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Add Driver</button>
      </form>
    </div>
  </div>

  <!-- Messages -->
  <div id="messages" class="section" style="display:none;">
    <div class="card">
      <h5><i class="fas fa-comment me-2"></i> Messages</h5>
      <p>No messages available yet.</p>
    </div>
  </div>
</div>

<script>
// Sidebar toggle behavior
const sidebar = document.getElementById('sidebar');
const menuToggle = document.getElementById('menuToggle');
menuToggle.addEventListener('click', () => {
  sidebar.classList.toggle('closed');
  const spans = sidebar.querySelectorAll('span');
  if (sidebar.classList.contains('closed')) {
    spans.forEach(span => span.style.display = 'none');
  } else {
    setTimeout(() => {
      spans.forEach(span => span.style.display = 'inline');
    }, 200);
  }
});

// Profile dropdown toggle
document.getElementById('profileBtn').addEventListener('click', () => {
  document.getElementById('profileDropdown').classList.toggle('active');
});

// Navigation sections toggle
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    this.classList.add('active');
    const target = this.getAttribute('data-section');
    document.querySelectorAll('.section').forEach(sec => sec.style.display = 'none');
    document.getElementById(target).style.display = 'block';
  });
});
</script>

</body>
</html>




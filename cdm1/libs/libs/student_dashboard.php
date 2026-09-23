
<?php
session_start();
include("config.php");

if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$usn = $conn->real_escape_string($_SESSION['student']);
$student = $conn->query("SELECT * FROM students WHERE usn='$usn'")->fetch_assoc();

// Get driver info
$driver = ['name'=>'Not Assigned','bus_no'=>'N/A','route'=>'N/A','phone'=>'N/A'];
if (!empty($student['driver_id'])) {
    $driver = $conn->query("SELECT * FROM drivers WHERE id=".(int)$student['driver_id'])->fetch_assoc();
}

// Paths for uploaded files
$busPass = !empty($student['bus_pass']) ? "uploads/passes/".$student['bus_pass'] : null;
$receiptFile = !empty($student['fee_receipt']) ? "uploads/receipts/".$student['fee_receipt'] : null;
$photoFile = !empty($student['photo']) ? "uploads/photos/".$student['photo'] : "https://via.placeholder.com/80";


// Handle pass request submission
if (isset($_POST['request_pass'])) {
    $feeReceipt = $_FILES['fee_receipt'];
    $photo = $_FILES['photo'];

    $receiptName = time() . "_" . basename($feeReceipt['name']);
    $photoName = time() . "_" . basename($photo['name']);

    $receiptPath = "uploads/receipts/" . $receiptName;
    $photoPath = "uploads/photos/" . $photoName;

    move_uploaded_file($feeReceipt['tmp_name'], $receiptPath);
    move_uploaded_file($photo['tmp_name'], $photoPath);

    $conn->query("UPDATE students SET fee_receipt='$receiptName', photo='$photoName', pass_generated=0 WHERE usn='$usn'");
    header("Location: student.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pastel Student Dashboard</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
:root{
  --lav:#cdb4db;--pink:#fbc4ab;--blue:#a5c4ff;--mint:#b8e0d2;--peach:#ffd6a5;--dark:#333;
}
body{
  font-family:"Poppins",sans-serif;
  background:linear-gradient(120deg,var(--blue),var(--mint));
  overflow-x:hidden;margin:0;
}

/* Moving banner */
.banner{
  background:linear-gradient(90deg,var(--lav),var(--pink),var(--mint));
  color:#111;font-weight:700;
  padding:10px 0;text-align:center;overflow:hidden;white-space:nowrap;
  position:fixed;top:0;left:0;right:0;z-index:1001;
}
.banner span{display:inline-block;animation:scrollText 15s linear infinite;}
@keyframes scrollText{from{transform:translateX(100%);}to{transform:translateX(-100%);}}

/* Sidebar */
.sidebar{
  width:260px;height:100vh;
  background:linear-gradient(180deg,var(--lav),var(--peach));
  position:fixed;top:50px;left:0;transition:0.3s;
  padding-top:20px;box-shadow:3px 0 10px rgba(0,0,0,0.1);z-index:1000;
}
.sidebar.hidden{left:-260px;}
.sidebar a{
  display:block;padding:12px 20px;margin:6px 15px;border-radius:10px;
  background:rgba(255,255,255,0.5);color:#222;text-decoration:none;
  transition:0.3s;font-weight:500;
}
.sidebar a:hover,.sidebar a.active{background:var(--mint);color:#000;transform:translateX(5px);}

/* Toggle btn */
.toggle-btn{
  position:fixed;top:10px;left:15px;
  background:var(--mint);border:none;
  padding:8px 10px;border-radius:8px;cursor:pointer;z-index:1100;
}
.toggle-btn i{font-size:1.3rem;color:#000;}

/* Profile button top right */
.profile-btn{
  position:fixed;top:10px;right:20px;
  display:flex;align-items:center;gap:8px;
  background:var(--peach);padding:8px 12px;border:none;border-radius:25px;
  cursor:pointer;font-weight:600;z-index:1100;
}
.profile-btn img{width:35px;height:35px;border-radius:50%;border:2px solid #fff;}
.profile-popup{
  position:fixed;top:60px;right:20px;background:white;
  border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.2);
  padding:20px;width:260px;display:none;z-index:1200;
}
.profile-popup.active{display:block;animation:fadeIn 0.3s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(-5px);}to{opacity:1;transform:translateY(0);}}

/* Main */
.main{margin-left:270px;padding:80px 25px;transition:0.3s;}
.sidebar.hidden + .main{margin-left:20px;}
.section{
  display:none;background:linear-gradient(120deg,var(--peach),var(--lav));
  border-radius:15px;padding:25px;margin-bottom:25px;box-shadow:0 4px 15px rgba(0,0,0,0.1);
}
.section.active{display:block;animation:fadeIn 0.5s ease;}
.btn-pastel{
  background:linear-gradient(90deg,var(--mint),var(--blue));
  border:none;font-weight:600;border-radius:8px;padding:10px 20px;color:#111;
}
.btn-pastel:hover{background:linear-gradient(90deg,var(--pink),var(--lav));}
.card-pastel{
  background:linear-gradient(120deg,var(--mint),var(--blue));
  border-radius:12px;padding:20px;text-align:center;
  font-weight:600;box-shadow:0 5px 12px rgba(0,0,0,0.1);
}
</style>
</head>
<body>

<!-- Banner -->
<div class="banner">
  <span>🌸 Welcome to GSSSIETW Bus Management System — Hello, <?= htmlspecialchars($student['name']) ?>! 🌼</span>
</div>

<!-- Sidebar Toggle -->
<button class="toggle-btn"><i class="fas fa-bars"></i></button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h4 class="text-center"><i class="fas fa-bus"></i> Student Panel</h4>
  <a href="#" class="active" data-section="home"><i class="fas fa-home"></i> Dashboard</a>
  <a href="#" data-section="upload"><i class="fas fa-upload"></i> Upload Bus Pass</a>
  <a href="#" data-section="uploaddocs"><i class="fas fa-file-upload"></i> Upload Documents</a>
  <a href="#" data-section="download"><i class="fas fa-file-download"></i> Download Receipt</a>
  <a href="#" data-section="message"><i class="fas fa-comment-dots"></i> Message Driver</a>
  <a href="#" data-section="requestpass"><i class="fas fa-id-card"></i> Request Pass</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Profile Button -->
<button class="profile-btn" id="profileBtn">
  <img src="<?= htmlspecialchars($photoFile) ?>" alt="Profile">
  <?= htmlspecialchars($student['name']) ?>
</button>

<!-- Profile Popup -->
<div class="profile-popup" id="profilePopup">
  <h5 class="mb-2 text-center"><i class="fas fa-user-circle"></i> Profile Details</h5>
  <hr>
  <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
  <p><strong>USN:</strong> <?= htmlspecialchars($student['usn']) ?></p>
  <p><strong>Branch:</strong> <?= htmlspecialchars($student['branch'] ?? 'CSE') ?></p>
  <p><strong>Bus No:</strong> <?= htmlspecialchars($driver['bus_no']) ?></p>
  <p><strong>Route:</strong> <?= htmlspecialchars($driver['route']) ?></p>
  <p><strong>Pass No:</strong> <?= htmlspecialchars($student['id']) ?></p>
</div>

<!-- Main -->
<div class="main">
  <div id="home" class="section active">
    <h3>🎓 Dashboard</h3>
    <div class="row g-3 mt-2">
      <div class="col-md-4"><div class="card-pastel"><i class="fas fa-user-tie"></i><br>Driver: <?= htmlspecialchars($driver['name']) ?></div></div>
      <div class="col-md-4"><div class="card-pastel"><i class="fas fa-bus"></i><br>Bus No: <?= htmlspecialchars($driver['bus_no']) ?></div></div>
      <div class="col-md-4"><div class="card-pastel"><i class="fas fa-route"></i><br>Route: <?= htmlspecialchars($driver['route']) ?></div></div>
    </div>
  </div>

  <div id="upload" class="section">
    <h3>📤 Upload Bus Pass</h3>
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="bus_pass" accept=".pdf" class="form-control mb-2" required>
      <button type="submit" name="upload_pass" class="btn-pastel"><i class="fas fa-upload"></i> Upload</button>
    </form>
  </div>

  <div id="uploaddocs" class="section">
    <h3>🧾 Upload Fee Receipt & Photo</h3>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3"><label>Fee Receipt (PDF)</label><input type="file" name="fee_receipt" accept=".pdf" class="form-control"></div>
      <div class="mb-3"><label>Photo (JPG/PNG)</label><input type="file" name="photo" accept=".jpg,.jpeg,.png" class="form-control"></div>
      <button type="submit" name="upload_docs" class="btn-pastel"><i class="fas fa-file-upload"></i> Upload</button>
    </form>
  </div>

  <div id="requestpass" class="section">
  <h3>📝 Request Bus Pass</h3>
  <?php if ($student['pass_generated'] == 0): ?>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label>Upload Fee Receipt (PDF)</label>
        <input type="file" name="fee_receipt" accept=".pdf" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Upload Photo (JPG/PNG)</label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="form-control" required>
      </div>
      <button type="submit" name="request_pass" class="btn-pastel"><i class="fas fa-paper-plane"></i> Submit Request</button>
    </form>
  <?php elseif ($student['pass_generated'] == 1): ?>
    <div class="alert alert-success">✅ Your pass has been approved. You can download it from the "Download Receipt" section.</div>
  <?php elseif ($student['pass_generated'] == 2): ?>
    <div class="alert alert-danger">❌ Your request was rejected. Please contact admin.</div>
  <?php else: ?>
    <div class="alert alert-info">⏳ Your request is pending approval.</div>
  <?php endif; ?>
</div>

  <div id="download" class="section">
    <h3>📥 Download Fee Receipt</h3>
    <?php if ($receiptFile && file_exists($receiptFile)): ?>
      <a href="<?= $receiptFile ?>" download class="btn-pastel"><i class="fas fa-download"></i> Download Receipt</a>
    <?php else: ?><div class="alert alert-warning">⚠️ No receipt uploaded.</div><?php endif; ?>
  </div>

  <div class="mt-4">
  <h3>🎫 Download Bus Pass</h3>
  <?php if ($student['pass_generated'] == 1): ?>
    <a href="generate_pass.php" target="_blank" class="btn-pastel"><i class="fas fa-id-card"></i> Download Bus Pass</a>
  <?php else: ?>
    <div class="alert alert-info">⏳ Your pass is not approved yet.</div>
  <?php endif; ?>
</div>

  <div id="message" class="section">
    <h3>💬 Message Driver</h3>
    <a href="send_message.php?driver_id=<?= $driver['id'] ?? 0 ?>" class="btn-pastel"><i class="fas fa-paper-plane"></i> Send Message</a>
  </div>
</div>

<script>
// Sidebar toggle
const sidebar=document.getElementById("sidebar");
document.querySelector(".toggle-btn").onclick=()=>sidebar.classList.toggle("hidden");

// Profile toggle
const profileBtn=document.getElementById("profileBtn");
const profilePopup=document.getElementById("profilePopup");
profileBtn.onclick=()=>profilePopup.classList.toggle("active");

// Section switching
document.querySelectorAll('.sidebar a[data-section]').forEach(link=>{
  link.addEventListener('click',function(e){
    e.preventDefault();
    document.querySelectorAll('.sidebar a').forEach(a=>a.classList.remove('active'));
    this.classList.add('active');
    const target=this.getAttribute('data-section');
    document.querySelectorAll('.section').forEach(sec=>sec.classList.remove('active'));
    document.getElementById(target).classList.add('active');
  });
});
</script>
</body>
</html>





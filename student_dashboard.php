 
<?php
session_start();
include("config.php");

if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$usn = $_SESSION['student'];
$upload_msg = $notify_msg = $absence_msg = "";

// Upload photo and fee receipt
if (isset($_POST['upload'])) {
    $photo = "uploads/" . basename($_FILES['photo']['name']);
    $fee = "uploads/" . basename($_FILES['fee']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    move_uploaded_file($_FILES['fee']['tmp_name'], $fee);

    $sql = "UPDATE students SET photo='$photo', fee_receipt='$fee' WHERE usn='$usn'";
    $conn->query($sql);
    $upload_msg = "✅ Uploaded Successfully. Wait for Admin Approval.";
}

// Notify driver
if (isset($_POST['not_coming'])) {
    $msg = "Not coming today";
    $sql = "INSERT INTO messages(student_id, driver_id, message) 
            VALUES((SELECT id FROM students WHERE usn='$usn'), 1, '$msg')";
    $conn->query($sql);
    $notify_msg = "📩 Driver Notified!";
}

// Report absence with reason
if (isset($_POST['report_absence'])) {
    $reason = $conn->real_escape_string($_POST['reason']);
    $student_id = $conn->query("SELECT id FROM students WHERE usn='$usn'")->fetch_assoc()['id'];
    $msg = $reason ? "Not coming today: $reason" : "Not coming today";
    $conn->query("INSERT INTO messages(student_id, driver_id, message) VALUES('$student_id', 1, '$msg')");
    $absence_msg = "📌 Absence info sent to driver.";
}

// Check pass status and get student data
$student_data = $conn->query("SELECT * FROM students WHERE usn='$usn'")->fetch_assoc();
$status = $student_data['pass_generated'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2f7;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(to bottom, #2c3e50, #1a252f);
            color: white;
            padding: 30px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
        }
        .sidebar h4 {
            margin-bottom: 30px;
            font-weight: bold;
        }
        .sidebar .nav-link {
            color: white;
            padding: 10px 0;
            font-size: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: background 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .main-content {
            margin-left: 240px;
            padding: 40px;
        }
        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #34495e;
        }
        .btn-custom {
            width: 100%;
            margin-top: 10px;
        }
        .alert {
            margin-top: 15px;
        }
        textarea {
            resize: none;
        }
        
        /* Bus Pass Styles */
        .bus-pass {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
        .bus-pass::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2" x="2" y="2" width="96" height="96" rx="10" ry="10"/></svg>') 0 0 repeat;
            opacity: 0.3;
            transform: rotate(45deg);
        }
        .pass-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .pass-header h3 {
            margin: 0;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .pass-header p {
            margin: 5px 0 0;
            opacity: 0.8;
        }
        .pass-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        .pass-details {
            flex: 1;
        }
        .pass-detail-item {
            margin-bottom: 10px;
            display: flex;
        }
        .pass-detail-item strong {
            min-width: 120px;
            display: inline-block;
        }
        .pass-photo {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            overflow: hidden;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .pass-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pass-footer {
            margin-top: 20px;
            text-align: center;
            position: relative;
            font-size: 12px;
            opacity: 0.8;
        }
        .pass-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .bus-pass, .bus-pass * {
                visibility: visible;
            }
            .bus-pass {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
            }
            .pass-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>📚 Student Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-home me-2"></i> Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#upload-section"><i class="fas fa-upload me-2"></i> Upload Docs</a></li>
            <li class="nav-item"><a class="nav-link" href="#notify-section"><i class="fas fa-bell me-2"></i> Notify Driver</a></li>
            <li class="nav-item"><a class="nav-link" href="#absence-section"><i class="fas fa-calendar-times me-2"></i> Report Absence</a></li>
            <li class="nav-item"><a class="nav-link" href="#pass-section"><i class="fas fa-id-card me-2"></i> My Pass</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>🎓 Welcome, <?= htmlspecialchars($usn) ?></h2>

        <?php if ($upload_msg): ?>
            <div class="alert alert-success text-center"><?= $upload_msg ?></div>
        <?php endif; ?>
        <?php if ($notify_msg): ?>
            <div class="alert alert-info text-center"><?= $notify_msg ?></div>
        <?php endif; ?>
        <?php if ($absence_msg): ?>
            <div class="alert alert-warning text-center"><?= $absence_msg ?></div>
        <?php endif; ?>

        <div id="upload-section" class="dashboard-card">
            <div class="section-title">📤 Upload Photo & Fee Receipt</div>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Upload Photo</label>
                    <input type="file" name="photo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Fee Receipt</label>
                    <input type="file" name="fee" class="form-control" required>
                </div>
                <button type="submit" name="upload" class="btn btn-success btn-custom">📤 Upload Documents</button>
            </form>
        </div>

        <div id="notify-section" class="dashboard-card">
            <div class="section-title">🚫 Notify Driver: Not Coming Today</div>
            <form method="POST">
                <button type="submit" name="not_coming" class="btn btn-warning btn-custom">📩 Send Notification</button>
            </form>
        </div>

        <div id="absence-section" class="dashboard-card">
            <div class="section-title">🏫 Report College Absence</div>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Reason (optional)</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Sick, personal work..."></textarea>
                </div>
                <button type="submit" name="report_absence" class="btn btn-secondary btn-custom">📌 Submit Absence Info</button>
            </form>
        </div>

        <div id="pass-section" class="dashboard-card">
    <div class="section-title">🎫 Bus Pass Status</div>

    <?php if ($status == 1): ?>
        <div class="alert alert-success">✅ Your Bus Pass has been approved!</div>

        <!-- Bus Pass Display -->
        <div class="bus-pass border p-4 rounded shadow bg-white">
            <div class="text-center mb-3">
                <h4 class="fw-bold">GEETHA SHISHU SHIKSHANA SANGHA (R)</h4>
                <p class="mb-0">INSTITUTE OF ENGINEERING AND TECHNOLOGY FOR WOMEN</p>
                <small class="text-muted">Affiliated to VTU, Belagavi | Approved by AICTE, New Delhi</small>
                <p class="mt-2">📍 KRS Road, Metagalli, Mysuru - 570016</p>
                <h5 class="mt-3 text-decoration-underline">BUS PASS : 2025–26</h5>
            </div>

            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-2"><strong>Route No:</strong> <?= htmlspecialchars($student_data['route_no'] ?? 'To be assigned') ?></div>
                    <div class="mb-2"><strong>Stop Name:</strong> <?= htmlspecialchars($student_data['stop_name'] ?? 'Pending') ?></div>
                    <div class="mb-2"><strong>USN / ID No:</strong> <?= htmlspecialchars($student_data['usn']) ?></div>
                    <div class="mb-2"><strong>Branch:</strong> <?= htmlspecialchars($student_data['branch'] ?? 'N/A') ?></div>
                    <div class="mb-2"><strong>Semester:</strong> <?= htmlspecialchars($student_data['sem'] ?? 'N/A') ?></div>
                    <div class="mb-2"><strong>Mobile No:</strong> <?= htmlspecialchars($student_data['mobile'] ?? 'N/A') ?></div>
                    <div class="mb-2"><strong>Receipt No:</strong> <?= htmlspecialchars($student_data['receipt_no'] ?? 'N/A') ?></div>
                </div>

                <div class="col-md-4 text-center">
                    <?php if ($student_data['photo']): ?>
                        <img src="<?= $student_data['photo'] ?>" alt="Student Photo" class="img-thumbnail" style="max-height: 150px;">
                    <?php else: ?>
                        <div class="border d-flex align-items-center justify-content-center" style="height: 150px;">
                            <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <p class="mt-2"><em>Authorized Signatory</em></p>
                </div>
            </div>

            <div class="mt-3 text-center">
                <small class="text-muted">This pass is property of GSSS IETW. Misuse will lead to cancellation.</small>
            </div>
        </div>

        <!-- Actions -->
        <div class="pass-actions mt-4 text-center">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="fas fa-print me-1"></i> Print Pass
            </button>
            <button class="btn btn-success">
                <i class="fas fa-download me-1"></i> Download PDF
            </button>
        </div>

        <?php elseif ($status == 2): ?>
            <div class="alert alert-danger">❌ Your application has been rejected. Please contact admin.</div>
        <?php else: ?>
            <div class="alert alert-warning">⏳ Waiting for Admin Approval. Please check back later.</div>
        <?php endif; ?>
    </div>
           
    <script>
        // Simple print functionality
        function printPass() {
            window.print();
        }
    </script>
</body>
</html> 












<?php
session_start();
include("config.php");

if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$usn = $_SESSION['student'];
$upload_msg = $notify_msg = $absence_msg = "";

// Upload photo and fee receipt
if (isset($_POST['upload'])) {
    $photo = "uploads/" . basename($_FILES['photo']['name']);
    $fee = "uploads/" . basename($_FILES['fee']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    move_uploaded_file($_FILES['fee']['tmp_name'], $fee);

    $sql = "UPDATE students SET photo='$photo', fee_receipt='$fee' WHERE usn='$usn'";
    $conn->query($sql);
    $upload_msg = "✅ Uploaded Successfully. Wait for Admin Approval.";
}

// Notify driver
if (isset($_POST['not_coming'])) {
    $msg = "Not coming today";
    $sql = "INSERT INTO messages(student_id, driver_id, message) 
            VALUES((SELECT id FROM students WHERE usn='$usn'), 1, '$msg')";
    $conn->query($sql);
    $notify_msg = "📩 Driver Notified!";
}

// Report absence with reason
if (isset($_POST['report_absence'])) {
    $reason = $conn->real_escape_string($_POST['reason']);
    $student_id = $conn->query("SELECT id FROM students WHERE usn='$usn'")->fetch_assoc()['id'];
    $msg = $reason ? "Not coming today: $reason" : "Not coming today";
    $conn->query("INSERT INTO messages(student_id, driver_id, message) VALUES('$student_id', 1, '$msg')");
    $absence_msg = "📌 Absence info sent to driver.";
}

// Check pass status
$status = $conn->query("SELECT pass_generated FROM students WHERE usn='$usn'")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2f7;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(to bottom, #2c3e50, #1a252f);
            color: white;
            padding: 30px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
        }
        .sidebar h4 {
            margin-bottom: 30px;
            font-weight: bold;
        }
        .sidebar .nav-link {
            color: white;
            padding: 10px 0;
            font-size: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: background 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .main-content {
            margin-left: 240px;
            padding: 40px;
        }
        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #34495e;
        }
        .btn-custom {
            width: 100%;
            margin-top: 10px;
        }
        .alert {
            margin-top: 15px;
        }
        textarea {
            resize: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>📚 Student Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="#">🏠 Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#">📤 Upload Docs</a></li>
            <li class="nav-item"><a class="nav-link" href="#">🚫 Notify Driver</a></li>
            <li class="nav-item"><a class="nav-link" href="#">🏫 Report Absence</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">🔓 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>🎓 Welcome, <?= htmlspecialchars($usn) ?></h2>

        <?php if ($upload_msg): ?>
            <div class="alert alert-success text-center"><?= $upload_msg ?></div>
        <?php endif; ?>
        <?php if ($notify_msg): ?>
            <div class="alert alert-info text-center"><?= $notify_msg ?></div>
        <?php endif; ?>
        <?php if ($absence_msg): ?>
            <div class="alert alert-warning text-center"><?= $absence_msg ?></div>
        <?php endif; ?>

        <div class="dashboard-card">
            <div class="section-title">📤 Upload Photo & Fee Receipt</div>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Upload Photo</label>
                    <input type="file" name="photo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Fee Receipt</label>
                    <input type="file" name="fee" class="form-control" required>
                </div>
                <button type="submit" name="upload" class="btn btn-success btn-custom">📤 Upload Documents</button>
            </form>
        </div>

        <div class="dashboard-card">
            <div class="section-title">🚫 Notify Driver: Not Coming Today</div>
            <form method="POST">
                <button type="submit" name="not_coming" class="btn btn-warning btn-custom">📩 Send Notification</button>
            </form>
        </div>

        <div class="dashboard-card">
            <div class="section-title">🏫 Report College Absence</div>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Reason (optional)</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Sick, personal work..."></textarea>
                </div>
                <button type="submit" name="report_absence" class="btn btn-secondary btn-custom">📌 Submit Absence Info</button>
            </form>
        </div>

        <div class="dashboard-card">
            <div class="section-title">🎫 Bus Pass Status</div>
            <?php if ($status['pass_generated'] == 1): ?>
                <div class="alert alert-success">✅ Pass Generated</div>
            <?php else: ?>
                <div class="alert alert-warning">⏳ Waiting for Admin Approval</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>  
<?php
session_start();
include("config.php");

if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$usn = $_SESSION['student'];
$upload_msg = $notify_msg = $absence_msg = "";

// Upload photo and fee receipt
if (isset($_POST['upload'])) {
    $photo = "uploads/" . basename($_FILES['photo']['name']);
    $fee = "uploads/" . basename($_FILES['fee']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    move_uploaded_file($_FILES['fee']['tmp_name'], $fee);

    $sql = "UPDATE students SET photo='$photo', fee_receipt='$fee' WHERE usn='$usn'";
    $conn->query($sql);
    $upload_msg = "✅ Uploaded Successfully. Wait for Admin Approval.";
}

// Notify driver
if (isset($_POST['not_coming'])) {
    $msg = "Not coming today";
    $sql = "INSERT INTO messages(student_id, driver_id, message) 
            VALUES((SELECT id FROM students WHERE usn='$usn'), 1, '$msg')";
    $conn->query($sql);
    $notify_msg = "📩 Driver Notified!";
}

// Report absence with reason
if (isset($_POST['report_absence'])) {
    $reason = $conn->real_escape_string($_POST['reason']);
    $student_id = $conn->query("SELECT id FROM students WHERE usn='$usn'")->fetch_assoc()['id'];
    $msg = $reason ? "Not coming today: $reason" : "Not coming today";
    $conn->query("INSERT INTO messages(student_id, driver_id, message) VALUES('$student_id', 1, '$msg')");
    $absence_msg = "📌 Absence info sent to driver.";
}

// Check pass status and get student data
$student_data = $conn->query("SELECT * FROM students WHERE usn='$usn'")->fetch_assoc();
$status = $student_data['pass_generated'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2f7;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(to bottom, #2c3e50, #1a252f);
            color: white;
            padding: 30px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
        }
        .sidebar h4 {
            margin-bottom: 30px;
            font-weight: bold;
        }
        .sidebar .nav-link {
            color: white;
            padding: 10px 0;
            font-size: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: background 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .main-content {
            margin-left: 240px;
            padding: 40px;
        }
        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #34495e;
        }
        .btn-custom {
            width: 100%;
            margin-top: 10px;
        }
        .alert {
            margin-top: 15px;
        }
        textarea {
            resize: none;
        }
        
        /* Bus Pass Styles */
        .bus-pass {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
        .bus-pass::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2" x="2" y="2" width="96" height="96" rx="10" ry="10"/></svg>') 0 0 repeat;
            opacity: 0.3;
            transform: rotate(45deg);
        }
        .pass-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .pass-header h3 {
            margin: 0;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .pass-header p {
            margin: 5px 0 0;
            opacity: 0.8;
        }
        .pass-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        .pass-details {
            flex: 1;
        }
        .pass-detail-item {
            margin-bottom: 10px;
            display: flex;
        }
        .pass-detail-item strong {
            min-width: 120px;
            display: inline-block;
        }
        .pass-photo {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            overflow: hid
            ---------------------------------------------$_COOKIE
            <div id="pass-section" class="dashboard-card">
    <div class="section-title">🎫 Bus Pass Status</div>

    <?php if ($status == 1): ?>
        <div class="alert alert-success">✅ Your Bus Pass has been approved!</div>

        <!-- Bus Pass Display -->
        <div class="bus-pass border p-4 rounded shadow bg-white">
            <div class="text-center mb-3">
                <h4 class="fw-bold">GEETHA SHISHU SHIKSHANA SANGHA (R)</h4>
                <p class="mb-0">INSTITUTE OF ENGINEERING AND TECHNOLOGY FOR WOMEN</p>
                <small class="text-muted">Affiliated to VTU, Belagavi | Approved by AICTE, New Delhi</small>
                <p class="mt-2">📍 KRS Road, Metagalli, Mysuru - 570016</p>
                <h5 class="mt-3 text-decoration-underline">BUS PASS : 2025–26</h5>
            </div>

            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-2"><strong>Route No:</strong> <?= htmlspecialchars($student_data['route_no'] ?? 'To be assigned') ?></div>
                    <div class="mb-2"><strong>Stop Name:</strong> <?= htmlspecialchars($student_data['stop_name'] ?? 'Pending') ?></div>
                    <div class="mb-2"><strong>USN / ID No:</strong> <?= htmlspecialchars($student_data['usn']) ?></div>
                    <div class="mb-2"><strong>Branch:</strong> <?= htmlspecialchars($student_data['branch'] ?? 'N/A') ?></div>
                    <div class="mb-2"><strong>Semester:</strong> <?= htmlspecialchars($student_data['sem'] ?? 'N/A') ?></div>
                    <div class="mb-2"><strong>Mobile No:</strong> <?= htmlspecialchars($student_data['mobile'] ?? 'N/A') ?></div>
                    <div class="mb-2"><strong>Receipt No:</strong> <?= htmlspecialchars($student_data['receipt_no'] ?? 'N/A') ?></div>
                </div>

                <div class="col-md-4 text-center">
                    <?php if ($student_data['photo']): ?>
                        <img src="<?= $student_data['photo'] ?>" alt="Student Photo" class="img-thumbnail" style="max-height: 150px;">
                    <?php else: ?>
                        <div class="border d-flex align-items-center justify-content-center" style="height: 150px;">
                            <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <p class="mt-2"><em>Authorized Signatory</em></p>
                </div>
            </div>

            <div class="mt-3 text-center">
                <small class="text-muted">This pass is property of GSSS IETW. Misuse will lead to cancellation.</small>
            </div>
        </div>

        <!-- Actions -->
        <div class="pass-actions mt-4 text-center">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="fas fa-print me-1"></i> Print Pass
            </button>
            <button class="btn btn-success">
                <i class="fas fa-download me-1"></i> Download PDF
            </button>
        </div>

    <?php elseif ($status == 2): ?>
        <div class="alert alert-danger">❌ Your application has been rejected. Please contact admin.</div>
    <?php else: ?>
        <div class="alert alert-warning">⏳ Waiting for Admin Approval. Please check back later.</div>
    <?php endif; ?>
</div>

----------------------$_COOKIE <div id="pass-section" class="dashboard-card">
            <div class="section-title">🎫 Bus Pass Status</div>
            <?php if ($status == 1): ?>
                <div class="alert alert-success">✅ Your Bus Pass has been approved!</div>
                
                <!-- Bus Pass Display -->
                <div class="bus-pass">
                    <div class="pass-header">
                        <h3>COLLEGE BUS PASS</h3>
                        <p>Valid for Academic Year 2023-2024</p>
                    </div>
                    <div class="pass-content">
                        <div class="pass-details">
                            <div class="pass-detail-item">
                                <strong>USN:</strong> <?= htmlspecialchars($student_data['usn']) ?>
                            </div>
                            <div class="pass-detail-item">
                                <strong>Name:</strong> <?= htmlspecialchars($student_data['name'] ?? 'N/A') ?>
                            </div>
                            <div class="pass-detail-item">
                                <strong>Route:</strong> <?= htmlspecialchars($student_data['route'] ?? 'To be assigned') ?>
                            </div>
                            <div class="pass-detail-item">
                                <strong>Valid Until:</strong> May 30, 2024
                            </div>
                        </div>
                        <div class="pass-photo">
                            <?php if ($student_data['photo']): ?>
                                <img src="<?= $student_data['photo'] ?>" alt="Student Photo">
                            <?php else: ?>
                                <div class="text-center d-flex align-items-center justify-content-center h-100">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="pass-footer">
                        <p>This pass is property of College Name. Misuse will lead to cancellation.</p>
                    </div>
                </div>
                
                <div class="pass-actions">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print me-2"></i> Print Pass
                    </button>
                    <button class="btn btn-success">
                        <i class="fas fa-download me-2"></i> Download PDF
                    </button>
                </div>
                
            <?php elseif ($status == 2): ?>
                <div class="alert alert-danger">❌ Your application has been rejected. Please contact admin.</div>
            <?php else: ?>
                <div class="alert alert-warning">⏳ Waiting for Admin Approval. Please check back later.</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Simple print functionality
        function printPass() {
            window.print();
        }
    </script>
</body>
</html> 

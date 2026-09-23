<?php
session_start();
include("config.php");

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Handle approval action
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE students SET pass_generated=1 WHERE id=$id");
    $_SESSION['approval_msg'] = "✅ Pass Approved for student ID: $id";
    header("Location: admin_dashboard.php");
    exit();
}

// Handle rejection action
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $conn->query("UPDATE students SET pass_generated=2 WHERE id=$id");
    $_SESSION['rejection_msg'] = "❌ Application Rejected for student ID: $id";
    header("Location: admin_dashboard.php");
    exit();
}

// Handle filter options
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT * FROM students";

switch($filter) {
    case 'approved':
        $query .= " WHERE pass_generated=1";
        break;
    case 'pending':
        $query .= " WHERE pass_generated=0";
        break;
    case 'rejected':
        $query .= " WHERE pass_generated=2";
        break;
}

$students = $conn->query($query);

// Get counts for dashboard stats
$total = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$approved = $conn->query("SELECT COUNT(*) as count FROM students WHERE pass_generated=1")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) as count FROM students WHERE pass_generated=0")->fetch_assoc()['count'];
$rejected = $conn->query("SELECT COUNT(*) as count FROM students WHERE pass_generated=2")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Bus Pass System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --danger: #e63946;
            --warning: #fca311;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            color: #495057;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 0;
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header h4 i {
            background: rgba(255,255,255,0.1);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-item {
            margin: 8px 15px;
            border-radius: 10px;
            overflow: hidden;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            padding: 12px 20px;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white !important;
            transform: translateX(5px);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .logout-btn {
            margin-top: 20px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 30px;
            padding-bottom: 60px;
            transition: all 0.3s;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .welcome-text h2 {
            color: var(--dark);
            font-weight: 700;
            margin: 0;
        }

        .welcome-text p {
            color: #6c757d;
            margin: 5px 0 0;
        }

        .date-section {
            background: white;
            padding: 10px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }

        .stat-title {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0 0;
        }

        .card-total .stat-icon {
            background: rgba(67, 97, 238, 0.15);
            color: var(--primary);
        }

        .card-pending .stat-icon {
            background: rgba(252, 163, 17, 0.15);
            color: var(--warning);
        }

        .card-approved .stat-icon {
            background: rgba(76, 201, 240, 0.15);
            color: var(--success);
        }

        .card-rejected .stat-icon {
            background: rgba(230, 57, 70, 0.15);
            color: var(--danger);
        }

        /* Content Box */
        .content-box {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .content-box-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .content-box-header h3 {
            margin: 0;
            font-weight: 700;
            color: var(--dark);
        }

        .filter-options {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid #dee2e6;
            background: white;
            color: #6c757d;
            transition: all 0.2s;
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
            padding: 0 10px;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table thead th {
            background-color: #f8f9fa;
            padding: 15px;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            text-align: left;
        }

        .custom-table tbody td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }

        .custom-table tbody tr {
            transition: background-color 0.2s;
        }

        .custom-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .student-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-approved {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Alert Styles */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            max-width: 350px;
        }

        .alert {
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert i {
            font-size: 20px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                bottom: 0;
                z-index: 1000;
                padding: 10px 0;
            }
            
            .sidebar-header {
                display: none;
            }
            
            .nav-items-container {
                display: flex;
                justify-content: space-around;
            }
            
            .nav-item {
                margin: 0 5px;
                flex: 1;
                text-align: center;
            }
            
            .nav-link {
                flex-direction: column;
                padding: 10px 5px;
                font-size: 12px;
                gap: 5px;
            }
            
            .nav-link span {
                display: none;
            }
            
            .nav-link i {
                font-size: 18px;
                margin-bottom: 5px;
            }
            
            .logout-btn {
                margin-top: 0;
                position: absolute;
                top: -50px;
                right: 20px;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .logout-btn span {
                display: none;
            }
            
            .main-content {
                padding: 20px;
                padding-bottom: 80px;
            }
            
            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h4><i class="fas fa-tools"></i> Admin Panel</h4>
            </div>
            <div class="nav-items-container">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-check-circle"></i>
                            <span>Approvals</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-bus"></i>
                            <span>Bus Routes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-users"></i>
                            <span>Students</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link logout-btn" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <div class="welcome-text">
                    <h2>Welcome Admin</h2>
                    <p>Manage student bus pass applications efficiently</p>
                </div>
                <div class="date-section">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?php echo date('l, F j, Y'); ?>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card card-total">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?php echo $total; ?></h3>
                        <p class="stat-title">Total Applications</p>
                    </div>
                </div>
                
                <div class="stat-card card-pending">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?php echo $pending; ?></h3>
                        <p class="stat-title">Pending Approval</p>
                    </div>
                </div>
                
                <div class="stat-card card-approved">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?php echo $approved; ?></h3>
                        <p class="stat-title">Approved Passes</p>
                    </div>
                </div>
                
                <div class="stat-card card-rejected">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?php echo $rejected; ?></h3>
                        <p class="stat-title">Rejected Applications</p>
                    </div>
                </div>
            </div>

            <!-- Student Applications Table -->
            <div class="content-box">
                <div class="content-box-header">
                    <h3><i class="fas fa-list-check me-2"></i>Student Applications</h3>
                    <div class="filter-options">
                        <a href="?filter=all" class="filter-btn <?php echo $filter == 'all' ? 'active' : ''; ?>">All</a>
                        <a href="?filter=pending" class="filter-btn <?php echo $filter == 'pending' ? 'active' : ''; ?>">Pending</a>
                        <a href="?filter=approved" class="filter-btn <?php echo $filter == 'approved' ? 'active' : ''; ?>">Approved</a>
                        <a href="?filter=rejected" class="filter-btn <?php echo $filter == 'rejected' ? 'active' : ''; ?>">Rejected</a>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>USN</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>Fee Receipt</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($s = $students->fetch_assoc()) { 
                                $status_class = '';
                                $status_text = '';
                                
                                if ($s['pass_generated'] == 1) {
                                    $status_class = 'badge-approved';
                                    $status_text = 'Approved';
                                } else if ($s['pass_generated'] == 2) {
                                    $status_class = 'badge-rejected';
                                    $status_text = 'Rejected';
                                } else {
                                    $status_class = 'badge-pending';
                                    $status_text = 'Pending';
                                }
                            ?>
                            <tr>
                                <td><?php echo $s['usn']; ?></td>
                                <td><?php echo isset($s['name']) ? $s['name'] : 'N/A'; ?></td>
                                <td>
                                    <?php if ($s['photo']): ?>
                                        <img src="<?php echo $s['photo']; ?>" alt="Student Photo" class="student-img">
                                    <?php else: ?>
                                        <div class="text-muted">No photo</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($s['fee_receipt']): ?>
                                        <a href="<?php echo $s['fee_receipt']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                    <?php else: ?>
                                        <div class="text-muted">Not uploaded</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($s['pass_generated'] == 0): ?>
                                            <a href="?approve=<?php echo $s['id']; ?>" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Approve
                                            </a>
                                            <a href="?reject=<?php echo $s['id']; ?>" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Reject
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Processed</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container for Notifications -->
    <div class="alert-container">
        <?php if (isset($_SESSION['approval_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <div><?php echo $_SESSION['approval_msg']; unset($_SESSION['approval_msg']); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['rejection_msg'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle"></i>
                <div><?php echo $_SESSION['rejection_msg']; unset($_SESSION['rejection_msg']); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
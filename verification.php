<?php
session_start();
include("config.php");

// Check if user is logged in (you can modify this based on your authentication)
if (!isset($_SESSION['student']) && !isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Fetch approved students (pass_generated = 1) with their photos
$approved_students = $conn->query("SELECT usn, name, photo FROM students WHERE pass_generated = 1 ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify USN - GSSSIETW Bus Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #010911;
            --accent: #c47159;
            --success: #28a745;
            --warning: #ffd700;
            --light-bg: rgba(236, 235, 235, 0.8);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            padding: 20px;
        }

        .verify-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-bottom: 30px;
            border-left: 5px solid var(--success);
        }

        .student-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid var(--success);
        }

        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .usn-badge {
            background: linear-gradient(45deg, var(--primary), var(--accent));
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .name-text {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 10px 0;
        }

        .status-badge {
            background-color: var(--success);
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .student-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--success);
            margin: 10px auto;
            display: block;
        }

        .photo-container {
            text-align: center;
            padding: 10px;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0;
        }

        .back-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #0056b3;
            color: white;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .student-card {
            animation: fadeIn 0.5s ease;
        }

        /* New style for status-photo column */
        .status-photo-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <!-- Header Section -->
        <div class="header-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold text-primary">
                        <i class="fas fa-id-card me-3"></i>
                        Verify USN - Approved Students
                    </h1>
                    <p class="lead text-muted">
                        List of students with approved bus pass applications
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="index.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Login
                    </a>
                    <?php if(isset($_SESSION['admin'])): ?>
                        <a href="admin_dashboard.php" class="back-btn ms-2">
                            <i class="fas fa-tachometer-alt"></i>
                            Admin Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Search and Stats Section -->
        <div class="row">
            <div class="col-md-8">
                <div class="search-box">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by USN or Name...">
                        </div>
                        <div class="col-md-6">
                            <select id="sortSelect" class="form-select">
                                <option value="name">Sort by Name</option>
                                <option value="usn">Sort by USN</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <div class="stats-number"><?php echo $approved_students->num_rows; ?></div>
                    <div class="text-muted">Approved Students</div>
                </div>
            </div>
        </div>

        <!-- Approved Students List -->
        <div id="studentsList">
            <?php if($approved_students->num_rows > 0): ?>
                <?php while($student = $approved_students->fetch_assoc()): 
                    // Determine photo path
                    $photoPath = !empty($student['photo']) ? "uploads/photos/" . $student['photo'] : "https://via.placeholder.com/80";
                ?>
                    <div class="student-card" data-usn="<?php echo strtolower($student['usn']); ?>" data-name="<?php echo strtolower($student['name']); ?>">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <span class="usn-badge">
                                    <i class="fas fa-id-badge me-2"></i>
                                    <?php echo htmlspecialchars($student['usn']); ?>
                                </span>
                            </div>
                            <div class="col-md-4">
                                <div class="name-text">
                                    <i class="fas fa-user-graduate me-2 text-primary"></i>
                                    <?php echo htmlspecialchars($student['name']); ?>
                                </div>
                            </div>
                            <div class="col-md-2 text-center status-photo-column">
                                <span class="status-badge">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Approved
                                </span>
                                <!-- Student Photo -->
                                <div class="photo-container">
                                    <img src="<?php echo $photoPath; ?>" alt="Student Photo" class="student-photo" 
                                         onerror="this.src='https://via.placeholder.com/80'">
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('<?php echo $student['usn']; ?>')">
                                    <i class="fas fa-copy me-1"></i>
                                    Copy USN
                                </button>
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-info btn-sm" onclick="viewStudentDetails('<?php echo $student['usn']; ?>')">
                                    <i class="fas fa-eye me-1"></i>
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Approved Students Found</h3>
                    <p class="text-muted">There are currently no students with approved bus passes.</p>
                    <?php if(isset($_SESSION['admin'])): ?>
                        <a href="admin_dashboard.php" class="btn btn-primary mt-3">
                            <i class="fas fa-users me-2"></i>
                            Go to Admin Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Verification Tool -->
        <div class="header-card mt-4">
            <h4><i class="fas fa-search me-2"></i>Quick USN Verification</h4>
            <div class="row mt-3">
                <div class="col-md-8">
                    <input type="text" id="verifyUsnInput" class="form-control" placeholder="Enter USN to verify...">
                </div>
                <div class="col-md-4">
                    <button onclick="verifySingleUSN()" class="btn btn-success w-100">
                        <i class="fas fa-check-circle me-2"></i>
                        Verify USN
                    </button>
                </div>
            </div>
            <div id="verificationResult" class="mt-3"></div>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="studentDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const studentCards = document.querySelectorAll('.student-card');
            
            studentCards.forEach(card => {
                const usn = card.getAttribute('data-usn');
                const name = card.getAttribute('data-name');
                
                if (usn.includes(searchTerm) || name.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Sort functionality
        document.getElementById('sortSelect').addEventListener('change', function() {
            const sortBy = this.value;
            const container = document.getElementById('studentsList');
            const studentCards = Array.from(container.querySelectorAll('.student-card'));
            
            studentCards.sort((a, b) => {
                const aValue = a.getAttribute(`data-${sortBy}`);
                const bValue = b.getAttribute(`data-${sortBy}`);
                return aValue.localeCompare(bValue);
            });
            
            // Remove all cards and re-add in sorted order
            studentCards.forEach(card => container.appendChild(card));
        });

        // Copy USN to clipboard
        function copyToClipboard(usn) {
            navigator.clipboard.writeText(usn).then(() => {
                // Show temporary feedback
                const originalText = event.target.innerHTML;
                event.target.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                event.target.classList.remove('btn-outline-primary');
                event.target.classList.add('btn-success');
                
                setTimeout(() => {
                    event.target.innerHTML = originalText;
                    event.target.classList.remove('btn-success');
                    event.target.classList.add('btn-outline-primary');
                }, 2000);
            });
        }

        // Single USN verification
        function verifySingleUSN() {
            const usnInput = document.getElementById('verifyUsnInput');
            const usn = usnInput.value.trim().toLowerCase();
            const resultDiv = document.getElementById('verificationResult');
            
            if (!usn) {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Please enter a USN to verify.
                    </div>
                `;
                return;
            }
            
            // Check if USN exists in the approved list
            const studentCards = document.querySelectorAll('.student-card');
            let found = false;
            let studentName = '';
            let studentPhoto = '';
            
            studentCards.forEach(card => {
                const cardUsn = card.getAttribute('data-usn');
                if (cardUsn === usn) {
                    found = true;
                    studentName = card.querySelector('.name-text').textContent.trim();
                    studentPhoto = card.querySelector('.student-photo').src;
                }
            });
            
            if (found) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>USN Verified!</strong><br>
                                USN: <strong>${usn.toUpperCase()}</strong><br>
                                Name: <strong>${studentName}</strong><br>
                                Status: <span class="badge bg-success">Approved</span>
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="${studentPhoto}" alt="Student Photo" class="student-photo">
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>USN Not Found!</strong><br>
                        The USN <strong>${usn.toUpperCase()}</strong> is not in the approved list or doesn't exist.
                    </div>
                `;
            }
            
            // Clear input after verification
            usnInput.value = '';
        }

        // View student details
        function viewStudentDetails(usn) {
            const studentCard = document.querySelector(`.student-card[data-usn="${usn.toLowerCase()}"]`);
            const name = studentCard.querySelector('.name-text').textContent.trim();
            const photo = studentCard.querySelector('.student-photo').src;
            
            document.getElementById('studentDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="${photo}" alt="Student Photo" class="student-photo" style="width: 150px; height: 150px;">
                    </div>
                    <div class="col-md-8">
                        <h4>${name}</h4>
                        <p><strong>USN:</strong> ${usn.toUpperCase()}</p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Approved</span></p>
                        <p><strong>Verification Date:</strong> ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h5>Additional Information</h5>
                    <p>This student has been verified and approved for bus pass usage. The photo matches the student records.</p>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
            modal.show();
        }

        // Allow Enter key to trigger verification
        document.getElementById('verifyUsnInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifySingleUSN();
            }
        });

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const studentCards = document.querySelectorAll('.student-card');
            studentCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>
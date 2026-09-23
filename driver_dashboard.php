<?php
session_start();
include("config.php");

if (!isset($_SESSION['driver'])) {
    header("Location: index.php");
    exit();
}

$driver = $_SESSION['driver'];

// Clear messages if requested
if (isset($_POST['clear_messages'])) {
    $conn->query("DELETE FROM messages");
    $clear_msg = "🧹 All messages cleared successfully.";
}

// Fetch students
$students = $conn->query("SELECT * FROM students");

// Fetch messages
$msgs = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard</title>
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

        .dashboard-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        h2, h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .message-box {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .logout-link {
            display: block;
            text-align: center;
            margin-top: 30px;
        }

        #voiceOutput {
            font-style: italic;
            font-size: 16px;
            margin-top: 10px;
            text-align: center;
            color: #555;
        }

        .btn-danger {
            width: 200px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>🚌 Driver Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="#">🏠 Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#">📋 Student List</a></li>
            <li class="nav-item"><a class="nav-link" href="#">📨 Messages</a></li>
            <li class="nav-item"><a class="nav-link" href="#">🎙️ Voice Notification</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">🔓 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h2>🚌 Welcome Driver: <?php echo $driver; ?></h2>

            <h3>📋 Student List</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>USN</th>
                            <th>Pass Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $students->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['usn']; ?></td>
                            <td>
                                <?php echo $row['pass_generated'] ? "<span class='badge bg-success'>Yes</span>" : "<span class='badge bg-secondary'>No</span>"; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-container">
            <h3>📨 Messages from Students</h3>

            <?php if (isset($clear_msg)): ?>
                <div class="alert alert-danger text-center"><?php echo $clear_msg; ?></div>
            <?php endif; ?>

            <?php if ($msgs->num_rows > 0): ?>
                <?php while($m = $msgs->fetch_assoc()) { ?>
                    <div class="message-box">
                        <strong><?php echo $m['message']; ?></strong>
                        <div class="text-muted small"><?php echo date("d M Y, h:i A", strtotime($m['created_at'])); ?></div>
                    </div>
                <?php } ?>
                <form method="POST" class="text-center mt-3">
                    <button type="submit" name="clear_messages" class="btn btn-danger">🧹 Clear All Messages</button>
                </form>
            <?php else: ?>
                <p class="text-center text-muted">No messages available.</p>
            <?php endif; ?>
        </div>

        <div class="dashboard-container text-center">
            <h3>🎙️ Voice Notification</h3>
            <button id="voiceBtn" class="btn btn-primary">🎧 Get Voice Command</button>
            <p id="voiceOutput"></p>
        </div>

        <a href="logout.php" class="btn btn-outline-danger logout-link">🔓 Logout</a>
    </div>

    <!-- Voice Recognition Script -->
    <script>
        const voiceBtn = document.getElementById('voiceBtn');
        const voiceOutput = document.getElementById('voiceOutput');

        voiceBtn.addEventListener('click', () => {
            if (!('webkitSpeechRecognition' in window)) {
                alert("Voice recognition not supported in this browser.");
                return;
            }

            const recognition = new webkitSpeechRecognition();
            recognition.lang = 'en-IN';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            voiceOutput.textContent = "🎙️ Listening...";

            recognition.start();

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                voiceOutput.textContent = `🗣️ Received: "${transcript}"`;
            };

            recognition.onerror = (event) => {
                voiceOutput.textContent = "❌ Error: " + event.error;
            };
        });
    </script>
</body>
</html>
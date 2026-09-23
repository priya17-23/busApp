<?php
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usn = $_POST['usn'];
    $name = $_POST['name'];
    $route_no = $_POST['route_no'];
    $stop_name = $_POST['stop_name'];
    $branch = $_POST['branch'];
    $sem = $_POST['sem'];
    $mobile = $_POST['mobile'];
    $receipt_no = $_POST['receipt_no'];

    // Handle photo upload
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        $photo_path = $target_dir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path);
    }

    // Insert into database
    $sql = "INSERT INTO bus_passes (usn, name, route_no, stop_name, branch, sem, mobile, receipt_no, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $usn, $name, $route_no, $stop_name, $branch, $sem, $mobile, $receipt_no, $photo_path);

    if ($stmt->execute()) {
        $success = "✅ Registration successful!";
    } else {
        $error = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Pass Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4 text-center">📝 Bus Pass Registration</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success text-center"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="border p-4 bg-white rounded shadow">
            <div class="mb-3">
                <label class="form-label">USN</label>
                <input type="text" name="usn" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Route No</label>
                <input type="text" name="route_no" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Stop Name</label>
                <input type="text" name="stop_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Branch</label>
                <input type="text" name="branch" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Semester</label>
                <input type="text" name="sem" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mobile No</label>
                <input type="text" name="mobile" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Receipt No</label>
                <input type="text" name="receipt_no" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Photo</label>
                <input type="file" name="photo" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</body>
</html>
<?php
include("config.php");

// Get route ID from URL
$routeId = $_GET['route'] ?? 1;

// Query route details
$sql = "SELECT * FROM routes WHERE id = $routeId";
$res = $conn->query($sql);
$route = $res->num_rows > 0 ? $res->fetch_assoc() : null;

// Construct image filename with fallbacks
$imageFile = 'images/default-route.jpg';
$candidates = [];

if ($route && !empty($route['bus_number'])) {
    $bus = $route['bus_number'];
    $candidates[] = "images/Route{$routeId}-{$bus}.jpg";
    $candidates[] = "images/Route{$routeId}-" . ltrim($bus, '0') . ".jpg";
    $candidates[] = "images/Route" . str_pad($routeId, 2, '0', STR_PAD_LEFT) . "-{$bus}.jpg";
    $candidates[] = "images/route{$routeId}-{$bus}.jpg";
    $candidates[] = "images/route-" . str_pad($routeId, 2, '0', STR_PAD_LEFT) . ".jpg";
    $candidates[] = "images/route{$routeId}.jpg";
    $candidates[] = "images/Route{$routeId}.jpg";
    $candidates[] = "images/Route{$routeId}-01.jpg";
} else {
    $candidates[] = "images/route-" . str_pad($routeId, 2, '0', STR_PAD_LEFT) . ".jpg";
    $candidates[] = "images/route{$routeId}.jpg";
}

// Handle Add Stop
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stop'])) {
    $newStop = trim($_POST['new_stop']);
    if ($newStop !== '') {
        $updatedStops = $route['stops'] . ', ' . $newStop;
        $conn->query("UPDATE routes SET stops='$updatedStops' WHERE id=$routeId");
        header("Location: route_details.php?route=$routeId");
        exit;
    }
}

// Handle Delete Stop
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_stop'])) {
    $stopToDelete = $_POST['delete_stop'];
    $stopsArray = array_filter(array_map('trim', explode(',', $route['stops'])), fn($s) => $s !== $stopToDelete);
    $updatedStops = implode(', ', $stopsArray);
    $conn->query("UPDATE routes SET stops='$updatedStops' WHERE id=$routeId");
    header("Location: route_details.php?route=$routeId");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stop'])) {
    $newStopsRaw = trim($_POST['new_stop']);
    if ($newStopsRaw !== '') {
        $newStopsArray = array_map('trim', explode(',', $newStopsRaw));
        $existingStops = array_map('trim', explode(',', $route['stops']));
        $mergedStops = array_merge($existingStops, $newStopsArray);
        $updatedStops = implode(', ', $mergedStops);
        $conn->query("UPDATE routes SET stops='$updatedStops' WHERE id=$routeId");
        header("Location: route_details.php?route=$routeId");
        exit;
    }
}

//DELEATE ALL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all_stops'])) {
    $conn->query("UPDATE routes SET stops='' WHERE id=$routeId");
    header("Location: route_details.php?route=$routeId");
    exit;
}

foreach ($candidates as $c) {
    if (file_exists(__DIR__ . '/' . $c)) {
        $imageFile = $c;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Route Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { color-scheme: light; }
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .route-box {
      width: 210mm;
      max-width: 100%;
      margin: 24px auto;
      background: white;
      padding: 12mm;
      border-radius: 6px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
      box-sizing: border-box;
    }

    .route-box h2 {
      color: #0400ff;
      margin-bottom: 12px;
      font-size: 1.25rem;
    }

    .route-box .label {
      font-weight: 600;
      color: #555;
    }

    .route-img {
      display: block;
      width: 100%;
      height: auto;
      max-height: calc(297mm - 40mm);
      object-fit: contain;
      border-radius: 6px;
      margin-bottom: 12px;
    }

    @page {
      size: A4 portrait;
      margin: 10mm;
    }
    @media print {
      body { background: #fff; }
      .route-box { box-shadow: none; border-radius: 0; padding: 8mm; width: auto; }
      .route-img { max-height: calc(297mm - 30mm); }
    }

    .top-bar {
      background: #fff;
      border-bottom: 1px solid #e6e6e6;
      padding: 8px 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .top-bar .container-top {
      display: flex;
      align-items: center;
      gap: 12px;
      max-width: 1140px;
      margin: 0 auto;
    }
    .top-logo { width: 120px; height: auto; border-radius: 4px; }
    .top-title h1 { margin: 0; font-size: 1rem; font-weight: 700; color: #222; }
    .top-title .muted { margin: 0; font-size: 0.85rem; color: #666; }

    .route-tabs {
      display: flex;
      gap: 6px;
      max-width: 1140px;
      margin: 8px auto 0;
      padding: 6px 12px;
      overflow: auto;
    }
    .route-tabs a {
      display: inline-block;
      padding: 6px 10px;
      background: #1f66a4;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      font-size: 0.9rem;
    }
    .route-tabs a:hover { background: #174f7b; }
    .route-tabs a.active { background: #f7d64a; color: #000; font-weight: 700; }

    @media (max-width:700px){
      .top-logo { width: 88px; }
      .top-title h1 { font-size: 0.95rem; }
      .route-tabs { gap: 4px; }
    }
  </style>
</head>
<body>

<header class="top-bar" role="banner">
  <div class="container-top">
    <img src="images/college_banner.png" alt="GSSS Banner" class="top-logo" onerror="this.onerror=null;this.src='images/default-banner.png'">
    <div class="top-title">
      <h1>GSSS Institute of Engineering and Technology for Women</h1>
      <p class="muted">Bus Routes — select a route below</p>
    </div>
  </div>

  

  <nav class="route-tabs" aria-label="Route navigation">
    <?php
      $maxRoutes = 17;
      for ($i = 1; $i <= $maxRoutes; $i++) {
        $num = str_pad($i, 2, '0', STR_PAD_LEFT);
        $isActive = ($i == intval($routeId)) ? 'active' : '';
        echo "<a href=\"?route={$i}\" class=\"{$isActive}\">{$num}</a>";
      }
    ?>
  </nav>
</header>

<div class="route-box">
  <h2>Route No: <?php echo str_pad($routeId, 2, '0', STR_PAD_LEFT); ?></h2>

  <?php if ($route): ?>
    <img src="<?php echo $imageFile; ?>"
         alt="Route <?php echo $routeId; ?>"
         class="img-fluid route-img"
         onerror="this.onerror=null;this.src='images/default-route.jpg';">

    <div class="mb-3">
      <label class="label">Search Stop:</label>
      <input type="text" id="stopSearch" class="form-control" placeholder="Type stop name...">
    </div>

    <div>
      <span class="label">Stops:</span>
      <ul id="stopList" class="list-group mt-2">
        <?php
          $stops = explode(',', $route['stops']);
          foreach ($stops as $stop) {
            echo '<li class="list-group-item stop-item">' . trim($stop) . '</li>';
          }
        ?>
      </ul>
    </div>

    <p class="mt-3"><span class="label">Timings:</span> <?php echo $route['timings']; ?></p>
    <p><span class="label">Driver:</span> <?php echo $route['driver_name'] ?? 'Not Assigned'; ?></p>
    <p><span class="label">Bus No:</span> <?php echo $route['bus_number'] ?? 'N/A'; ?></p>
  <?php else: ?>
    <div class="alert alert-warning">Route not found.</div>
  <?php endif; ?>

  <div style="margin-top:20px;">
    <a href="index.php" class="btn btn-secondary">Back to Home</a>
  </div>
</div>

<div class="mb-3">
  <label class="label">Search Stop:</label>
  <input type="text" id="stopSearch" class="form-control" placeholder="Type stop name...">
</div>

<div class="mb-3">
  <form method="POST" class="d-flex gap-2">
    <input type="text" name="new_stop" class="form-control" placeholder="Add new stop..." required>
    <button type="submit" name="add_stop" class="btn btn-success">Add Stop</button>

    

  </form>
</div>

<div>
  <span class="label">Stops:</span>
  <ul id="stopList" class="list-group mt-2">
    <form method="POST" class="mt-3">
  <button type="submit" name="delete_all_stops" class="btn btn-danger"
          onclick="return confirm('Are you sure you want to delete all stops for this route?');">
    Delete All Stops
  </button>
</form>
    <?php
      $stops = explode(',', $route['stops']);
      foreach ($stops as $stop) {
        $stop = trim($stop);
        echo '<li class="list-group-item stop-item d-flex justify-content-between align-items-center">'
           . $stop .
           '<form method="POST" style="margin:0;">
              <input type="hidden" name="delete_stop" value="' . $stop . '">
              <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
          </li>';
      }
    ?>
  </ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('stopSearch').addEventListener('input', function() {
  const query = this.value.toLowerCase();
  const items = document.querySelectorAll('.stop-item');
  items.forEach(item => {
    const text = item.textContent.toLowerCase();
    item.style.display = text.includes(query) ? 'block' : 'none';
  });
});
</script>

<script>
document.getElementById('stopSearch').addEventListener('input', function() {
  const query = this.value.toLowerCase();
  const items = document.querySelectorAll('.stop-item');
  items.forEach(item => {
    const text = item.textContent.toLowerCase();
    item.style.display = text.includes(query) ? 'flex' : 'none';
  });
});
</script>

</body>
</html>



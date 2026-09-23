   <?php
   // Get the data from the bus tracker
   $bus_id = $_POST['bus_id'];
   $lat = $_POST['lat'];
   $lng = $_POST['lng'];

   // Validate the data (optional)

   // Update MySQL
   $pdo = new PDO('mysql:host=localhost;dbname=bus_tracking', 'username', 'password');
   $stmt = $pdo->prepare('UPDATE buses SET latitude = ?, longitude = ?, last_updated = NOW() WHERE bus_id = ?');
   $stmt->execute([$lat, $lng, $bus_id]);

   // Update Redis
   $redis = new Redis();
   $redis->connect('redis_host', 6379);
   $redis->hSet('bus_locations', 'bus_'.$bus_id, json_encode(['lat' => $lat, 'lng' => $lng]));

   // Return success
   echo json_encode(['status' => 'success']);
   ?>
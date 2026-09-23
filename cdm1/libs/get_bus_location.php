   <?php
   // Set the content type to JSON
   header('Content-Type: application/json');

   // Set cache control for 1 second
   header('Cache-Control: max-age=1');

   // Connect to Redis
   $redis = new Redis();
   $redis->connect('redis_host', 6379);

   // Get all bus locations from Redis
   $bus_locations = $redis->hGetAll('bus_locations');

   // If Redis hash is empty, then get from MySQL and update Redis
   if (empty($bus_locations)) {
       $pdo = new PDO('mysql:host=localhost;dbname=bus_tracking', 'username', 'password');
       $stmt = $pdo->query('SELECT bus_id, latitude, longitude FROM buses');
       $buses = $stmt->fetchAll(PDO::FETCH_ASSOC);

       // Build the bus_locations array and update Redis
       $bus_locations = [];
       foreach ($buses as $bus) {
           $key = 'bus_'.$bus['bus_id'];
           $value = json_encode(['lat' => $bus['latitude'], 'lng' => $bus['longitude']]);
           $bus_locations[$key] = $value;
           $redis->hSet('bus_locations', $key, $value);
       }
   } else {
       // Convert the Redis hash to the same format as the MySQL one
       // The Redis hash has keys like 'bus_1' and values as JSON strings.
       // We want to convert it to an array of bus_id and lat/lng.
       // But note: the frontend expects a list of buses with bus_id and lat/lng.
       // We can return the same structure as the MySQL one.

       $result = [];
       foreach ($bus_locations as $bus_key => $bus_data) {
           // Extract bus_id from the key (e.g., 'bus_1' -> 1)
           $bus_id = substr($bus_key, 4);
           $data = json_decode($bus_data, true);
           $result[] = [
               'bus_id' => $bus_id,
               'latitude' => $data['lat'],
               'longitude' => $data['lng']
           ];
       }
       $bus_locations = $result;
   }

   // Return the bus locations as JSON
   echo json_encode($bus_locations);
   ?>
<!DOCTYPE html>
   <html>
   <head>
       <title>Bus Tracking</title>
       <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
       <style>
           #map { height: 500px; }
       </style>
   </head>
   <body>
       <div id="map"></div>

       <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
       <script>
           // Initialize the map
           var map = L.map('map').setView([0, 0], 13);

           // Add a tile layer (OpenStreetMap)
           L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '© OpenStreetMap contributors'
           }).addTo(map);

           // Object to hold bus markers
           var busMarkers = {};

           // Function to update bus locations
           function updateBusLocations() {
               fetch('get_bus_locations.php')
                   .then(response => response.json())
                   .then(buses => {
                       // For each bus in the response
                       buses.forEach(bus => {
                           var busId = bus.bus_id;
                           var lat = bus.latitude;
                           var lng = bus.longitude;

                           // If the bus marker already exists, update its position
                           if (busMarkers[busId]) {
                               busMarkers[busId].setLatLng([lat, lng]);
                           } else {
                               // Create a new marker and add it to the map
                               var marker = L.marker([lat, lng]).addTo(map);
                               // Optionally, bind a popup to the marker
                               marker.bindPopup('Bus ' + busId);
                               busMarkers[busId] = marker;
                           }
                       });
                   })
                   .catch(error => console.error('Error fetching bus locations:', error));
           }

           // Update bus locations every 5 seconds
           setInterval(updateBusLocations, 5000);

           // Initial update
           updateBusLocations();
       </script>
   </body>
   </html>
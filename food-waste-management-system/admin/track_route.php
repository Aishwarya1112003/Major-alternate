<?php
require_once('api_config.php'); // Include the API key configuration

// Retrieve destination from the GET parameters
$destination = isset($_GET['destination']) ? urldecode($_GET['destination']) : '';

if (!$destination) {
    die("Destination is required.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Real-Time Tracking</title>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap">
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #container {
            display: flex;
            width: 100%;
            max-width: 1200px;
        }
        #map {
            height: 500px;
            width: 70%;
        }
        #info-panel {
            width: 30%;
            display: flex;
            flex-direction: column;
            padding-left: 20px;
        }
        #info, #directions-panel {
            font-size: 14px;
            margin-bottom: 20px;
        }
        #directions-panel {
            height: 500px;
            overflow: auto;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            background: #f9f9f9;
        }
    </style>
    <script>
        let map, marker, directionsService, directionsRenderer;
        let currentPos;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: { lat: 19.0760, lng: 72.8777 }, // Default center
            });

            // Enable traffic layer
            const trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(map);

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                draggable: false,
                panel: document.getElementById("directions-panel"),
            });

            directionsRenderer.setMap(map);

            const destination = <?php echo json_encode($destination); ?>;
            trackLocation(destination);
        }

        function trackLocation(destination) {
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(
                    (position) => {
                        currentPos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        if (!marker) {
                            marker = new google.maps.Marker({
                                position: currentPos,
                                map: map,
                                title: "Your Location",
                                icon: {
                                    url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                                }
                            });
                        } else {
                            marker.setPosition(currentPos);
                        }

                        map.setCenter(currentPos);

                        // Request route to the destination
                        calculateAndDisplayRoute(currentPos, destination);
                    },
                    (error) => {
                        console.error("Error getting location: ", error);
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 0
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function calculateAndDisplayRoute(origin, destination) {
            directionsService.route(
                {
                    origin: origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsRenderer.setDirections(response);

                        const route = response.routes[0].legs[0];
                        const travelTime = route.duration.text;
                        const distance = route.distance.text;

                        document.getElementById("info").innerHTML = `
                            <p><strong>Origin:</strong> ${origin.lat}, ${origin.lng}</p>
                            <p><strong>Destination:</strong> ${destination}</p>
                            <p><strong>Total Distance:</strong> ${distance}</p>
                            <p><strong>Total Time:</strong> ${travelTime}</p>
                        `;
                    } else {
                        console.error("Directions request failed: " + status);
                    }
                }
            );
        }

        window.initMap = initMap;
    </script>
</head>
<body>
    <h1>Real-Time Tracking with Traffic</h1>
    <div id="container">
        <div id="map"></div>
        <div id="info-panel">
            <div id="info"></div>
            <div id="directions-panel"></div>
        </div>
    </div>
</body>
</html>

<?php
// Get the addresses from the GET parameters
$donation_address = isset($_GET['donation_address']) ? urldecode($_GET['donation_address']) : '';
$delivery_address = isset($_GET['delivery_address']) ? urldecode($_GET['delivery_address']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Map View with Current Location</title>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCm01PKDHEWuNKixfTgIRsVaQYLZCLAJWM&callback=initMap">
    </script>
    <style>
        #info-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        #info, #current-location {
            flex: 1;
        }

        #buttons {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        #buttons button {
            background-color: greenyellow;
            color: black;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }

        #buttons button:hover {
            background-color:yellow;
        }
    </style>
    <script>
        let currentLocationMarker;

        // Initialize the map
        function initMap() {
            const donationAddress = <?php echo json_encode($donation_address); ?>;
            const deliveryAddress = <?php echo json_encode($delivery_address); ?>;

            if (!donationAddress || !deliveryAddress) {
                alert("Both donation and delivery addresses are required.");
                return;
            }

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: { lat: 19.0760, lng: 72.8777 }, // Default center (Mumbai)
            });

            const geocoder = new google.maps.Geocoder();
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            // Fetch and show donation and delivery route
            directionsService.route(
                {
                    origin: donationAddress,
                    destination: deliveryAddress,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsRenderer.setDirections(response);

                        // Extract total distance and duration
                        const route = response.routes[0].legs[0];
                        const travelTime = route.duration.text;
                        const distance = route.distance.text;

                        // Update the #info section
                        document.getElementById("info").innerHTML = `
                            <p><strong>Donation Address:</strong> ${donationAddress}</p>
                            <p><strong>Delivery Address:</strong> ${deliveryAddress}</p>
                            <p><strong>Total Distance:</strong> ${distance}</p>
                            <p><strong>Total Time:</strong> ${travelTime}</p>
                        `;
                    } else {
                        alert("Directions request failed: " + status);
                    }
                }
            );

            // Add the current location marker
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const currentLatLng = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        currentLocationMarker = new google.maps.Marker({
                            position: currentLatLng,
                            map: map,
                            label: "C", // Label "C" for Current Location
                            title: "Your Current Location",
                        });

                        map.setCenter(currentLatLng);

                        // Add info for current location
                        document.getElementById("current-location").innerHTML = `
                            <p><strong>Current Location:</strong> 
                            Latitude: ${currentLatLng.lat}, Longitude: ${currentLatLng.lng}</p>
                        `;
                    },
                    (error) => {
                        alert("Error fetching current location: " + error.message);
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Handle button redirection
        function redirectToRoute(routeType) {
            let url = "";

            if (routeType === "current_to_donation") {
                if (!navigator.geolocation) {
                    alert("Geolocation is not supported by your browser.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const currentLat = position.coords.latitude;
                        const currentLng = position.coords.longitude;

                        url = `track_route.php?origin=${currentLat},${currentLng}&destination=<?php echo urlencode($donation_address); ?>`;
                        window.location.href = url; // Redirect
                    },
                    (error) => {
                        alert("Error fetching current location: " + error.message);
                    }
                );
            } else if (routeType === "donation_to_delivery") {
                url = `track_route.php?origin=<?php echo urlencode($donation_address); ?>&destination=<?php echo urlencode($delivery_address); ?>`;
                window.location.href = url; // Redirect
            }
        }

        // Ensure initMap is globally accessible
        window.initMap = initMap;
    </script>
</head>
<body>
    <h1>Route from Donation to Delivery</h1>
    <div id="info-container">
        <div id="info"></div>
        <div id="current-location" style="margin-top: 10px;"></div>
        <div id="buttons">
            <button onclick="redirectToRoute('current_to_donation')">
                Current Location to Donation Address
            </button>
            <button onclick="redirectToRoute('donation_to_delivery')">
                Donation Address to Delivery Address
            </button>
        </div>
    </div>
    <div id="map" style="height: 500px; width: 100%;"></div>
</body>
</html>

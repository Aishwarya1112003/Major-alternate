<?php
require_once('api_config.php'); // Include the API key configuration

// Get the addresses from the GET parameters
$donation_address = isset($_GET['donation_address']) ? urldecode($_GET['donation_address']) : '';
$delivery_address = isset($_GET['delivery_address']) ? urldecode($_GET['delivery_address']) : '';
$order_id = isset($_GET['order_id']) ? urldecode($_GET['order_id']) : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Map View with Current Location</title>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap">
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
            background-color: yellow;
        }
    </style>
    <script>
        let currentLocationMarker;

        function initMap() {
            const donationAddress = <?php echo json_encode($donation_address); ?>;
            const deliveryAddress = <?php echo json_encode($delivery_address); ?>;

            if (!donationAddress || !deliveryAddress) {
                alert("Both donation and delivery addresses are required.");
                return;
            }

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: { lat: 19.0760, lng: 72.8777 },
            });

            const geocoder = new google.maps.Geocoder();
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            directionsService.route(
                {
                    origin: donationAddress,
                    destination: deliveryAddress,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsRenderer.setDirections(response);

                        const route = response.routes[0].legs[0];
                        const travelTime = route.duration.text;
                        const distance = route.distance.text;

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
                            label: "C",
                            title: "Your Current Location",
                        });

                        map.setCenter(currentLatLng);

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
                        window.location.href = url;
                    },
                    (error) => {
                        alert("Error fetching current location: " + error.message);
                    }
                );
            } else if (routeType === "donation_to_delivery") {
                url = `track_route.php?origin=<?php echo urlencode($donation_address); ?>&destination=<?php echo urlencode($delivery_address); ?>`;
                window.location.href = url;
            }
        }

        function pickupToday() {
    const donationId = <?php echo json_encode($order_id); ?>;

    if (!donationId || donationId === "null" || donationId === "") {
        alert("Donation ID is missing.");
        return;
    }

    // Make the Pickup Today button invisible after clicking
    // We won't use localStorage here anymore

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "pickup.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                // Log the response for debugging
                console.log(xhr.responseText);
                // Reload the page or redirect to update the button visibility based on status
                window.location.href = "admin.php?order_id=" + donationId;
            } else {
                alert("Failed to update the donation status. Please try again.");
            }
        }
    };

    // Send the donation ID to update the donation status
    xhr.send("order_id=" + encodeURIComponent(donationId));
}

function markAsDelivered() {
    const donationId = <?php echo json_encode($order_id); ?>;

    if (!donationId || donationId === "null" || donationId === "") {
        alert("Donation ID is missing.");
        return;
    }

    // Make the Delivered button invisible after clicking
    // We won't use localStorage here either

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "delivered.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                // Log the response for debugging
                console.log(xhr.responseText);
                // Redirect to admin.php after marking as delivered
                window.location.href = "admin.php?order_id=" + donationId;
            } else {
                alert("Failed to update the donation status. Please try again.");
            }
        }
    };

    // Send the donation ID to delivered.php to mark as delivered
    xhr.send("order_id=" + encodeURIComponent(donationId));
}

        // console.log("JavaScript is loaded!");
        function cancelOrder() {
    const donationId = <?php echo json_encode($order_id); ?>;

    if (!donationId || donationId === "null" || donationId === "") {
        alert("Donation ID is missing.");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "cancel_donation.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                // Log the response from cancel_donation.php for debugging
                console.log(xhr.responseText); // Log the response

                // Redirect to admin.php after successful cancellation
                window.location.href = "admin.php";
            } else {
                // If the request failed, show an alert
                alert("Failed to cancel the order. Please try again.");
            }
        }
    };

    // Send the donation ID to cancel_donation.php
    xhr.send("order_id=" + encodeURIComponent(donationId));
}


            
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("cancelOrderBtn").addEventListener("click", cancelOrder);
});


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
            <button id="cancelOrderBtn" style="background-color: red; color: white;">
                Cancel Order
            </button>
            <button id="pickupTodayBtn" style="background-color: yellow; color: black;" onclick="pickupToday()">
                Pickup Today
            </button>
            <button id="deliveredBtn" style="background-color: green; color: white;" onclick="markAsDelivered()" style="display:none;">
                Delivered the Food
            </button>
        </div>
    </div>
    <div id="map" style="height: 500px; width: 100%;"></div>
</body>
</html>

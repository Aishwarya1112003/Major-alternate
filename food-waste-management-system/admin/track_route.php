<?php
$origin = isset($_GET['origin']) ? urldecode($_GET['origin']) : '';
$destination = isset($_GET['destination']) ? urldecode($_GET['destination']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Step-by-Step Navigation</title>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCm01PKDHEWuNKixfTgIRsVaQYLZCLAJWM&callback=initMap&libraries=geometry">
    </script>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }

        #info, #turn-by-turn {
            margin-top: 20px;
            font-size: 16px;
        }

        #start-btn {
            margin: 20px 0;
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        #start-btn:hover {
            background-color: darkgreen;
        }
    </style>
    <script>
        let map, directionsService, directionsRenderer, watchID;
        let originMarker, destinationMarker, currentLocationMarker;
        let stepIndex = 0, routeSteps = [];

        function initMap() {
            const origin = <?php echo json_encode($origin); ?>;
            const destination = <?php echo json_encode($destination); ?>;

            if (!origin || !destination) {
                alert("Both origin and destination are required.");
                return;
            }

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: { lat: 19.0760, lng: 72.8777 }, // Default center (Mumbai)
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({ suppressMarkers: true });
            directionsRenderer.setMap(map);

            document.getElementById("start-btn").addEventListener("click", () => {
                startStepByStepNavigation(origin, destination);
            });

            // Place origin and destination markers
            placeMarkers(origin, destination);
        }

        function placeMarkers(origin, destination) {
            const geocoder = new google.maps.Geocoder();

            // Geocode and place origin marker
            geocoder.geocode({ address: origin }, (results, status) => {
                if (status === "OK") {
                    originMarker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        label: "O",
                        title: "Origin",
                    });
                } else {
                    alert("Geocoding origin failed: " + status);
                }
            });

            // Geocode and place destination marker
            geocoder.geocode({ address: destination }, (results, status) => {
                if (status === "OK") {
                    destinationMarker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        label: "D",
                        title: "Destination",
                    });
                } else {
                    alert("Geocoding destination failed: " + status);
                }
            });
        }

        function startStepByStepNavigation(origin, destination) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const currentLatLng = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        if (!currentLocationMarker) {
                            currentLocationMarker = new google.maps.Marker({
                                position: currentLatLng,
                                map: map,
                                icon: "https://maps.google.com/mapfiles/ms/icons/man.png",
                                title: "Your Current Location",
                            });
                        }

                        calculateRoute(currentLatLng, destination);
                    },
                    (error) => {
                        alert("Error fetching location: " + error.message);
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function calculateRoute(origin, destination) {
            directionsService.route(
                {
                    origin: origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsRenderer.setDirections(response);

                        // Extract steps
                        routeSteps = response.routes[0].legs[0].steps;
                        displayCurrentStep();

                        // Start live tracking
                        startLiveTracking();
                    } else {
                        alert("Directions request failed: " + status);
                    }
                }
            );
        }

        function displayCurrentStep() {
            if (stepIndex < routeSteps.length) {
                const step = routeSteps[stepIndex];
                document.getElementById("turn-by-turn").innerHTML = `
                    <p><strong>Next Turn:</strong> ${step.instructions}</p>
                    <p><strong>Distance:</strong> ${step.distance.text}</p>
                    <p><strong>Duration:</strong> ${step.duration.text}</p>
                `;
            } else {
                document.getElementById("turn-by-turn").innerHTML = `
                    <p><strong>Navigation Complete!</strong> You have reached your destination.</p>
                `;
                stopLiveTracking();
            }
        }

        function startLiveTracking() {
            watchID = navigator.geolocation.watchPosition(
                (position) => {
                    const currentLatLng = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    currentLocationMarker.setPosition(currentLatLng);
                    map.setCenter(currentLatLng);

                    // Check proximity to the next step
                    const step = routeSteps[stepIndex];
                    const stepLatLng = step.end_location;

                    const distanceToStep = google.maps.geometry.spherical.computeDistanceBetween(
                        new google.maps.LatLng(currentLatLng),
                        new google.maps.LatLng(stepLatLng)
                    );

                    if (distanceToStep < 50) { // Move to the next step if close enough
                        stepIndex++;
                        displayCurrentStep();
                    }
                },
                (error) => {
                    alert("Error tracking location: " + error.message);
                },
                { enableHighAccuracy: true }
            );
        }

        function stopLiveTracking() {
            if (watchID) {
                navigator.geolocation.clearWatch(watchID);
            }
        }

        // Ensure initMap is globally accessible
        window.initMap = initMap;
    </script>
</head>
<body>
    <h1>Step-by-Step Navigation</h1>
    <button id="start-btn">Start Journey</button>
    <div id="turn-by-turn"></div>
    <div id="map"></div>
</body>
</html>

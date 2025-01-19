<?php
session_start(); // Start the session

// Check if donation address exists in the GET parameters
$donation_address = isset($_GET['donation_address']) ? urldecode($_GET['donation_address']) : '';
$delivery_address = isset($_GET['delivery_address']) ? urldecode($_GET['delivery_address']) : ''; // Retrieve delivery address from query string
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Map</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMIEYU6LsR_z2npJN1GMIMUH40Pc_CT_c"></script>
    <script>
        function initMap() {
            var geocoder = new google.maps.Geocoder();

            // Geocode donation address
            geocoder.geocode({ address: "<?php echo $donation_address; ?>" }, function (results, status) {
                if (status === "OK") {
                    var donationLocation = results[0].geometry.location;

                    // Geocode delivery address
                    geocoder.geocode({ address: "<?php echo $delivery_address; ?>" }, function (results, status) {
                        if (status === "OK") {
                            var deliveryLocation = results[0].geometry.location;

                            // Display map with route
                            var map = new google.maps.Map(document.getElementById("map"), {
                                zoom: 12,
                                center: donationLocation,
                            });

                            var directionsService = new google.maps.DirectionsService();
                            var directionsRenderer = new google.maps.DirectionsRenderer();
                            directionsRenderer.setMap(map);

                            var request = {
                                origin: donationLocation,
                                destination: deliveryLocation,
                                travelMode: 'DRIVING',
                            };

                            directionsService.route(request, function (result, status) {
                                if (status == "OK") {
                                    directionsRenderer.setDirections(result);
                                }
                            });
                        }
                    });
                }
            });
        }
    </script>
</head>
<body onload="initMap()">
    <div id="map" style="height: 500px; width: 100%;"></div>
</body>
</html>

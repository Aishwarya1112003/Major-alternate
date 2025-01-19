<?php
include("connect.php"); // Include database connection

session_start(); // Start session if using session variables

if (isset($_POST['food'])) {
    $order_id = $_POST['order_id'];
    $delivery_person_id = $_POST['delivery_person_id'];

    // Fetch the donation address from the `food_donations` table
    $donation_query = "SELECT address FROM food_donations WHERE Fid = $order_id";
    $donation_result = mysqli_query($connection, $donation_query);
    $donation_row = mysqli_fetch_assoc($donation_result);
    $donation_address = $donation_row['address'];

    // Fetch the delivery address from the `admin` table using session email
    $admin_email = $_SESSION['email']; // Fetch the email of the logged-in admin
    $admin_query = "SELECT address FROM admin WHERE email = '$admin_email' LIMIT 1";
    $admin_result = mysqli_query($connection, $admin_query);
    $admin_row = mysqli_fetch_assoc($admin_result);
    $delivery_address = $admin_row['address'];

    // Update the database to assign the order to the delivery person
    $update_query = "UPDATE food_donations SET assigned_to = $delivery_person_id WHERE Fid = $order_id";
    $update_result = mysqli_query($connection, $update_query);

    if ($update_result) {
        // Redirect to the map page with both addresses as query parameters
        header("Location: map_page.php?donation_address=" . urlencode($donation_address) . "&delivery_address=" . urlencode($delivery_address));
        exit();
    } else {
        echo "Error updating database: " . mysqli_error($connection);
    }
}
?>

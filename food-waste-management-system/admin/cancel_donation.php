<?php
require_once('api_config.php'); // Include the API key configuration
include('connect.php'); // Database connection

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Debugging: check if the order_id is being passed correctly
    echo "Received order_id: " . htmlspecialchars($order_id);
    
    // Check if order_id is valid and not empty
    if (!empty($order_id)) {
        // Update the donation status to 'canceled' in the database
        $query = "UPDATE food_donations SET donation_status='canceled' WHERE fid = $order_id";
        // Execute the query
        $result = mysqli_query($connection, $query);
        

        // Check if the query was successful
        if ($result) {
            // Successfully canceled, redirect back or show a success message
            header("Location: admin.php"); // Redirect back to your page (change the URL as needed)
            exit;
        } else {
            // Show an error message if the deletion fails
            echo "Error updating record: " . mysqli_error($connection);
        }
    }
    else {
        echo "Error: Cancel button was not clicked.";
    }
}
?>

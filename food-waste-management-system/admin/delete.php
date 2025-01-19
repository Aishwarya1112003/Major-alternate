<?php
// Include database connection
include('connect.php');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the cancel button is clicked
if (isset($_POST['cancel'])) {
    // Check if the required POST and SESSION variables are set
    if (isset($_POST['order_id']) && isset($_SESSION['admin_id'])) {
        $donation_id = $_POST['order_id']; // Get the donation ID from the form
        $admin_id = $_SESSION['admin_id']; // Get the logged-in admin ID (stored in session)

        // Update the status of the donation to 'canceled'
        $query = "UPDATE food_donations SET status = 'canceled', assigned_to = $admin_id WHERE Fid = $donation_id";

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
    } else {
        // Display a meaningful error if required data is missing
        echo "Error: Missing order_id or admin_id. Please try again.";
    }
} else {
    echo "Error: Cancel button was not clicked.";
}
?>
